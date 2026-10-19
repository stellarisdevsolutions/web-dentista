<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SplFileInfo;
use Throwable;

class ImageOptimizerService
{
    /**
     * Default maximum width for web display.
     */
    public const DEFAULT_MAX_WIDTH = 1600;

    /**
     * Default WebP compression quality (82 provides optimal visual quality with ~80-90% size reduction).
     */
    public const DEFAULT_QUALITY = 82;

    /**
     * Optimize an uploaded image file, convert it to WebP, resize if exceeding maxWidth, and store it.
     *
     * @param  string  $directory  Target directory inside the disk (e.g. 'dentists', 'services')
     * @param  string  $disk  Storage disk name (default: 'public')
     * @param  int  $maxWidth  Maximum width in pixels
     * @param  int  $quality  WebP compression quality (1-100)
     * @return string|null Relative storage path of the stored file (e.g. 'dentists/01JK23...webp')
     */
    public static function optimizeAndStore(
        TemporaryUploadedFile|UploadedFile|SplFileInfo $file,
        string $directory = '',
        string $disk = 'public',
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY
    ): ?string {
        $realPath = $file instanceof SplFileInfo ? $file->getRealPath() : null;

        if (! $realPath || ! file_exists($realPath)) {
            return null;
        }

        $storage = Storage::disk($disk);
        $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));

        if ($file instanceof UploadedFile) {
            $extension = strtolower($file->getClientOriginalExtension());
        }

        // SVGs should not be rasterized to WebP; keep as vector SVG
        if ($extension === 'svg') {
            $filename = Str::ulid().'.svg';
            $path = trim($directory.'/'.$filename, '/');
            $storage->put($path, file_get_contents($realPath));

            return $path;
        }

        try {
            $image = Image::decode($realPath);

            // Scale down to max width while preserving aspect ratio (never upscales smaller images)
            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            // Encode to modern WebP format
            $encoded = $image->encodeUsingFileExtension('webp', quality: $quality);

            $filename = Str::ulid().'.webp';
            $path = trim($directory.'/'.$filename, '/');

            $storage->put($path, (string) $encoded);

            return $path;
        } catch (Throwable $e) {
            report($e);

            // Graceful fallback: store original file untouched if image decode fails
            $fallbackExt = $extension ?: 'jpg';
            $filename = Str::ulid().'.'.$fallbackExt;
            $path = trim($directory.'/'.$filename, '/');
            $storage->put($path, file_get_contents($realPath));

            return $path;
        }
    }

    /**
     * Optimize an existing file in storage and convert it to WebP.
     *
     * @return array{
     *     old_path: string,
     *     new_path: string,
     *     old_size: int,
     *     new_size: int,
     *     saved_bytes: int,
     *     saved_percent: float
     * }|null
     */
    public static function optimizeExistingFile(
        string $relativePath,
        string $disk = 'public',
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY,
        bool $deleteOriginal = false
    ): ?array {
        $storage = Storage::disk($disk);

        if (! $storage->exists($relativePath)) {
            return null;
        }

        $fullPath = $storage->path($relativePath);
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($ext === 'svg') {
            return null;
        }

        $oldSize = (int) $storage->size($relativePath);

        try {
            $image = Image::decode($fullPath);

            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            $encoded = $image->encodeUsingFileExtension('webp', quality: $quality);
            $newSize = strlen((string) $encoded);

            $dirname = pathinfo($relativePath, PATHINFO_DIRNAME);
            $filenameWithoutExt = pathinfo($relativePath, PATHINFO_FILENAME);
            $newRelativePath = ($dirname !== '.' && $dirname !== '')
                ? $dirname.'/'.$filenameWithoutExt.'.webp'
                : $filenameWithoutExt.'.webp';

            $storage->put($newRelativePath, (string) $encoded);

            // If requested and old file was not already a .webp with the same name, remove it
            if ($deleteOriginal && $relativePath !== $newRelativePath) {
                $storage->delete($relativePath);
            }

            $savedBytes = max(0, $oldSize - $newSize);
            $savedPercent = $oldSize > 0 ? round(($savedBytes / $oldSize) * 100, 1) : 0.0;

            return [
                'old_path' => $relativePath,
                'new_path' => $newRelativePath,
                'old_size' => $oldSize,
                'new_size' => $newSize,
                'saved_bytes' => $savedBytes,
                'saved_percent' => $savedPercent,
            ];
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
