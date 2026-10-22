<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Services\ImageOptimizerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    /**
     * List media assets formatted for the modal picker or standalone view.
     */
    public function index(Request $request): JsonResponse
    {
        $folder = $request->query('folder', 'media');
        $term = $request->query('search');

        $query = MediaAsset::query();

        if ($folder === 'media' || $folder === 'editor' || empty($folder)) {
            $query->where('file_path', 'like', 'media/%');
        } elseif ($folder !== 'all') {
            $query->where('file_path', 'like', "{$folder}/%");
        }

        $assets = $query
            ->when($term, fn ($q) => $q->search($term))
            ->latest()
            ->take(100)
            ->get();

        return response()->json([
            'success' => true,
            'assets' => $assets,
        ]);
    }

    /**
     * Render the dedicated full-screen Media Library dashboard page.
     */
    public function standalone(): View
    {
        return view('admin.media-library.standalone');
    }

    /**
     * Upload and register one or multiple media assets.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->hasFile('files')) {
            $request->validate([
                'files' => ['required', 'array'],
                'files.*' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp,svg,gif', 'max:8192'],
            ]);

            $createdAssets = [];
            foreach ($request->file('files') as $file) {
                $createdAssets[] = $this->processAndStoreFile($file);
            }

            return response()->json([
                'success' => true,
                'message' => count($createdAssets).' imágenes subidas exitosamente.',
                'assets' => $createdAssets,
                'asset' => $createdAssets[0] ?? null,
            ], 201);
        }

        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp,svg,gif', 'max:8192'],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $asset = $this->processAndStoreFile(
            $request->file('file'),
            $request->input('title'),
            $request->input('alt_text')
        );

        return response()->json([
            'success' => true,
            'message' => 'Imagen subida exitosamente.',
            'asset' => $asset,
        ], 201);
    }

    /**
     * Update asset metadata (title and alt text for SEO / Accessibility).
     */
    public function update(Request $request, string|int $id): JsonResponse
    {
        $mediaAsset = MediaAsset::find($id);
        if (! $mediaAsset) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo no fue encontrado.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $mediaAsset->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Detalles de la imagen actualizados.',
            'asset' => $mediaAsset->fresh(),
        ]);
    }

    /**
     * Delete an asset from disk and database.
     */
    public function destroy(string|int $id): JsonResponse
    {
        $mediaAsset = MediaAsset::find($id);

        if (! $mediaAsset) {
            return response()->json([
                'success' => true,
                'message' => 'El archivo ya no existe o fue eliminado previamente.',
            ]);
        }

        if (Storage::disk($mediaAsset->disk)->exists($mediaAsset->file_path)) {
            Storage::disk($mediaAsset->disk)->delete($mediaAsset->file_path);
        }

        $mediaAsset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Archivo eliminado de la biblioteca.',
        ]);
    }

    /**
     * Sync pre-existing clinic storage files into the media library.
     */
    public function syncExisting(): JsonResponse
    {
        $directories = ['media'];
        $syncedCount = 0;

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);

            foreach ($files as $filePath) {
                if (basename($filePath) === '.gitignore') {
                    continue;
                }

                $alreadyRegistered = MediaAsset::where('file_path', $filePath)->exists();
                if ($alreadyRegistered) {
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

                $syncedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'synced_count' => $syncedCount,
            'message' => $syncedCount > 0
                ? "Se sincronizaron {$syncedCount} imágenes existentes en la biblioteca."
                : 'Todas las imágenes ya se encontraban sincronizadas.',
        ]);
    }

    /**
     * Helper to store and register an uploaded file, automatically optimizing to WebP.
     */
    protected function processAndStoreFile($file, ?string $title = null, ?string $altText = null): MediaAsset
    {
        $originalName = $file->getClientOriginalName();
        $isSvg = $file->getMimeType() === 'image/svg+xml' || strtolower($file->getClientOriginalExtension()) === 'svg';

        // Optimize raster images to WebP via ImageOptimizerService
        $path = ImageOptimizerService::optimizeAndStore(
            file: $file,
            directory: 'media',
            disk: 'public',
        );

        if (! $path) {
            $safeName = Str::random(24).'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('media', $safeName, 'public');
        }

        $width = null;
        $height = null;
        $storedFullPath = Storage::disk('public')->path($path);

        if (! $isSvg && file_exists($storedFullPath)) {
            $dimensions = @getimagesize($storedFullPath);
            if ($dimensions) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }
        }

        $defaultTitle = $title ?: Str::headline(pathinfo($originalName, PATHINFO_FILENAME));
        $defaultAlt = $altText ?: __('images.media_library_item', ['clinic' => 'Aura Dental Studio']);
        $fileSize = file_exists($storedFullPath) ? (int) filesize($storedFullPath) : (int) $file->getSize();
        $mimeType = file_exists($storedFullPath) ? (mime_content_type($storedFullPath) ?: 'image/webp') : (string) $file->getMimeType();

        return MediaAsset::create([
            'filename' => $originalName,
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'title' => $defaultTitle,
            'alt_text' => $defaultAlt,
        ]);
    }
}
