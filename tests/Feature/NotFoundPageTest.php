<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_existent_route_renders_custom_404_view_in_spanish(): void
    {
        $clinic = ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 123-4567',
            'whatsapp' => '+15551234567',
            'emergency_phone' => '+1 (555) 911-DENT',
            'email' => 'soporte@auradental.com',
            'address' => 'Av. Sanitaria 100',
        ]);

        $response = $this->get('/una-ruta-que-no-existe-para-test-404');

        $response->assertNotFound();
        $response->assertSee('Error 404 • Página No Encontrada');
        $response->assertSee('Parece que esta página no existe o ha cambiado de lugar');
        $response->assertSee('Volver al Inicio');
        $response->assertSee('Agendar Cita Médica');
        $response->assertSee('Explorar Tratamientos »');
        $response->assertSee($clinic->clinic_name);
        $response->assertSee($clinic->emergency_phone);
    }

    public function test_non_existent_route_renders_custom_404_view_in_english_when_locale_is_en(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 123-4567',
            'whatsapp' => '+15551234567',
            'emergency_phone' => '+1 (555) 911-DENT',
            'email' => 'soporte@auradental.com',
            'address' => 'Av. Sanitaria 100',
        ]);

        app()->setLocale('en');

        $response = $this->get('/another-missing-route-test-404');

        $response->assertNotFound();
        $response->assertSee('Error 404 • Page Not Found');
        $response->assertSee('It looks like this page does not exist or has moved');
        $response->assertSee('Back to Home');
        $response->assertSee('Book a Medical Appointment');
        $response->assertSee('Explore Treatments »');
    }
}
