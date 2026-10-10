<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImageAnalysisTest extends TestCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAIAAAADCAIAAAA2iEnWAAAAFElEQVR4nGPUiFrAwMDAxAAGUAoAFWIBKMtzSaAAAAAASUVORK5CYII=';

    private const JPEG = '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAADAAIDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaWpxc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldZWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3P9oADAMBAAIRAxEAPwDkqKKK+mPDP//Z';

    public function test_upload_page_shows_the_image_upload_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Upload your image')
            ->assertSee('name="_token"', false)
            ->assertSee('JPG, JPEG, PNG or WEBP');
    }

    public function test_valid_jpeg_is_forwarded_to_the_ai_service_and_result_is_shown(): void
    {
        Http::fake([
            '*' => Http::response($this->analysisResult('sample.jpg', 'JPEG'), 200),
        ]);

        $response = $this->postJson(route('images.analyze'), [
            'image' => $this->image('sample.jpg', self::JPEG),
        ]);

        $response->assertOk()
            ->assertJsonPath('filename', 'sample.jpg')
            ->assertJsonPath('format', 'JPEG')
            ->assertJsonPath('width', 2)
            ->assertJsonPath('height', 3);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'http://127.0.0.1:8000/api/v1/analyze-image'
            && $request->hasFile('file'));
    }

    public function test_valid_png_result_is_rendered_after_form_submission(): void
    {
        Http::fake([
            '*' => Http::response($this->analysisResult('sample.png', 'PNG'), 200),
        ]);

        $this->post(route('images.analyze'), [
            'image' => $this->image('sample.png', self::PNG),
        ])->assertRedirect(route('images.index'))
            ->assertSessionHas('analysis.filename', 'sample.png');

        $this->get('/')
            ->assertOk()
            ->assertSee('Image details')
            ->assertSee('sample.png')
            ->assertSee('3 × 2 px');
    }

    public function test_rejects_unsupported_files_before_calling_the_ai_service(): void
    {
        Http::fake();

        $this->postJson(route('images.analyze'), [
            'image' => UploadedFile::fake()->createWithContent('document.txt', 'plain text'),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        Http::assertNothingSent();
    }

    public function test_rejects_corrupted_images_before_calling_the_ai_service(): void
    {
        Http::fake();

        $this->postJson(route('images.analyze'), [
            'image' => UploadedFile::fake()->createWithContent('corrupt.jpg', 'not a readable image'),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        Http::assertNothingSent();
    }

    public function test_rejects_images_over_the_configured_limit(): void
    {
        Http::fake();

        $tooLarge = str_repeat('x', config('image_upload.max_size_kb') * 1024 + 1);
        $this->postJson(route('images.analyze'), [
            'image' => UploadedFile::fake()->createWithContent('large.jpg', $tooLarge),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        Http::assertNothingSent();
    }

    public function test_returns_a_friendly_error_when_the_ai_service_is_unavailable(): void
    {
        Http::fake([
            '*' => function (): never {
                throw new ConnectionException('connection refused');
            },
        ]);

        $this->postJson(route('images.analyze'), [
            'image' => $this->image('sample.jpg', self::JPEG),
        ])->assertStatus(503)
            ->assertJsonPath('message', 'Image analysis is temporarily unavailable. Please try again shortly.');
    }

    public function test_rejects_an_invalid_ai_service_response(): void
    {
        Http::fake([
            '*' => Http::response(['unexpected' => true], 200),
        ]);

        $this->postJson(route('images.analyze'), [
            'image' => $this->image('sample.jpg', self::JPEG),
        ])->assertStatus(502)
            ->assertJsonPath('message', 'The image analysis service returned an unexpected response. Please try again.');
    }

    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    /**
     * @param  'JPEG'|'PNG'  $format
     * @return array<string, bool|int|string>
     */
    private function analysisResult(string $filename, string $format): array
    {
        return [
            'success' => true,
            'filename' => $filename,
            'format' => $format,
            'width' => 2,
            'height' => 3,
            'file_size_bytes' => 64,
            'message' => 'Image validated successfully. Image analysis is not enabled yet.',
        ];
    }

    private function image(string $filename, string $base64): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $filename,
            base64_decode($base64, strict: true),
        );
    }
}
