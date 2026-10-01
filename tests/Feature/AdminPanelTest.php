<?php

namespace Tests\Feature;

use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_are_redirected_to_filament_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_via_login_page(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'admin@admin.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_admin_can_access_filament_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_authenticated_admin_can_view_appointments_resource(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/appointments');

        $response->assertSuccessful();
    }

    public function test_bilingual_accessor_delivers_current_locale_content(): void
    {
        $service = Service::create([
            'name_en' => 'Laser Teeth Whitening',
            'name_es' => 'Blanqueamiento Dental Láser',
            'slug' => 'laser-teeth-whitening',
            'price' => 240.00,
            'duration_minutes' => 60,
        ]);

        app()->setLocale('en');
        $this->assertEquals('Laser Teeth Whitening', $service->name);

        app()->setLocale('es');
        $this->assertEquals('Blanqueamiento Dental Láser', $service->name);
    }

    public function test_records_can_be_created_with_spanish_only(): void
    {
        $service = Service::create([
            'name_es' => 'Ortodoncia Invisible',
            'slug' => 'ortodoncia-invisible',
            'price' => 1200.00,
        ]);

        $this->assertNull($service->name_en);
        $this->assertEquals('Ortodoncia Invisible', $service->name);

        // Even if locale is switched to en, fallback returns Spanish because it was the only language supplied
        app()->setLocale('en');
        $this->assertEquals('Ortodoncia Invisible', $service->name);
    }

    public function test_records_can_be_created_with_english_only(): void
    {
        $service = Service::create([
            'name_en' => 'Root Canal Therapy',
            'slug' => 'root-canal-therapy',
            'price' => 450.00,
        ]);

        $this->assertNull($service->name_es);

        app()->setLocale('en');
        $this->assertEquals('Root Canal Therapy', $service->name);

        // Even if locale is switched to es, fallback returns English because it was the only language supplied
        app()->setLocale('es');
        $this->assertEquals('Root Canal Therapy', $service->name);
    }

    public function test_form_schema_adapts_to_configured_locale(): void
    {
        $user = User::factory()->create();

        // When configured in Spanish, direct Spanish fields are rendered without tabs
        app()->setLocale('es');
        $responseEs = $this->actingAs($user)->get('/admin/services/create');
        $responseEs->assertSuccessful();
        $responseEs->assertSee('Nombre del Tratamiento');
        $responseEs->assertDontSee('Tabs\Tab');

        // When configured in English, direct English fields are rendered without tabs
        app()->setLocale('en');
        $responseEn = $this->actingAs($user)->get('/admin/services/create');
        $responseEn->assertSuccessful();
        $responseEn->assertSee('Treatment Name');
    }

    public function test_appointment_create_form_adapts_to_configured_locale(): void
    {
        $user = User::factory()->create();

        // In Spanish
        app()->setLocale('es');
        $responseEs = $this->actingAs($user)->get('/admin/appointments/create');
        $responseEs->assertSuccessful();
        $responseEs->assertSee('Información del Paciente');
        $responseEs->assertSee('Nombre Completo del Paciente');
        $responseEs->assertSee('Detalles de la Cita');
        $responseEs->assertSee('Tratamiento / Servicio');
        $responseEs->assertSee('Especialista Asignado');
        $responseEs->assertSee('Estado de la Cita');
        $responseEs->assertSee('Notas o Motivo de la Cita (Opcional)');
        $responseEs->assertDontSee('Idioma de Preferencia');
        $responseEs->assertDontSee('Notas Internas / Administrativas');

        // In English
        app()->setLocale('en');
        $responseEn = $this->actingAs($user)->get('/admin/appointments/create');
        $responseEn->assertSuccessful();
        $responseEn->assertSee('Patient Information');
        $responseEn->assertSee('Patient Full Name');
        $responseEn->assertSee('Appointment Details');
        $responseEn->assertSee('Notes or Reason for Visit (Optional)');
        $responseEn->assertDontSee('Preferred Language');
        $responseEn->assertDontSee('Internal Clinical / Administrative Notes');
    }

    public function test_dentist_can_be_associated_with_specific_services(): void
    {
        $dentist = Dentist::create([
            'name' => 'Dr. Robert Martinez',
            'slug' => 'dr-robert-martinez',
            'title_es' => 'Ortodoncista',
            'specialty_es' => 'Ortodoncia Avanzada',
        ]);

        $service1 = Service::create([
            'name_es' => 'Brackets Zafiro',
            'slug' => 'brackets-zafiro',
            'price' => 1500.00,
        ]);

        $service2 = Service::create([
            'name_es' => 'Limpieza Básica',
            'slug' => 'limpieza-basica',
            'price' => 60.00,
        ]);

        $dentist->services()->attach([$service1->id, $service2->id]);

        $this->assertCount(2, $dentist->services);
        $this->assertTrue($dentist->services->contains($service1));
        $this->assertTrue($service1->dentists->contains($dentist));
    }
}
