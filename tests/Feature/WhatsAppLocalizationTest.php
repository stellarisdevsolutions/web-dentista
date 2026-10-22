<?php

namespace Tests\Feature;

use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected ClinicSetting $clinic;

    protected Service $service;

    protected Dentist $dentist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio',
            'phone' => '+1 (555) 234-5678',
            'whatsapp' => '+15552345678',
            'email' => 'hello@auradental.com',
            'address' => '742 Evergreen Terrace, Beverly Hills, CA 90210',
        ]);

        $this->service = Service::create([
            'name' => 'Limpieza Dental Ultrasonido',
            'slug' => 'limpieza-dental-ultrasonido',
            'price' => 75.00,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $this->dentist = Dentist::create([
            'name' => 'Dr. Carlos Mendoza',
            'slug' => 'dr-carlos-mendoza',
            'specialty' => 'Prevención y Salud Oral',
            'is_active' => true,
        ]);

        $this->dentist->services()->attach($this->service->id);
    }

    public function test_english_public_schedule_page_does_not_mention_whatsapp(): void
    {
        $response = $this->get('/schedule');
        $response->assertSuccessful();

        $content = $response->getContent();
        $this->assertStringNotContainsStringIgnoringCase('WhatsApp', $content);
    }

    public function test_english_public_contact_page_does_not_mention_whatsapp(): void
    {
        $response = $this->get('/contact');
        $response->assertSuccessful();

        $content = $response->getContent();
        $this->assertStringNotContainsStringIgnoringCase('WhatsApp', $content);
    }

    public function test_english_public_home_page_does_not_mention_whatsapp(): void
    {
        app()->setLocale('en');

        $response = $this->get('/en');
        $response->assertSuccessful();

        $content = $response->getContent();
        $this->assertStringNotContainsStringIgnoringCase('WhatsApp', $content);
    }

    public function test_booking_endpoint_returns_null_whatsapp_url_when_locale_is_english(): void
    {
        app()->setLocale('en');

        $payload = [
            'service_id' => $this->service->id,
            'dentist_id' => $this->dentist->id,
            'appointment_date' => Carbon::now()->next(Carbon::MONDAY)->toDateString(),
            'appointment_time' => '10:00',
            'patient_name' => 'John Doe',
            'patient_email' => 'john@example.com',
            'patient_phone' => '+15551234567',
        ];

        $response = $this->withHeaders(['Accept-Language' => 'en'])
            ->postJson('/appointments', $payload);

        $response->assertStatus(201);
        $this->assertNull($response->json('whatsapp_url'));
    }

    public function test_booking_endpoint_returns_whatsapp_url_when_locale_is_spanish(): void
    {
        app()->setLocale('es');

        $payload = [
            'service_id' => $this->service->id,
            'dentist_id' => $this->dentist->id,
            'appointment_date' => Carbon::now()->next(Carbon::MONDAY)->toDateString(),
            'appointment_time' => '10:00',
            'patient_name' => 'Juan Perez',
            'patient_email' => 'juan@example.com',
            'patient_phone' => '+15551234567',
        ];

        $response = $this->withHeaders(['Accept-Language' => 'es'])
            ->postJson('/appointments', $payload);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('whatsapp_url'));
        $this->assertStringContainsString('wa.me', $response->json('whatsapp_url'));
    }

    public function test_admin_view_modal_hides_whatsapp_button_in_english(): void
    {
        $appointment = Appointment::factory()->create([
            'dentist_id' => $this->dentist->id,
            'service_id' => $this->service->id,
            'patient_name' => 'Alice Smith',
            'patient_phone' => '+15559876543',
        ]);

        app()->setLocale('en');

        $view = view('filament.resources.appointments.view-modal', [
            'record' => $appointment,
        ])->render();

        $this->assertStringNotContainsString('Contact patient via WhatsApp', $view);
        $this->assertStringNotContainsString('wa.me', $view);
        $this->assertStringContainsString('tel:+15559876543', $view);
    }

    public function test_admin_view_modal_shows_whatsapp_button_in_spanish(): void
    {
        $appointment = Appointment::factory()->create([
            'dentist_id' => $this->dentist->id,
            'service_id' => $this->service->id,
            'patient_name' => 'Alicia Lopez',
            'patient_phone' => '+15559876543',
        ]);

        app()->setLocale('es');

        $view = view('filament.resources.appointments.view-modal', [
            'record' => $appointment,
        ])->render();

        $this->assertStringContainsString('Contactar paciente por WhatsApp', $view);
        $this->assertStringContainsString('wa.me', $view);
    }

    public function test_admin_slide_over_drawer_hides_whatsapp_link_in_english(): void
    {
        $appointment = Appointment::factory()->create([
            'dentist_id' => $this->dentist->id,
            'service_id' => $this->service->id,
            'patient_name' => 'Bob Miller',
            'patient_phone' => '+15551112222',
        ]);

        app()->setLocale('en');

        $view = view('filament.resources.appointments.slide-over-detail', [
            'record' => $appointment,
        ])->render();

        $this->assertStringNotContainsString('wa.me', $view);
        $this->assertStringContainsString('tel:+15551112222', $view);
    }

    public function test_admin_slide_over_drawer_shows_whatsapp_link_in_spanish(): void
    {
        $appointment = Appointment::factory()->create([
            'dentist_id' => $this->dentist->id,
            'service_id' => $this->service->id,
            'patient_name' => 'Roberto Morales',
            'patient_phone' => '+15551112222',
        ]);

        app()->setLocale('es');

        $view = view('filament.resources.appointments.slide-over-detail', [
            'record' => $appointment,
        ])->render();

        $this->assertStringContainsString('wa.me', $view);
    }

    public function test_admin_edit_page_whatsapp_action_visibility_respects_locale(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $appointment = Appointment::factory()->create([
            'dentist_id' => $this->dentist->id,
            'service_id' => $this->service->id,
            'patient_phone' => '+15551112222',
            'appointment_date' => now()->next(Carbon::MONDAY)->toDateString(),
            'appointment_time' => '10:00:00',
        ]);

        // English: action hidden
        app()->setLocale('en');
        Livewire::test(EditAppointment::class, ['record' => $appointment->id])
            ->assertActionHidden('whatsapp');

        // Spanish: action visible
        app()->setLocale('es');
        Livewire::test(EditAppointment::class, ['record' => $appointment->id])
            ->assertActionVisible('whatsapp');
    }
}
