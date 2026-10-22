<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MediaAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('media');

        $demoAssets = [
            [
                'filename' => '01M5JGJ440P8R1Z0QC5Z3Y02WD.webp',
                'file_path' => 'media/01M5JGJ440P8R1Z0QC5Z3Y02WD.webp',
                'title' => 'Recepción y Salas Clínicas',
                'alt_text' => 'Instalaciones y salas de espera de Aura Dental Studio',
                'width' => 800,
                'height' => 535,
            ],
            [
                'filename' => '01M5JGJ4A1DPQTAC9VR2S1K15N.webp',
                'file_path' => 'media/01M5JGJ4A1DPQTAC9VR2S1K15N.webp',
                'title' => 'Atención Médica y Diagnóstico',
                'alt_text' => 'Atención personalizada al paciente en gabinete odontológico',
                'width' => 600,
                'height' => 450,
            ],
            [
                'filename' => '01M5JGJ4J79Z8WR476FB9JAPK4.webp',
                'file_path' => 'media/01M5JGJ4J79Z8WR476FB9JAPK4.webp',
                'title' => 'Tecnología e Instrumental Quirúrgico',
                'alt_text' => 'Equipamiento tecnológico e instrumental esterilizado',
                'width' => 800,
                'height' => 800,
            ],
            [
                'filename' => '01M5JGJ4QXMG3KX2V14DRRQ5MJ.webp',
                'file_path' => 'media/01M5JGJ4QXMG3KX2V14DRRQ5MJ.webp',
                'title' => 'Gabinete Dental Moderno',
                'alt_text' => 'Sillón dental ergonómico y tecnología intraoral 3D',
                'width' => 600,
                'height' => 750,
            ],
            [
                'filename' => '01M5JGJ4XT6MQCK33A7DFCE3A1.webp',
                'file_path' => 'media/01M5JGJ4XT6MQCK33A7DFCE3A1.webp',
                'title' => 'Tratamientos de Estética Dental',
                'alt_text' => 'Procedimiento de blanqueamiento y diseño de sonrisa',
                'width' => 800,
                'height' => 533,
            ],
            [
                'filename' => '01M5JGJHPPB55Y34ZH5Q2Y8DFJ.webp',
                'file_path' => 'media/01M5JGJHPPB55Y34ZH5Q2Y8DFJ.webp',
                'title' => 'Consulta Médica Especializada',
                'alt_text' => 'Especialista odontológico evaluando plan de tratamiento',
                'width' => 600,
                'height' => 400,
            ],
            [
                'filename' => '01M5JGJHXTGN38D2Y1EZHHBZ9W.webp',
                'file_path' => 'media/01M5JGJHXTGN38D2Y1EZHHBZ9W.webp',
                'title' => 'Limpieza Dental Profesional',
                'alt_text' => 'Tratamiento de profilaxis ultrasónica indolora',
                'width' => 1000,
                'height' => 1100,
            ],
        ];

        foreach ($demoAssets as $item) {
            $path = $item['file_path'];
            $fullPath = Storage::disk('public')->path($path);
            $size = Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 40000;

            if (file_exists($fullPath)) {
                $info = @getimagesize($fullPath);
                if ($info) {
                    $item['width'] = $info[0] ?: $item['width'];
                    $item['height'] = $info[1] ?: $item['height'];
                }
            }

            MediaAsset::updateOrCreate(
                ['file_path' => $path],
                [
                    'filename' => $item['filename'],
                    'disk' => 'public',
                    'mime_type' => 'image/webp',
                    'file_size' => (int) $size,
                    'width' => $item['width'],
                    'height' => $item['height'],
                    'title' => $item['title'],
                    'alt_text' => $item['alt_text'],
                ]
            );
        }
    }
}
