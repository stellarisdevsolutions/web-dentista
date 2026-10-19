<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Dentist;
use App\Models\Post;
use App\Models\Service;
use App\Models\SmileCase;
use App\Models\Testimonial;
use App\Services\ImageOptimizerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize
                            {--disk=public : El disco de almacenamiento a escanear}
                            {--width=1600 : Ancho máximo en píxeles para escalar imágenes grandes}
                            {--quality=82 : Calidad de compresión WebP (1-100)}
                            {--keep-originals : Mantener los archivos originales JPG/PNG en disco}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte imágenes de almacenamiento a formato WebP optimizado y actualiza las referencias en base de datos.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = (string) $this->option('disk');
        $maxWidth = (int) $this->option('width');
        $quality = (int) $this->option('quality');
        $deleteOriginals = ! $this->option('keep-originals');

        $storage = Storage::disk($disk);
        $this->info("Escaneando disco [{$disk}] en busca de imágenes optimizables (JPG/PNG)...");

        $allFiles = $storage->allFiles();
        $targetExtensions = ['jpg', 'jpeg', 'png'];

        $eligibleFiles = array_filter($allFiles, function (string $path) use ($targetExtensions) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            return in_array($ext, $targetExtensions, true);
        });

        if (empty($eligibleFiles)) {
            $this->info('No se encontraron imágenes JPG/PNG pendientes de optimización.');

            return self::SUCCESS;
        }

        $this->info('Se encontraron '.count($eligibleFiles).' imagen(es) para optimizar a WebP.');

        $rows = [];
        $totalOldBytes = 0;
        $totalNewBytes = 0;
        $convertedCount = 0;

        $bar = $this->output->createProgressBar(count($eligibleFiles));
        $bar->start();

        foreach ($eligibleFiles as $relativePath) {
            $result = ImageOptimizerService::optimizeExistingFile(
                relativePath: $relativePath,
                disk: $disk,
                maxWidth: $maxWidth,
                quality: $quality,
                deleteOriginal: $deleteOriginals
            );

            if ($result) {
                $oldPath = $result['old_path'];
                $newPath = $result['new_path'];

                // Update database references across all application models
                Dentist::where('photo', $oldPath)->update(['photo' => $newPath]);
                Service::where('image', $oldPath)->update(['image' => $newPath]);
                Post::where('cover_image', $oldPath)->update(['cover_image' => $newPath]);
                Banner::where('background_image', $oldPath)->update(['background_image' => $newPath]);
                SmileCase::where('before_image', $oldPath)->update(['before_image' => $newPath]);
                SmileCase::where('after_image', $oldPath)->update(['after_image' => $newPath]);
                Testimonial::where('patient_avatar', $oldPath)->update(['patient_avatar' => $newPath]);

                $totalOldBytes += $result['old_size'];
                $totalNewBytes += $result['new_size'];
                $convertedCount++;

                $rows[] = [
                    'Archivo' => basename($oldPath),
                    'Original' => $this->formatBytes($result['old_size']),
                    'WebP' => $this->formatBytes($result['new_size']),
                    'Ahorro' => "-{$result['saved_percent']}%",
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Archivo', 'Original', 'WebP Optimizado', 'Ahorro'], $rows);

        $totalSavedBytes = max(0, $totalOldBytes - $totalNewBytes);
        $totalPercent = $totalOldBytes > 0 ? round(($totalSavedBytes / $totalOldBytes) * 100, 1) : 0;

        $this->info("✨ Éxito: {$convertedCount} imágenes convertidas a WebP.");
        $this->info("📊 Peso anterior: {$this->formatBytes($totalOldBytes)} | Peso nuevo: {$this->formatBytes($totalNewBytes)}");
        $this->info("🚀 Ahorro total de almacenamiento y ancho de banda: {$this->formatBytes($totalSavedBytes)} (-{$totalPercent}%)");

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
