<?php

namespace App\Services;

use Illuminate\Http\Request;

class LocalizationService
{
    /**
     * Map of routes between Spanish and English.
     *
     * @var array<string, array{es: string, en: string}>
     */
    protected static array $pageRoutePairs = [
        'home' => ['es' => 'home', 'en' => 'home.en'],
        'about' => ['es' => 'about', 'en' => 'about.en'],
        'services' => ['es' => 'services.index', 'en' => 'services.index.en'],
        'contact' => ['es' => 'contact', 'en' => 'contact.en'],
        'schedule' => ['es' => 'schedule', 'en' => 'schedule.en'],
        'privacy' => ['es' => 'privacy', 'en' => 'privacy.en'],
        'terms' => ['es' => 'terms', 'en' => 'terms.en'],
        'patient-rights' => ['es' => 'patient-rights', 'en' => 'patient-rights.en'],
        'blog' => ['es' => 'blog.index', 'en' => 'blog.index.en'],
    ];

    /**
     * Get the corresponding URL in the target locale for the current request.
     */
    public static function getAlternateUrl(string $targetLocale, ?Request $request = null): string
    {
        $request = $request ?? request();
        $path = trim($request->path(), '/');

        // Exact path mappings
        $directMap = [
            'es' => [
                '' => '/',
                'services' => '/servicios',
                'about' => '/nosotros',
                'contact' => '/contacto',
                'schedule' => '/reservar',
                'privacy-policy' => '/politica-de-privacidad',
                'terms-and-conditions' => '/terminos-y-condiciones',
                'patient-rights' => '/derechos-del-paciente',
                'en' => '/',
                'en/blog' => '/blog',
            ],
            'en' => [
                'servicios' => '/services',
                'nosotros' => '/about',
                'contacto' => '/contact',
                'reservar' => '/schedule',
                'politica-de-privacidad' => '/privacy-policy',
                'terminos-y-condiciones' => '/terms-and-conditions',
                'derechos-del-paciente' => '/patient-rights',
                '' => '/en',
                'blog' => '/en/blog',
            ],
        ];

        if (isset($directMap[$targetLocale][$path])) {
            return url($directMap[$targetLocale][$path]);
        }

        // Dynamic prefix patterns: services/{slug} <-> servicios/{slug}
        if ($targetLocale === 'en') {
            if (str_starts_with($path, 'servicios/')) {
                $slug = substr($path, strlen('servicios/'));

                return url('/services/'.$slug);
            }
            if (str_starts_with($path, 'blog/')) {
                $slug = substr($path, strlen('blog/'));

                return url('/en/blog/'.$slug);
            }
        } elseif ($targetLocale === 'es') {
            if (str_starts_with($path, 'services/')) {
                $slug = substr($path, strlen('services/'));

                return url('/servicios/'.$slug);
            }
            if (str_starts_with($path, 'en/blog/')) {
                $slug = substr($path, strlen('en/blog/'));

                return url('/blog/'.$slug);
            }
        }

        // Fallback: switch query parameter
        return route('lang.switch', ['locale' => $targetLocale, 'redirect' => $request->fullUrl()]);
    }
}
