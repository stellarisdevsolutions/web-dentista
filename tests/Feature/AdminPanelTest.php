<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Dentists\DentistResource;
use App\Filament\Resources\Dentists\Pages\CreateDentist;
use App\Filament\Resources\Dentists\Pages\EditDentist;
use App\Filament\Resources\Dentists\Pages\ListDentists;
use App\Filament\Resources\Dentists\Schemas\DentistForm;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Pages\Login;
use Filament\Notifications\Notification;
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

    public function test_dentist_create_form_renders_clean_fields_with_time_pickers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/dentists/create');
        $response->assertSuccessful();
        $response->assertSee('Perfil Profesional e Identidad');
        $response->assertSee('Nombre Completo Profesional');
        $response->assertSee('Cargo / Título Profesional');
        $response->assertSee('Especialidad Médica Principal');
        $response->assertSee('Horarios y Turnos de Atención Semanal');
        $response->assertSee('Área Clínica y Tratamientos Asignados');
        $response->assertSee('Lunes');
        $response->assertSee('Mañana Inicio');
        $response->assertSee('Tarde Fin');
        $response->assertSee('AM');
        $response->assertSee('PM');
        $response->assertSee('--:--');
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

    public function test_appointment_create_and_edit_page_actions_and_notifications(): void
    {
        $createPage = new CreateAppointment;
        $this->assertFalse($createPage->canCreateAnother());

        $refMethod = new \ReflectionMethod($createPage, 'getRedirectUrl');
        $this->assertEquals(AppointmentResource::getUrl('index'), $refMethod->invoke($createPage));

        $refCancel = new \ReflectionMethod($createPage, 'getCancelFormAction');
        /** @var Action $cancelAction */
        $cancelAction = $refCancel->invoke($createPage);
        $this->assertEquals(AppointmentResource::getUrl('index'), $cancelAction->getUrl());

        $refCreatedNotif = new \ReflectionMethod($createPage, 'getCreatedNotification');
        /** @var Notification $createdNotif */
        $createdNotif = $refCreatedNotif->invoke($createPage);
        $this->assertEquals('Cita creada exitosamente', $createdNotif->getTitle());

        $editPage = new EditAppointment;
        $refEditRedirect = new \ReflectionMethod($editPage, 'getRedirectUrl');
        $this->assertEquals(AppointmentResource::getUrl('index'), $refEditRedirect->invoke($editPage));

        $refEditCancel = new \ReflectionMethod($editPage, 'getCancelFormAction');
        /** @var Action $editCancelAction */
        $editCancelAction = $refEditCancel->invoke($editPage);
        $this->assertEquals(AppointmentResource::getUrl('index'), $editCancelAction->getUrl());

        $refSavedNotif = new \ReflectionMethod($editPage, 'getSavedNotification');
        /** @var Notification $savedNotif */
        $savedNotif = $refSavedNotif->invoke($editPage);
        $this->assertEquals('Cita actualizada exitosamente', $savedNotif->getTitle());
    }

    public function test_global_search_result_details_with_enum_status(): void
    {
        $service = Service::factory()->create(['name' => 'Limpieza Dental']);
        $dentist = Dentist::factory()->create();

        $appointment = Appointment::factory()->create([
            'patient_name' => 'Carlos Mendoza',
            'status' => AppointmentStatus::Confirmed,
            'service_id' => $service->id,
            'dentist_id' => $dentist->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '10:00:00',
        ]);

        $title = AppointmentResource::getGlobalSearchResultTitle($appointment);
        $this->assertStringContainsString('Carlos Mendoza', $title);

        $details = AppointmentResource::getGlobalSearchResultDetails($appointment);
        $this->assertIsArray($details);
        $this->assertArrayHasKey('Estado', $details);
        $this->assertEquals('Confirmada', $details['Estado']);
    }

    public function test_dentist_create_and_edit_page_actions_and_notifications(): void
    {
        $createPage = new CreateDentist;
        $this->assertFalse($createPage->canCreateAnother());

        $refMethod = new \ReflectionMethod($createPage, 'getRedirectUrl');
        $this->assertEquals(DentistResource::getUrl('index'), $refMethod->invoke($createPage));

        $refCancel = new \ReflectionMethod($createPage, 'getCancelFormAction');
        /** @var Action $cancelAction */
        $cancelAction = $refCancel->invoke($createPage);
        $this->assertEquals(DentistResource::getUrl('index'), $cancelAction->getUrl());

        $refCreatedNotif = new \ReflectionMethod($createPage, 'getCreatedNotification');
        /** @var Notification $createdNotif */
        $createdNotif = $refCreatedNotif->invoke($createPage);
        $this->assertEquals('Especialista registrado exitosamente', $createdNotif->getTitle());

        $editPage = new EditDentist;
        $refEditRedirect = new \ReflectionMethod($editPage, 'getRedirectUrl');
        $this->assertEquals(DentistResource::getUrl('index'), $refEditRedirect->invoke($editPage));

        $refEditCancel = new \ReflectionMethod($editPage, 'getCancelFormAction');
        /** @var Action $editCancelAction */
        $editCancelAction = $refEditCancel->invoke($editPage);
        $this->assertEquals(DentistResource::getUrl('index'), $editCancelAction->getUrl());

        $refSavedNotif = new \ReflectionMethod($editPage, 'getSavedNotification');
        /** @var Notification $savedNotif */
        $savedNotif = $refSavedNotif->invoke($editPage);
        $this->assertEquals('Especialista actualizado exitosamente', $savedNotif->getTitle());
    }

    public function test_list_dentists_get_dentist_details(): void
    {
        $service = Service::factory()->create(['name' => 'Ortodoncia']);
        $dentist = Dentist::factory()->create([
            'name' => 'Dra. Lucia Torres',
            'specialty' => 'Ortodoncia',
            'phone' => '+1234567890',
        ]);
        $dentist->services()->attach($service->id);

        $listPage = new ListDentists;
        $details = $listPage->getDentistDetails($dentist->id);

        $this->assertIsArray($details);
        $this->assertEquals('Dra. Lucia Torres', $details['name']);
        $this->assertEquals('LT', $details['initials']);
        $this->assertEquals('Ortodoncia', $details['specialty']);
        $this->assertCount(1, $details['services']);
        $this->assertEquals('Ortodoncia', $details['services'][0]['name']);
    }

    public function test_dentist_time_format_and_transformations_in_12h_and_24h(): void
    {
        // 1. Verify default 12h configuration
        $this->assertEquals('12h', config('app.time_format'));

        // 2. 12h Transformation tests
        config(['app.time_format' => '12h']);

        // Afternoon conversion (DB 24h -> Input 12h, and Input 12h -> DB 24h)
        $this->assertEquals('02:00', DentistForm::formatTimeForInput('14:00', 'afternoon'));
        $this->assertEquals('14:00', DentistForm::formatTimeForDatabase('02:00', 'afternoon'));
        $this->assertEquals('06:30', DentistForm::formatTimeForInput('18:30', 'afternoon'));
        $this->assertEquals('18:30', DentistForm::formatTimeForDatabase('06:30', 'afternoon'));
        $this->assertEquals('12:00', DentistForm::formatTimeForInput('12:00', 'afternoon'));
        $this->assertEquals('12:00', DentistForm::formatTimeForDatabase('12:00', 'afternoon'));

        // Morning conversion
        $this->assertEquals('09:00', DentistForm::formatTimeForInput('09:00', 'morning'));
        $this->assertEquals('09:00', DentistForm::formatTimeForDatabase('09:00', 'morning'));
        $this->assertEquals('01:00', DentistForm::formatTimeForInput('13:00', 'morning'));
        $this->assertEquals('13:00', DentistForm::formatTimeForDatabase('01:00', 'morning'));

        // Optional / null / empty
        $this->assertNull(DentistForm::formatTimeForInput(null, 'morning'));
        $this->assertNull(DentistForm::formatTimeForInput('', 'morning'));
        $this->assertNull(DentistForm::formatTimeForDatabase(null, 'afternoon'));
        $this->assertNull(DentistForm::formatTimeForDatabase('', 'afternoon'));

        // Not working day returns null
        $this->assertNull(DentistForm::formatTimeForDatabase('02:00', 'afternoon', false));

        // 3. 24h Transformation tests
        config(['app.time_format' => '24h']);
        $this->assertEquals('14:00', DentistForm::formatTimeForInput('14:00', 'afternoon'));
        $this->assertEquals('14:00', DentistForm::formatTimeForDatabase('14:00', 'afternoon'));
        $this->assertEquals('09:00', DentistForm::formatTimeForInput('09:00', 'morning'));
        $this->assertEquals('09:00', DentistForm::formatTimeForDatabase('09:00', 'morning'));

        // Reset to 12h
        config(['app.time_format' => '12h']);
    }

    public function test_dentist_schedule_validation_rules_via_livewire(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Fails when is_working is true but no shifts are provided
        Livewire::test(CreateDentist::class)
            ->fillForm([
                'name' => 'Dr. Test Empty',
                'title' => 'Title',
                'specialty' => 'Spec',
                'slug' => 'dr-test-empty',
                'weekly_schedule' => [
                    'monday' => [
                        'is_working' => true,
                        'morning_start' => null,
                        'morning_end' => null,
                        'afternoon_start' => null,
                        'afternoon_end' => null,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasErrors(['data.weekly_schedule.monday.is_working']);

        // 2. Fails when morning_start is filled without morning_end
        Livewire::test(CreateDentist::class)
            ->fillForm([
                'name' => 'Dr. Test Incomplete Morning',
                'title' => 'Title',
                'specialty' => 'Spec',
                'slug' => 'dr-test-inc-morning',
                'weekly_schedule' => [
                    'monday' => [
                        'is_working' => true,
                        'morning_start' => '09:00',
                        'morning_end' => null,
                        'afternoon_start' => null,
                        'afternoon_end' => null,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasErrors(['data.weekly_schedule.monday.morning_end']);

        // 3. Fails when morning_end is before morning_start
        Livewire::test(CreateDentist::class)
            ->fillForm([
                'name' => 'Dr. Test Reverse Morning',
                'title' => 'Title',
                'specialty' => 'Spec',
                'slug' => 'dr-test-rev-morning',
                'weekly_schedule' => [
                    'monday' => [
                        'is_working' => true,
                        'morning_start' => '11:00',
                        'morning_end' => '09:00',
                        'afternoon_start' => null,
                        'afternoon_end' => null,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasErrors(['data.weekly_schedule.monday.morning_end']);

        // 4. Fails when afternoon_end is before afternoon_start
        Livewire::test(CreateDentist::class)
            ->fillForm([
                'name' => 'Dr. Test Reverse Afternoon',
                'title' => 'Title',
                'specialty' => 'Spec',
                'slug' => 'dr-test-rev-afternoon',
                'weekly_schedule' => [
                    'monday' => [
                        'is_working' => true,
                        'morning_start' => null,
                        'morning_end' => null,
                        'afternoon_start' => '05:00',
                        'afternoon_end' => '02:00',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasErrors(['data.weekly_schedule.monday.afternoon_end']);

        // 5. Succeeds with valid shifts and saves 24h format in database
        Livewire::test(CreateDentist::class)
            ->fillForm([
                'name' => 'Dr. Alexander Bell',
                'title' => 'Cirujano Dentista',
                'specialty' => 'Implantología',
                'slug' => 'dr-alexander-bell',
                'weekly_schedule' => [
                    'monday' => [
                        'is_working' => true,
                        'morning_start' => '09:00',
                        'morning_end' => '12:00',
                        'afternoon_start' => '02:00',
                        'afternoon_end' => '06:00',
                    ],
                    'tuesday' => [
                        'is_working' => false,
                        'morning_start' => null,
                        'morning_end' => null,
                        'afternoon_start' => null,
                        'afternoon_end' => null,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoErrors();

        $savedDentist = Dentist::where('slug', 'dr-alexander-bell')->first();
        $this->assertNotNull($savedDentist);
        $this->assertEquals('14:00', $savedDentist->weekly_schedule['monday']['afternoon_start']);
        $this->assertEquals('18:00', $savedDentist->weekly_schedule['monday']['afternoon_end']);
        $this->assertEquals('09:00', $savedDentist->weekly_schedule['monday']['morning_start']);
        $this->assertEquals('12:00', $savedDentist->weekly_schedule['monday']['morning_end']);
    }
}
