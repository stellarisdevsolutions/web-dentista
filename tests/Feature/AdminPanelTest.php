<?php

namespace Tests\Feature;

use App\Enums\UserRole;
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
        $response->assertSee('Aura Dental Studio');
        $response->assertSee('Nueva cita');
        $response->assertSee('citas hoy');
    }

    public function test_authenticated_admin_can_view_appointments_resource(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/appointments');

        $response->assertSuccessful();
    }

    public function test_service_record_can_be_created_with_unified_attributes(): void
    {
        $service = Service::create([
            'name' => 'Blanqueamiento Dental Láser',
            'slug' => 'blanqueamiento-dental-laser',
            'category' => 'Estética Dental',
            'short_description' => 'Tratamiento en una sesión',
            'description' => 'Aclara hasta 4 tonos en una hora.',
            'price' => 240.00,
            'duration_minutes' => 60,
        ]);

        $this->assertEquals('Blanqueamiento Dental Láser', $service->name);
        $this->assertEquals('Estética Dental', $service->category);
        $this->assertEquals(240.00, $service->price);
    }

    public function test_service_form_schema_renders_clean_unified_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services/create');
        $response->assertSuccessful();
        $response->assertSee('Nombre del Tratamiento');
        $response->assertSee('Categoría');
        $response->assertSee('Descripción Breve');
        $response->assertDontSee('Tabs\Tab');
    }

    public function test_appointment_create_form_renders_clean_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/appointments/create');
        $response->assertSuccessful();
        $response->assertSee('Información del Paciente');
        $response->assertSee('Nombre Completo del Paciente');
        $response->assertSee('Detalles de la Cita');
        $response->assertSee('Tratamiento / Servicio');
        $response->assertSee('Especialista Asignado');
        $response->assertSee('Estado de la Cita');
        $response->assertSee('Notas o Motivo de la Cita (Opcional)');
        $response->assertDontSee('Idioma de Preferencia');
        $response->assertDontSee('Notas Internas / Administrativas');
    }

    public function test_dentist_can_be_associated_with_specific_services(): void
    {
        $dentist = Dentist::create([
            'name' => 'Dr. Robert Martinez',
            'slug' => 'dr-robert-martinez',
            'title' => 'Ortodoncista',
            'specialty' => 'Ortodoncia Avanzada',
        ]);

        $service1 = Service::create([
            'name' => 'Brackets Zafiro',
            'slug' => 'brackets-zafiro',
            'price' => 1500.00,
        ]);

        $service2 = Service::create([
            'name' => 'Limpieza Básica',
            'slug' => 'limpieza-basica',
            'price' => 60.00,
        ]);

        $dentist->services()->attach([$service1->id, $service2->id]);

        $this->assertCount(2, $dentist->services);
        $this->assertTrue($dentist->services->contains($service1));
        $this->assertTrue($service1->dentists->contains($dentist));
    }

    public function test_admin_can_access_users_management(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertSuccessful();
    }

    public function test_receptionist_can_access_appointments_but_forbidden_from_admin_modules(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        // Can access appointments
        $responseAppointments = $this->actingAs($receptionist)->get('/admin/appointments');
        $responseAppointments->assertSuccessful();

        // Forbidden from users management
        $responseUsers = $this->actingAs($receptionist)->get('/admin/users');
        $responseUsers->assertForbidden();

        // Forbidden from CMS banners
        $responseBanners = $this->actingAs($receptionist)->get('/admin/banners');
        $responseBanners->assertForbidden();
    }

    public function test_dentist_can_access_appointments_but_forbidden_from_admin_modules(): void
    {
        $dentistUser = User::factory()->dentist()->create();

        // Can access appointments
        $responseAppointments = $this->actingAs($dentistUser)->get('/admin/appointments');
        $responseAppointments->assertSuccessful();

        // Forbidden from users management
        $responseUsers = $this->actingAs($dentistUser)->get('/admin/users');
        $responseUsers->assertForbidden();

        // Forbidden from CMS banners
        $responseBanners = $this->actingAs($dentistUser)->get('/admin/banners');
        $responseBanners->assertForbidden();
    }
}
