<?php

namespace App\Providers;

use App\Services\ImageOptimizerService;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Macro to allow explicit configuration with custom maxWidth and quality
        FileUpload::macro('optimizeForWeb', function (int $maxWidth = ImageOptimizerService::DEFAULT_MAX_WIDTH, int $quality = ImageOptimizerService::DEFAULT_QUALITY): static {
            /** @var FileUpload $this */
            return $this
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif'])
                ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file) use ($maxWidth, $quality): ?string {
                    return ImageOptimizerService::optimizeAndStore(
                        file: $file,
                        directory: (string) ($component->getDirectory() ?? ''),
                        disk: (string) ($component->getDiskName() ?? 'public'),
                        maxWidth: $maxWidth,
                        quality: $quality,
                    );
                });
        });

        // Global default: automatically optimize and convert any raster image uploaded in Filament to WebP
        FileUpload::configureUsing(function (FileUpload $component): void {
            $component->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                $mime = $file->getMimeType() ?? '';
                $ext = strtolower($file->getClientOriginalExtension());

                $isRasterImage = str_starts_with($mime, 'image/') && $mime !== 'image/svg+xml' && $ext !== 'svg';

                if ($isRasterImage) {
                    return ImageOptimizerService::optimizeAndStore(
                        file: $file,
                        directory: (string) ($component->getDirectory() ?? ''),
                        disk: (string) ($component->getDiskName() ?? 'public'),
                    );
                }

                return $component->saveUploadedFile($file);
            });
        });
    }
}
