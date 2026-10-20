<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncMediaLibraryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza imágenes existentes en storage/app/public con la biblioteca de medios';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando sincronización de imágenes de la clínica...');

        $directories = ['clinic', 'dentists', 'services', 'blog', 'media'];
        $synced = 0;

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);

            foreach ($files as $filePath) {
                if (basename($filePath) === '.gitignore') {
                    continue;
                }

                if (MediaAsset::where('file_path', $filePath)->exists()) {
                    continue;
                }

                $fullPath = Storage::disk('public')->path($filePath);
                $mimeType = Storage::disk('public')->mimeType($filePath) ?: 'image/jpeg';

                if (! str_starts_with($mimeType, 'image/')) {
                    continue;
                }

                $fileSize = Storage::disk('public')->size($filePath);
                $originalName = basename($filePath);
                $width = null;
                $height = null;

                if ($mimeType !== 'image/svg+xml') {
                    $dimensions = @getimagesize($fullPath);
                    if ($dimensions) {
                        $width = $dimensions[0] ?? null;
                        $height = $dimensions[1] ?? null;
                    }
                }

                $title = ucfirst($dir).' - '.Str::headline(pathinfo($originalName, PATHINFO_FILENAME));

                MediaAsset::create([
                    'filename' => $originalName,
                    'file_path' => $filePath,
                    'disk' => 'public',
                    'mime_type' => $mimeType,
                    'file_size' => (int) $fileSize,
                    'width' => $width,
                    'height' => $height,
                    'title' => $title,
                    'alt_text' => $title,
                ]);

                $synced++;
                $this->line("  ✓ Sincronizado: <comment>{$filePath}</comment>");
            }
        }

        $this->info("¡Sincronización completada! Se registraron {$synced} archivos.");

        return self::SUCCESS;
    }
}
