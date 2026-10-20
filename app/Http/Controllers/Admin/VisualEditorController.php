<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisualEditorController extends Controller
{
    /**
     * Display the full-screen visual customizer workspace.
     */
    public function index(Request $request): View
    {
        $currentPage = $request->query('page', 'home');

        $availablePages = [
            'home' => [
                'name' => 'Página de Inicio',
                'url' => route('home', ['editor_preview' => 1]),
            ],
            'about' => [
                'name' => 'Sobre Nosotros',
                'url' => route('about', ['editor_preview' => 1]),
            ],
            'services' => [
                'name' => 'Tratamientos y Servicios',
                'url' => route('services.index', ['editor_preview' => 1]),
            ],
            'contact' => [
                'name' => 'Contacto y Citas',
                'url' => route('contact', ['editor_preview' => 1]),
            ],
        ];

        if (! array_key_exists($currentPage, $availablePages)) {
            $currentPage = 'home';
        }

        $sections = SiteSection::forPage($currentPage)
            ->get()
            ->values();

        return view('admin.editor.index', [
            'currentPage' => $currentPage,
            'availablePages' => $availablePages,
            'sections' => $sections,
        ]);
    }

    /**
     * Persist updated section order, visibility, and contents.
     */
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => ['required', 'string', 'max:50'],
            'sections' => ['required', 'array'],
            'sections.*.section_key' => ['required', 'string', 'max:50'],
            'sections.*.is_active' => ['required', 'boolean'],
            'sections.*.order' => ['required', 'integer'],
            'sections.*.content' => ['required', 'array'],
        ]);

        foreach ($validated['sections'] as $item) {
            SiteSection::updateOrCreate(
                [
                    'page' => $validated['page'],
                    'section_key' => $item['section_key'],
                ],
                [
                    'is_active' => $item['is_active'],
                    'order' => $item['order'],
                    'content' => $item['content'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => '¡Página guardada y publicada exitosamente!',
        ]);
    }
}
