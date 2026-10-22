<?php

namespace App\Providers;

use App\Routing\LocalizedUrlGenerator;
use App\Services\ImageOptimizerService;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();
            $app->instance('routes', $routes);

            $url = new LocalizedUrlGenerator(
                $routes,
                $app->rebinding('request', function ($app, $request) {
                    $app['url']->setRequest($request);
                }),
                $app['config']['app.asset_url']
            );

            $url->setSessionResolver(function () use ($app) {
                return $app['session'] ?? null;
            });

            $url->setKeyResolver(function () use ($app) {
                $config = $app->make('config');

                return [$config->get('app.key'), ...($config->get('app.previous_keys') ?? [])];
            });

            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

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
