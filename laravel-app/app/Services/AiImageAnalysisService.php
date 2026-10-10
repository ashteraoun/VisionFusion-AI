<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiImageAnalysisService
{
    /**
     * @return array{
     *     success: bool,
     *     filename: string,
     *     format: string,
     *     width: int,
     *     height: int,
     *     file_size_bytes: int,
     *     message: string
     * }
     */
    public function analyze(UploadedFile $image): array
    {
        try {
            $response = Http::acceptJson()
                ->connectTimeout(config('services.ai.connect_timeout'))
                ->timeout(config('services.ai.timeout'))
                ->attach(
                    'file',
                    $image->get(),
                    $image->getClientOriginalName(),
                    ['Content-Type' => $image->getMimeType() ?? 'application/octet-stream'],
                )
                ->post(
                    rtrim(config('services.ai.url'), '/').'/'.ltrim(config('services.ai.analyze_path'), '/'),
                );
        } catch (ConnectionException $exception) {
            Log::warning('The AI image analysis service could not be reached.');

            throw new AiServiceException(
                'Image analysis is temporarily unavailable. Please try again shortly.',
                503,
                $exception,
            );
        }

        if (! $response->successful()) {
            Log::warning('The AI image analysis service rejected a request.', [
                'status' => $response->status(),
            ]);

            throw new AiServiceException(
                'The image could not be analyzed. Please check the file and try again.',
                502,
            );
        }

        $payload = $response->json();

        if (! $this->isValidResponse($payload)) {
            Log::warning('The AI image analysis service returned an invalid response.');

            throw new AiServiceException(
                'The image analysis service returned an unexpected response. Please try again.',
            );
        }

        return $payload;
    }

    private function isValidResponse(mixed $payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        foreach (['success', 'filename', 'format', 'width', 'height', 'file_size_bytes', 'message'] as $key) {
            if (! array_key_exists($key, $payload)) {
                return false;
            }
        }

        return $payload['success'] === true
            && is_string($payload['filename'])
            && is_string($payload['format'])
            && is_int($payload['width'])
            && is_int($payload['height'])
            && is_int($payload['file_size_bytes'])
            && is_string($payload['message']);
    }
}
