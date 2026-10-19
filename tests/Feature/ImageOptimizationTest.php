<?php

namespace Tests\Feature;

use App\Models\Dentist;
use App\Services\ImageOptimizerService;
use Filament\Forms\Components\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Tests\TestCase;

class ImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_optimizer_service_converts_raster_image_to_webp_and_scales_down(): void
    {
        Storage::fake('public');

        // Create a large fake PNG image (2000x1000)
        $file = UploadedFile::fake()->image('doctor-portrait.png', 2000, 1000);

        $storedPath = ImageOptimizerService::optimizeAndStore(
            file: $file,
            directory: 'dentists',
            disk: 'public',
            maxWidth: 800,
            quality: 80
        );

        $this->assertNotNull($storedPath);
        $this->assertStringStartsWith('dentists/', $storedPath);
        $this->assertStringEndsWith('.webp', $storedPath);
        Storage::disk('public')->assertExists($storedPath);

        // Verify stored file properties via Intervention Image
        $fullPath = Storage::disk('public')->path($storedPath);
        $image = Image::decode($fullPath);

        $this->assertSame(800, $image->width());
        $this->assertSame(400, $image->height());
    }

    public function test_image_optimizer_service_preserves_smaller_images_without_upscaling(): void
    {
        Storage::fake('public');

        // Small 400x300 image
        $file = UploadedFile::fake()->image('small-avatar.jpg', 400, 300);

        $storedPath = ImageOptimizerService::optimizeAndStore(
            file: $file,
            directory: 'avatars',
            disk: 'public',
            maxWidth: 1200,
            quality: 82
        );

        $this->assertNotNull($storedPath);
        $this->assertStringEndsWith('.webp', $storedPath);

        $fullPath = Storage::disk('public')->path($storedPath);
        $image = Image::decode($fullPath);

        // Should maintain 400x300 and not upscale
        $this->assertSame(400, $image->width());
        $this->assertSame(300, $image->height());
    }

    public function test_optimize_images_artisan_command_converts_existing_files_and_updates_database(): void
    {
        Storage::fake('public');

        // Create dummy JPG file directly on disk
        $dummyImage = UploadedFile::fake()->image('dummy-dentist.jpg', 1800, 900);
        $originalRelativePath = Storage::disk('public')->putFileAs('dentists', $dummyImage, 'dummy-dentist.jpg');

        $dentist = Dentist::factory()->create([
            'photo' => $originalRelativePath,
        ]);

        $this->artisan('images:optimize', ['--disk' => 'public', '--width' => 1200])
            ->assertSuccessful();

        $dentist->refresh();
        $this->assertStringEndsWith('.webp', $dentist->photo);
        Storage::disk('public')->assertExists($dentist->photo);
        Storage::disk('public')->assertMissing($originalRelativePath);
    }

    public function test_file_upload_macro_optimize_for_web_is_registered(): void
    {
        $this->assertTrue(FileUpload::hasMacro('optimizeForWeb'));

        $component = FileUpload::make('photo')->optimizeForWeb(1000, 85);
        $this->assertInstanceOf(FileUpload::class, $component);
    }
}
