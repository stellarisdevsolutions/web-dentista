<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageAltAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio',
            'tagline' => 'Odontología moderna, integral y sin dolor',
            'phone' => '+1 (555) 234-5678',
            'whatsapp' => '+15552345678',
            'email' => 'hola@auradental.com',
            'address' => 'Av. Las Palmas 742, Piso 4',
            'emergency_phone' => '+1 (555) 911-DENT',
            'opening_hours' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM",
            'logo_url' => 'https://example.com/logo.png',
        ]);
    }

    public function test_home_page_renders_spanish_alt_texts_by_default(): void
    {
        config(['app.locale' => 'es']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Atención y tratamiento odontológico profesional en Aura Dental Studio', false);
        $response->assertSee('Instalaciones clínicas modernas y consultorios odontológicos de Aura Dental Studio', false);
        $response->assertSee('Procedimiento y diagnóstico dental de alta precisión', false);
        $response->assertSee('Especialista odontológico realizando revisión clínica avanzada', false);
        $response->assertSee('Tratamiento dental preventivo y aparatología de última generación', false);
        $response->assertSee('Odontólogo explicando simulación y diagnóstico digital 3D a paciente', false);
        $response->assertSee('Doctora especialista del equipo odontológico de Aura Dental Studio', false);
        $response->assertSee('Logotipo oficial de Aura Dental Studio', false);
    }

    public function test_home_page_renders_english_alt_texts_when_locale_is_en(): void
    {
        config(['app.locale' => 'en']);
        app()->setLocale('en');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Professional dental care and specialized treatment at Aura Dental Studio', false);
        $response->assertSee('Modern clinical facilities and dental offices at Aura Dental Studio', false);
        $response->assertSee('High-precision dental procedure and clinical diagnostics', false);
        $response->assertSee('Dental specialist performing an advanced clinical examination', false);
        $response->assertSee('Preventive dental treatment and state-of-the-art dental equipment', false);
        $response->assertSee('Dentist explaining 3D digital simulation and diagnostics to patient', false);
        $response->assertSee('Specialist doctor from the dental care team at Aura Dental Studio', false);
        $response->assertSee('Official logo of Aura Dental Studio', false);
    }

    public function test_about_page_renders_spanish_alt_texts_by_default(): void
    {
        config(['app.locale' => 'es']);
        app()->setLocale('es');

        $response = $this->get('/nosotros');

        $response->assertOk();
        $response->assertSee('Equipo de profesionales y especialistas odontológicos en Aura Dental Studio', false);
        $response->assertSee('Área de atención clínica y equipamiento quirúrgico esterilizado', false);
    }

    public function test_about_page_renders_english_alt_texts_when_locale_is_en(): void
    {
        config(['app.locale' => 'en']);
        app()->setLocale('en');

        $response = $this->get('/nosotros');

        $response->assertOk();
        $response->assertSee('Team of dental healthcare professionals and specialists at Aura Dental Studio', false);
        $response->assertSee('Clinical care area and sterilized surgical dental equipment', false);
    }
}
