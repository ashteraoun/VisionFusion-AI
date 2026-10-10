<?php

namespace App\Http\Controllers;

use App\Services\AiImageAnalysisService;
use App\Services\AiServiceException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class ImageAnalysisController extends Controller
{
    public function index(): View
    {
        return view('images.upload');
    }

    public function analyze(Request $request, AiImageAnalysisService $aiService): JsonResponse|RedirectResponse
    {
        $maxSizeKb = config('image_upload.max_size_kb');
        $extensions = implode(',', config('image_upload.allowed_extensions'));

        $validated = $request->validate([
            'image' => [
                'required',
                File::image()->types(config('image_upload.allowed_extensions'))->max($maxSizeKb),
                'extensions:'.$extensions,
            ],
        ], [
            'image.required' => 'Choose an image to analyze.',
            'image.image' => 'The selected file must be a valid image.',
            'image.mimes' => 'Choose a JPG, JPEG, PNG, or WEBP image.',
            'image.extensions' => 'Choose a JPG, JPEG, PNG, or WEBP image.',
            'image.max' => 'The image must not be larger than '.config('image_upload.max_size_mb').' MB.',
        ]);

        try {
            $analysis = $aiService->analyze($validated['image']);
        } catch (AiServiceException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], $exception->httpStatus);
            }

            return back()->withErrors(['image' => $exception->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json($analysis);
        }

        return to_route('images.index')->with('analysis', $analysis);
    }

    public function health(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }
}
