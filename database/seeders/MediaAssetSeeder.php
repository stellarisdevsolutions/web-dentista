<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('media');

        $sampleImages = [
            [
                'name' => 'clinica-consultorio-moderno.jpg',
                'url' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'tecnologia-dental-3d.jpg',
                'url' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'atencion-paciente-odontologia.jpg',
                'url' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'diseno-sonrisa-estetica.jpg',
                'url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($sampleImages as $img) {
            $path = 'media/'.$img['name'];

            if (! Storage::disk('public')->exists($path)) {
                $content = @file_get_contents($img['url']);
                if ($content) {
                    Storage::disk('public')->put($path, $content);
                }
            }

            if (Storage::disk('public')->exists($path) && ! MediaAsset::where('file_path', $path)->exists()) {
                $fullPath = Storage::disk('public')->path($path);
                $fileSize = Storage::disk('public')->size($path);
                $dimensions = @getimagesize($fullPath);

                MediaAsset::create([
                    'filename' => $img['name'],
                    'file_path' => $path,
                    'disk' => 'public',
                    'mime_type' => 'image/jpeg',
                    'file_size' => (int) $fileSize,
                    'width' => $dimensions[0] ?? 1200,
                    'height' => $dimensions[1] ?? 800,
                    'title' => Str::headline(pathinfo($img['name'], PATHINFO_FILENAME)),
                    'alt_text' => __('images.media_library_item', ['clinic' => 'Aura Dental Studio']),
                ]);
            }
        }
    }
}
