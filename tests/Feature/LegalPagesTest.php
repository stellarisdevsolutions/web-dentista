<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    private function createClinic(): ClinicSetting
    {
        return ClinicSetting::create([
            'clinic_name' => 'Aura Dental Clinic',
            'tagline' => 'Odontología integral y avanzada.',
            'phone' => '+1 (555) 123-4567',
            'emergency_phone' => '+1 (555) 911-DENT',
            'whatsapp' => '+15551234567',
            'email' => 'privacidad@auradental.com',
            'address' => 'Av. Sanitaria 450, Piso 3',
        ]);
    }

    public function test_privacy_policy_page_renders_in_spanish_by_default(): void
    {
        $clinic = $this->createClinic();

        $response = $this->get(route('privacy'));

        $response->assertSuccessful();
        $response->assertSee($clinic->clinic_name);
        $response->assertSee('Política de Privacidad');
        $response->assertSee('Responsable del Tratamiento de Datos');
        $response->assertSee('Tus Derechos de Protección de Datos (ARCO)');
        $response->assertSee('privacidad@auradental.com');
    }

    public function test_privacy_policy_page_renders_in_english_when_locale_is_en(): void
    {
        $this->createClinic();
        app()->setLocale('en');

        $response = $this->get('/privacy-policy');

        $response->assertSuccessful();
        $response->assertSee('Privacy Policy');
        $response->assertSee('Data Controller');
        $response->assertSee('Your Data Protection Rights');
        $response->assertSee('SSL / TLS Encryption');
    }

    public function test_terms_and_conditions_page_renders_in_spanish_by_default(): void
    {
        $clinic = $this->createClinic();

        $response = $this->get(route('terms'));

        $response->assertSuccessful();
        $response->assertSee($clinic->clinic_name);
        $response->assertSee('Términos del Servicio');
        $response->assertSee('Aviso Previo de Cancelación (24 Horas)');
        $response->assertSee('Protocolo de Emergencias Odontológicas');
        $response->assertSee($clinic->emergency_phone);
    }

    public function test_terms_and_conditions_page_renders_in_english_when_locale_is_en(): void
    {
        $this->createClinic();
        app()->setLocale('en');

        $response = $this->get('/terms-and-conditions');

        $response->assertSuccessful();
        $response->assertSee('Terms of Service');
        $response->assertSee('Advance Cancellation Notice (24 Hours)');
        $response->assertSee('Dental Emergency Protocol');
        $response->assertSee('Important Medical Notice: Informational Purpose');
    }

    public function test_patient_rights_page_renders_in_spanish_by_default(): void
    {
        $clinic = $this->createClinic();

        $response = $this->get(route('patient-rights'));

        $response->assertSuccessful();
        $response->assertSee($clinic->clinic_name);
        $response->assertSee('Carta de Derechos y Deberes del Paciente');
        $response->assertSee('Tus Derechos como Paciente');
        $response->assertSee('1. Trato Digno y Respetuoso');
        $response->assertSee('Tus Deberes y Responsabilidades');
        $response->assertSee('Veracidad en la Información Médica:');
    }

    public function test_patient_rights_page_renders_in_english_when_locale_is_en(): void
    {
        $this->createClinic();
        app()->setLocale('en');

        $response = $this->get('/patient-rights');

        $response->assertSuccessful();
        $response->assertSee('Patient Rights & Responsibilities Charter');
        $response->assertSee('Your Rights as a Patient');
        $response->assertSee('1. Dignified & Respectful Care');
        $response->assertSee('Your Duties & Responsibilities');
        $response->assertSee('Truthfulness in Medical History:');
    }

    public function test_footer_contains_working_links_to_all_legal_pages(): void
    {
        $this->createClinic();

        $response = $this->get(route('home'));

        $response->assertSuccessful();
        $response->assertSee(route('privacy'));
        $response->assertSee(route('terms'));
        $response->assertSee(route('patient-rights'));
    }
}
