<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
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
    public function update(Request $request, MediaAsset $mediaAsset): JsonResponse
    {
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
    public function destroy(MediaAsset $mediaAsset): JsonResponse
    {
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
     * Helper to store and register an uploaded file.
     */
    protected function processAndStoreFile($file, ?string $title = null, ?string $altText = null): MediaAsset
    {
        $originalName = $file->getClientOriginalName();
        $safeName = Str::random(24).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('media', $safeName, 'public');

        $width = null;
        $height = null;

        if ($file->getMimeType() !== 'image/svg+xml') {
            $dimensions = @getimagesize($file->getRealPath());
            if ($dimensions) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }
        }

        $defaultTitle = $title ?: Str::headline(pathinfo($originalName, PATHINFO_FILENAME));
        $defaultAlt = $altText ?: __('images.media_library_item', ['clinic' => 'Aura Dental Studio']);

        return MediaAsset::create([
            'filename' => $originalName,
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => (string) $file->getMimeType(),
            'file_size' => (int) $file->getSize(),
            'width' => $width,
            'height' => $height,
            'title' => $defaultTitle,
            'alt_text' => $defaultAlt,
        ]);
    }
}
