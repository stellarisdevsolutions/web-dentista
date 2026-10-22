<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function createClinic(): ClinicSetting
    {
        return ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 123-4567',
            'emergency_phone' => '+1 (555) 911-DENT',
            'whatsapp' => '+15551234567',
            'email' => 'hola@auradental.com',
            'address' => 'Av. Sanitaria 100',
        ]);
    }

    private function createService(): Service
    {
        return Service::create([
            'name' => 'Ortodoncia Invisible',
            'slug' => 'ortodoncia-invisible',
            'category' => 'Ortodoncia',
            'short_description' => 'Alineadores invisibles y estéticos.',
            'price' => 1200.00,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);
    }

    public function test_spanish_servicios_url_renders_in_spanish_with_spanish_links(): void
    {
        $this->createClinic();
        $service = $this->createService();

        $response = $this->get('/servicios');

        $response->assertSuccessful();
        $this->assertEquals('es', app()->getLocale());
        $response->assertSee('Nuestros Tratamientos y Procedimientos');
        $response->assertSee('/servicios/'.$service->slug);
        $response->assertSee(url('/nosotros'));
        $response->assertSee(url('/contacto'));
        $response->assertSee(url('/reservar'));
    }

    public function test_english_services_url_renders_in_english_with_english_links(): void
    {
        $this->createClinic();
        $service = $this->createService();

        $response = $this->get('/services');

        $response->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
        $response->assertSee('Our Treatments & Procedures');
        $response->assertSee('/services/'.$service->slug);
        $response->assertSee(url('/about'));
        $response->assertSee(url('/contact'));
        $response->assertSee(url('/schedule'));
    }

    public function test_spanish_and_english_service_detail_urls(): void
    {
        $this->createClinic();
        $service = $this->createService();

        // Spanish detail
        $esResponse = $this->get('/servicios/'.$service->slug);
        $esResponse->assertSuccessful();
        $this->assertEquals('es', app()->getLocale());
        $esResponse->assertSee('/servicios');

        // English detail
        $enResponse = $this->get('/services/'.$service->slug);
        $enResponse->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
        $enResponse->assertSee('/services');
    }

    public function test_about_and_nosotros_routes(): void
    {
        $this->createClinic();

        $esResponse = $this->get('/nosotros');
        $esResponse->assertSuccessful();
        $this->assertEquals('es', app()->getLocale());

        $enResponse = $this->get('/about');
        $enResponse->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
        $enResponse->assertSee('About');
    }

    public function test_contact_and_contacto_routes(): void
    {
        $this->createClinic();

        $esResponse = $this->get('/contacto');
        $esResponse->assertSuccessful();
        $this->assertEquals('es', app()->getLocale());

        $enResponse = $this->get('/contact');
        $enResponse->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_schedule_and_reservar_routes(): void
    {
        $this->createClinic();

        $esResponse = $this->get('/reservar');
        $esResponse->assertSuccessful();
        $this->assertEquals('es', app()->getLocale());

        $enResponse = $this->get('/schedule');
        $enResponse->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_language_switch_endpoint(): void
    {
        $response = $this->get(route('lang.switch', ['locale' => 'en']));

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));

        $responseEs = $this->get(route('lang.switch', ['locale' => 'es']));
        $responseEs->assertRedirect();
        $this->assertEquals('es', session('locale'));
    }
}
