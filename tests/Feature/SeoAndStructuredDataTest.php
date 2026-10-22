<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndStructuredDataTest extends TestCase
{
    use RefreshDatabase;

    private function createClinic(array $attributes = []): ClinicSetting
    {
        return ClinicSetting::create(array_merge([
            'clinic_name' => 'Aura Dental Studio VIP',
            'tagline' => 'Odontología digital de precisión',
            'phone' => '+1 (555) 777-8888',
            'whatsapp' => '+15557778888',
            'email' => 'vip@auradental.com',
            'address' => 'Boulevard Médico 101, Suite 400',
            'emergency_phone' => '+1 (555) 911-0000',
            'google_maps_url' => 'https://maps.google.com/?q=AuraDentalVIP',
            'opening_hours' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 2:00 PM",
            'show_language_switcher' => true,
            'social_links' => [
                'instagram' => 'https://instagram.com/auravip',
                'facebook' => 'https://facebook.com/auravip',
            ],
        ], $attributes));
    }

    public function test_sitemap_xml_renders_valid_xml_with_all_bilingual_routes(): void
    {
        $this->createClinic();

        $service = Service::create([
            'name' => 'Ortodoncia Invisible Sapphire',
            'slug' => 'ortodoncia-invisible-sapphire',
            'price' => 1250.00,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $author = User::factory()->create();
        $dentist = Dentist::create([
            'name' => 'Dra. Elena Ramos',
            'slug' => 'dra-elena-ramos',
            'specialty' => 'Ortodoncista',
            'bio' => 'Especialista en alineadores transparentes',
            'is_active' => true,
        ]);

        $post = Post::create([
            'title' => 'Beneficios del Escaneo Intraoral 3D',
            'slug' => 'beneficios-escaneo-intraoral-3d',
            'content' => 'Contenido clínico detallado sobre diagnóstico 3D.',
            'excerpt' => 'El escaneo 3D elimina las pastas de impresión tradicionales.',
            'category' => 'Tecnología',
            'user_id' => $author->id,
            'dentist_id' => $dentist->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $content);
        $this->assertStringContainsString('xmlns:xhtml="http://www.w3.org/1999/xhtml"', $content);

        // Static routes in both ES and EN
        $this->assertStringContainsString(url('/'), $content);
        $this->assertStringContainsString(url('/en'), $content);
        $this->assertStringContainsString(url('/servicios'), $content);
        $this->assertStringContainsString(url('/services'), $content);
        $this->assertStringContainsString(url('/nosotros'), $content);
        $this->assertStringContainsString(url('/about'), $content);
        $this->assertStringContainsString(url('/contacto'), $content);
        $this->assertStringContainsString(url('/contact'), $content);
        $this->assertStringContainsString(url('/reservar'), $content);
        $this->assertStringContainsString(url('/schedule'), $content);
        $this->assertStringContainsString(url('/blog'), $content);
        $this->assertStringContainsString(url('/en/blog'), $content);
        $this->assertStringContainsString(url('/politica-de-privacidad'), $content);
        $this->assertStringContainsString(url('/privacy-policy'), $content);

        // Dynamic service routes
        $this->assertStringContainsString(url('/servicios/ortodoncia-invisible-sapphire'), $content);
        $this->assertStringContainsString(url('/services/ortodoncia-invisible-sapphire'), $content);

        // Dynamic blog post routes
        $this->assertStringContainsString(url('/blog/beneficios-escaneo-intraoral-3d'), $content);
        $this->assertStringContainsString(url('/en/blog/beneficios-escaneo-intraoral-3d'), $content);

        // Multi-language hreflang alternates
        $this->assertStringContainsString('xhtml:link rel="alternate" hreflang="es"', $content);
        $this->assertStringContainsString('xhtml:link rel="alternate" hreflang="en"', $content);
    }

    public function test_robots_txt_serves_dynamic_sitemap_url(): void
    {
        $this->createClinic();

        $response = $this->get('/robots.txt');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $content = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $content);
    }

    public function test_opengraph_and_twitter_meta_tags_on_landing_page(): void
    {
        $clinic = $this->createClinic([
            'clinic_name' => 'Clínica Dental OdontoPlus',
            'tagline' => 'La sonrisa que siempre soñaste',
        ]);

        $response = $this->get('/');
        $response->assertSuccessful();

        $content = $response->getContent();

        // Meta title and description
        $this->assertStringContainsString('<title>Clínica Dental OdontoPlus | ', $content);
        $this->assertStringContainsString('name="description" content="La sonrisa que siempre soñaste"', $content);
        $this->assertStringContainsString('rel="canonical" href="'.url('/').'"', $content);

        // Hreflang alternates
        $this->assertStringContainsString('rel="alternate" hreflang="es" href="'.url('/').'"', $content);
        $this->assertStringContainsString('rel="alternate" hreflang="en" href="'.url('/en').'"', $content);

        // OpenGraph tags
        $this->assertStringContainsString('property="og:site_name" content="Clínica Dental OdontoPlus"', $content);
        $this->assertStringContainsString('property="og:title" content="Clínica Dental OdontoPlus | ', $content);
        $this->assertStringContainsString('property="og:description" content="La sonrisa que siempre soñaste"', $content);
        $this->assertStringContainsString('property="og:type" content="website"', $content);
        $this->assertStringContainsString('property="og:url" content="'.url('/').'"', $content);

        // Twitter cards
        $this->assertStringContainsString('name="twitter:card" content="summary_large_image"', $content);
        $this->assertStringContainsString('name="twitter:title" content="Clínica Dental OdontoPlus | ', $content);
    }

    public function test_schema_org_json_ld_on_landing_page(): void
    {
        $clinic = $this->createClinic([
            'clinic_name' => 'Dental Care Madrid',
            'phone' => '+34 91 123 4567',
            'email' => 'citas@dentalcaremadrid.es',
            'address' => 'Calle Gran Vía 55, Madrid',
        ]);

        $response = $this->get('/');
        $response->assertSuccessful();

        $content = $response->getContent();

        $this->assertStringContainsString('application/ld+json', $content);
        $this->assertStringContainsString('"@type": [', $content);
        $this->assertStringContainsString('"Dentist"', $content);
        $this->assertStringContainsString('"MedicalClinic"', $content);
        $this->assertStringContainsString('"name": "Dental Care Madrid"', $content);
        $this->assertStringContainsString('"telephone": "+34 91 123 4567"', $content);
        $this->assertStringContainsString('"email": "citas@dentalcaremadrid.es"', $content);
        $this->assertStringContainsString('"streetAddress": "Calle Gran Vía 55, Madrid"', $content);
    }

    public function test_schema_org_json_ld_on_service_detail_page(): void
    {
        $this->createClinic();

        $service = Service::create([
            'name' => 'Implantes Dentales Guiados',
            'slug' => 'implantes-dentales-guiados',
            'short_description' => 'Colocación guiada 3D de implantes de titanio con carga inmediata.',
            'price' => 890.00,
            'is_active' => true,
        ]);

        $response = $this->get('/servicios/implantes-dentales-guiados');
        $response->assertSuccessful();

        $content = $response->getContent();

        $this->assertStringContainsString('"@type": "MedicalProcedure"', $content);
        $this->assertStringContainsString('"name": "Implantes Dentales Guiados"', $content);
        $this->assertStringContainsString('"price": "890.00"', $content);
        $this->assertStringContainsString('"medicalSpecialty": "Dentistry"', $content);
    }

    public function test_schema_org_json_ld_on_blog_post_page(): void
    {
        $this->createClinic();

        $author = User::factory()->create();
        $dentist = Dentist::create([
            'name' => 'Dr. Carlos Mendoza',
            'slug' => 'dr-carlos-mendoza',
            'specialty' => 'Cirujano Maxilofacial',
            'is_active' => true,
        ]);

        $post = Post::create([
            'title' => 'Cuidados Postoperatorios Tras una Extracción',
            'slug' => 'cuidados-postoperatorios-extraccion',
            'content' => 'Recomendaciones fundamentales para una recuperación rápida y sin inflamación.',
            'excerpt' => 'Guía rápida de cuidados durante las primeras 48 horas.',
            'category' => 'Cirugía',
            'user_id' => $author->id,
            'dentist_id' => $dentist->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/blog/cuidados-postoperatorios-extraccion');
        $response->assertSuccessful();

        $content = $response->getContent();

        $this->assertStringContainsString('"@type": "BlogPosting"', $content);
        $this->assertStringContainsString('"headline": "Cuidados Postoperatorios Tras una Extracción"', $content);
        $this->assertStringContainsString('"Aura Dental Studio VIP"', $content);
    }

    public function test_different_clinic_settings_automatically_updates_all_seo_and_schema(): void
    {
        $clinic = $this->createClinic([
            'clinic_name' => 'Sanitas Dental Barcelona',
            'tagline' => 'Cuidamos cada detalle de tu salud bucal',
            'phone' => '+34 93 987 6543',
        ]);

        $response = $this->get('/');
        $response->assertSee('Sanitas Dental Barcelona');
        $response->assertSee('Cuidamos cada detalle de tu salud bucal');
        $response->assertSee('+34 93 987 6543');

        // Dynamically update clinic details (simulating admin panel edit)
        $clinic->update([
            'clinic_name' => 'Clínica Dental NovaSmile',
            'tagline' => 'Expertos en diseño y estética de sonrisas',
            'phone' => '+34 93 000 1122',
        ]);

        $responseUpdated = $this->get('/');
        $responseUpdated->assertSee('Clínica Dental NovaSmile');
        $responseUpdated->assertSee('Expertos en diseño y estética de sonrisas');
        $responseUpdated->assertSee('+34 93 000 1122');
        $responseUpdated->assertDontSee('Sanitas Dental Barcelona');
    }
}
