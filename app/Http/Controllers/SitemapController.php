<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap with all bilingual routes and database content.
     */
    public function index(): Response
    {
        $urls = [];

        // Static bilingual pages configuration
        $staticPages = [
            [
                'es' => url('/'),
                'en' => url('/en'),
                'priority' => '1.0',
                'changefreq' => 'weekly',
            ],
            [
                'es' => url('/servicios'),
                'en' => url('/services'),
                'priority' => '0.9',
                'changefreq' => 'weekly',
            ],
            [
                'es' => url('/nosotros'),
                'en' => url('/about'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ],
            [
                'es' => url('/reservar'),
                'en' => url('/schedule'),
                'priority' => '0.9',
                'changefreq' => 'daily',
            ],
            [
                'es' => url('/contacto'),
                'en' => url('/contact'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ],
            [
                'es' => url('/blog'),
                'en' => url('/en/blog'),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ],
            [
                'es' => url('/politica-de-privacidad'),
                'en' => url('/privacy-policy'),
                'priority' => '0.3',
                'changefreq' => 'yearly',
            ],
            [
                'es' => url('/terminos-y-condiciones'),
                'en' => url('/terms-and-conditions'),
                'priority' => '0.3',
                'changefreq' => 'yearly',
            ],
            [
                'es' => url('/derechos-del-paciente'),
                'en' => url('/patient-rights'),
                'priority' => '0.3',
                'changefreq' => 'yearly',
            ],
        ];

        $today = now()->startOfDay()->toAtomString();

        foreach ($staticPages as $page) {
            // Spanish entry
            $urls[] = [
                'loc' => $page['es'],
                'lastmod' => $today,
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
                'alternates' => [
                    'es' => $page['es'],
                    'en' => $page['en'],
                ],
            ];

            // English entry
            $urls[] = [
                'loc' => $page['en'],
                'lastmod' => $today,
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
                'alternates' => [
                    'es' => $page['es'],
                    'en' => $page['en'],
                ],
            ];
        }

        // Dynamic treatments / services
        $services = Service::where('is_active', true)->get();
        foreach ($services as $service) {
            $esUrl = url('/servicios/'.$service->slug);
            $enUrl = url('/services/'.$service->slug);
            $lastmod = ($service->updated_at ?? now())->toAtomString();

            $urls[] = [
                'loc' => $esUrl,
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => [
                    'es' => $esUrl,
                    'en' => $enUrl,
                ],
            ];

            $urls[] = [
                'loc' => $enUrl,
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => [
                    'es' => $esUrl,
                    'en' => $enUrl,
                ],
            ];
        }

        // Dynamic published blog posts
        $posts = Post::where('is_published', true)->get();
        foreach ($posts as $post) {
            $esUrl = url('/blog/'.$post->slug);
            $enUrl = url('/en/blog/'.$post->slug);
            $lastmod = ($post->updated_at ?? $post->published_at ?? now())->toAtomString();

            $urls[] = [
                'loc' => $esUrl,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => '0.7',
                'alternates' => [
                    'es' => $esUrl,
                    'en' => $enUrl,
                ],
            ];

            $urls[] = [
                'loc' => $enUrl,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => '0.7',
                'alternates' => [
                    'es' => $esUrl,
                    'en' => $enUrl,
                ],
            ];
        }

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
