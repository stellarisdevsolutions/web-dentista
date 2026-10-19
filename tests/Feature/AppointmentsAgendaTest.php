<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppointmentsAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_page_renders_with_title_and_four_tabs(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $today = Carbon::today()->toDateString();

        Appointment::factory()->create(['status' => 'pending', 'appointment_date' => $today]);
        Appointment::factory()->create(['status' => 'confirmed', 'appointment_date' => $today]);
        Appointment::factory()->create(['status' => 'cancelled', 'appointment_date' => $today]);

        $response = $this->actingAs($admin)->get('/admin/appointments');
        $response->assertSuccessful();
        $response->assertSee('Agenda');
        $response->assertSee('Todas');
        $response->assertSee('Pendientes');
        $response->assertSee('Confirmadas');
        $response->assertSee('Canceladas');
    }

    public function test_dentist_sees_mis_citas_title_and_no_create_button(): void
    {
        $dentist = Dentist::factory()->create();
        $dentistUser = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
        ]);

        $response = $this->actingAs($dentistUser)->get('/admin/appointments');
        $response->assertSuccessful();
        $response->assertSee('Mis citas');
        $response->assertDontSee('/admin/appointments/create');
    }

    public function test_dentist_only_queries_their_own_appointments(): void
    {
        $today = Carbon::today()->toDateString();
        $dentistA = Dentist::factory()->create(['name' => 'Especialista A']);
        $dentistB = Dentist::factory()->create(['name' => 'Especialista B']);

        $dentistUserA = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentistA->id,
        ]);

        $myAppointment = Appointment::factory()->create([
            'dentist_id' => $dentistA->id,
            'patient_name' => 'Paciente Propio',
            'appointment_date' => $today,
        ]);

        $otherAppointment = Appointment::factory()->create([
            'dentist_id' => $dentistB->id,
            'patient_name' => 'Paciente Ajeno',
            'appointment_date' => $today,
        ]);

        Livewire::actingAs($dentistUserA)
            ->test(ListAppointments::class)
            ->assertCanSeeTableRecords([$myAppointment])
            ->assertCanNotSeeTableRecords([$otherAppointment]);
    }

    public function test_tabs_filter_by_correct_status_scopes(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $today = Carbon::today()->toDateString();

        $pending = Appointment::factory()->create(['status' => 'pending', 'patient_name' => 'Paciente Pendiente', 'appointment_date' => $today]);
        $confirmed = Appointment::factory()->create(['status' => 'confirmed', 'patient_name' => 'Paciente Confirmado', 'appointment_date' => $today]);
        $inProgress = Appointment::factory()->create(['status' => 'in_progress', 'patient_name' => 'Paciente En Sala', 'appointment_date' => $today]);
        $cancelled = Appointment::factory()->create(['status' => 'cancelled', 'patient_name' => 'Paciente Cancelado', 'appointment_date' => $today]);

        // Tab "pending"
        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->set('activeTab', 'pending')
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$confirmed, $inProgress, $cancelled]);

        // Tab "confirmed" includes both confirmed and in_progress
        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->set('activeTab', 'confirmed')
            ->assertCanSeeTableRecords([$confirmed, $inProgress])
            ->assertCanNotSeeTableRecords([$pending, $cancelled]);

        // Tab "cancelled"
        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->set('activeTab', 'cancelled')
            ->assertCanSeeTableRecords([$cancelled])
            ->assertCanNotSeeTableRecords([$pending, $confirmed, $inProgress]);
    }

    public function test_receptionist_cannot_complete_appointment_in_server(): void
    {
        $receptionist = User::factory()->create(['role' => UserRole::Receptionist]);
        $appointment = Appointment::factory()->create(['status' => 'in_progress']);

        $this->assertFalse($receptionist->can('complete', $appointment));
    }

    public function test_dentist_cannot_cancel_nor_reschedule(): void
    {
        $dentist = Dentist::factory()->create();
        $dentistUser = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
        ]);
        $appointment = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'status' => 'confirmed',
        ]);

        $this->assertFalse($dentistUser->can('cancel', $appointment));
        $this->assertFalse($dentistUser->can('reschedule', $appointment));
        $this->assertTrue($dentistUser->can('complete', $appointment));
    }

    public function test_reschedule_prevents_time_slot_conflict_for_same_dentist(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $dentist = Dentist::factory()->create();

        $existing = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'appointment_date' => '2026-10-15',
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        $toReschedule = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'appointment_date' => '2026-10-16',
            'appointment_time' => '11:00',
            'status' => 'confirmed',
        ]);

        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->set('selectedDate', '2026-10-16')
            ->callTableAction('reschedule', $toReschedule, data: [
                'appointment_date' => '2026-10-15',
                'appointment_time' => '10:00',
            ])
            ->assertHasNoTableActionErrors();

        // The date/time shouldn't have changed due to conflict
        $toReschedule->refresh();
        $this->assertEquals('2026-10-16', $toReschedule->appointment_date->toDateString());
        $this->assertEquals('11:00', $toReschedule->appointment_time);
    }

    public function test_native_search_queries_all_dates_and_ignores_selected_day(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $futureApp = Appointment::factory()->create([
            'patient_name' => 'Carlos Mendoza',
            'patient_phone' => '+591 700-12345',
            'appointment_date' => '2026-12-25',
        ]);

        $pastApp = Appointment::factory()->create([
            'patient_name' => 'Carlos Mendoza',
            'patient_phone' => '+591 700-12345',
            'appointment_date' => '2026-01-01',
        ]);

        $otherApp = Appointment::factory()->create([
            'patient_name' => 'Maria Lopez',
            'appointment_date' => Carbon::today()->toDateString(),
        ]);

        // Vista normal sin búsqueda: solo la cita de hoy es visible
        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->assertCanSeeTableRecords([$otherApp])
            ->assertCanNotSeeTableRecords([$futureApp, $pastApp]);

        // Búsqueda nativa de tabla: encuentra a Carlos en todas las fechas, ignorando el día
        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->searchTable('Carlos')
            ->assertCanSeeTableRecords([$futureApp, $pastApp])
            ->assertCanNotSeeTableRecords([$otherApp]);
    }

    public function test_native_search_orders_upcoming_first_then_past(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $today = Carbon::today()->toDateString();

        $nearFuture = Appointment::factory()->create([
            'patient_name' => 'Roberto Gomez',
            'appointment_date' => Carbon::parse($today)->addDays(3)->toDateString(),
            'appointment_time' => '10:00',
        ]);

        $farFuture = Appointment::factory()->create([
            'patient_name' => 'Roberto Gomez',
            'appointment_date' => Carbon::parse($today)->addDays(10)->toDateString(),
            'appointment_time' => '09:00',
        ]);

        $recentPast = Appointment::factory()->create([
            'patient_name' => 'Roberto Gomez',
            'appointment_date' => Carbon::parse($today)->subDays(2)->toDateString(),
            'appointment_time' => '14:00',
        ]);

        $olderPast = Appointment::factory()->create([
            'patient_name' => 'Roberto Gomez',
            'appointment_date' => Carbon::parse($today)->subDays(15)->toDateString(),
            'appointment_time' => '11:00',
        ]);

        $test = Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->searchTable('Roberto');

        // Verifica que los registros se muestran en orden: próximas primero (más cercanas primero), luego pasadas (más recientes primero)
        $test->assertCanSeeTableRecords([$nearFuture, $farFuture, $recentPast, $olderPast], inOrder: true);
    }

    public function test_native_search_by_phone_ignores_spaces_and_symbols(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $app = Appointment::factory()->create([
            'patient_name' => 'Elena Vasquez',
            'patient_phone' => '+591 700-12345',
            'appointment_date' => '2026-12-25',
        ]);

        $other = Appointment::factory()->create([
            'patient_name' => 'Otro Paciente',
            'patient_phone' => '+591 711-99999',
            'appointment_date' => Carbon::today()->toDateString(),
        ]);

        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->searchTable('70012345')
            ->assertCanSeeTableRecords([$app])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_user_without_permission_cannot_see_edit_button(): void
    {
        $dentist = Dentist::factory()->create();
        $dentistUser = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
        ]);
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $appointment = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'appointment_date' => Carbon::today()->toDateString(),
        ]);

        // El dentista no ve el botón de editar en la fila
        Livewire::actingAs($dentistUser)
            ->test(ListAppointments::class)
            ->assertTableActionHidden('edit', $appointment);

        // El administrador sí ve el botón de editar
        Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->assertTableActionVisible('edit', $appointment);
    }

    public function test_row_clicking_opens_slide_over_detail_and_does_not_navigate_to_edit_url(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $appointment = Appointment::factory()->create([
            'appointment_date' => Carbon::today()->toDateString(),
            'code' => 'CIT-2026-TEST1',
        ]);

        $test = Livewire::actingAs($admin)
            ->test(ListAppointments::class);

        // Row clicks are handled by a client-side Alpine drawer, not a Filament action
        $table = $test->instance()->getTable();
        $this->assertNull($table->getRecordAction($appointment));
        $this->assertNull($table->getRecordUrl($appointment));

        // The Livewire method that the Alpine drawer calls must return appointment data
        $details = $test->instance()->getAppointmentDetails($appointment->id);
        $this->assertNotNull($details);
        $this->assertEquals($appointment->id, $details['id']);
        $this->assertEquals('CIT-2026-TEST1', $details['code']);
    }

    public function test_clear_all_filters_resets_date_to_today_dentist_tab_and_search(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $dentist = Dentist::factory()->create();
        $pastDate = Carbon::today()->subDays(5)->toDateString();
        $today = Carbon::today()->toDateString();

        $test = Livewire::actingAs($admin)
            ->test(ListAppointments::class)
            ->set('dentistId', $dentist->id)
            ->set('activeTab', 'cancelled')
            ->set('selectedDate', $pastDate)
            ->set('tableSearch', 'Consulta');

        $this->assertTrue($test->instance()->hasActiveFilters());

        $test->call('clearAllFilters');

        $this->assertEquals($today, $test->get('selectedDate'));
        $this->assertNull($test->get('dentistId'));
        $this->assertEquals('all', $test->get('activeTab'));
        $this->assertEquals('', $test->get('tableSearch'));
        $this->assertFalse($test->instance()->hasActiveFilters());
        $test->assertDispatched('agenda-filters-cleared');
    }

    public function test_reschedule_initial_data_and_slot_generation(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $dentist = Dentist::factory()->create([
            'is_active' => true,
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '18:00:00',
        ]);
        $service = Service::factory()->create([
            'is_active' => true,
            'duration_minutes' => 30,
        ]);
        $service->dentists()->attach($dentist);

        $test = Livewire::actingAs($admin)
            ->test(ListAppointments::class);

        $catalogs = $test->instance()->getRescheduleInitialData();
        $this->assertArrayHasKey('services', $catalogs);
        $this->assertArrayHasKey('dentists', $catalogs);
        $this->assertFalse($catalogs['is_dentist_user']);

        // Check slots generation for next Monday
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $slotsData = $test->instance()->getRescheduleAvailableSlots($nextMonday, $dentist->id, $service->id);

        $this->assertTrue($slotsData['has_available_slots']);
        $this->assertNotEmpty($slotsData['morning_slots']);
        $this->assertNotEmpty($slotsData['afternoon_slots']);
        $this->assertEquals('09:00 AM', $slotsData['morning_slots'][0]['label']);
    }

    public function test_drawer_reschedule_with_treatment_and_specialist_change(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $dentistA = Dentist::factory()->create(['is_active' => true]);
        $dentistB = Dentist::factory()->create([
            'is_active' => true,
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '18:00:00',
        ]);

        $service1 = Service::factory()->create(['duration_minutes' => 30]);
        $service2 = Service::factory()->create(['duration_minutes' => 60]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $appointment = Appointment::factory()->create([
            'dentist_id' => $dentistA->id,
            'service_id' => $service1->id,
            'appointment_date' => Carbon::today()->toDateString(),
            'appointment_time' => '10:00',
            'duration_minutes' => 30,
        ]);

        $test = Livewire::actingAs($admin)
            ->test(ListAppointments::class);

        $result = $test->instance()->drawerReschedule(
            $appointment->id,
            $nextMonday,
            '15:00',
            $service2->id,
            $dentistB->id
        );

        $this->assertTrue($result['success']);
        $appointment->refresh();
        $this->assertEquals($nextMonday, $appointment->appointment_date->toDateString());
        $this->assertEquals('15:00', substr($appointment->appointment_time, 0, 5));
        $this->assertEquals($service2->id, $appointment->service_id);
        $this->assertEquals($dentistB->id, $appointment->dentist_id);
        $this->assertEquals(60, $appointment->duration_minutes);
    }

    public function test_drawer_reschedule_with_unified_slots_strictly_uses_qualified_dentists_and_assigns_available_specialist(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $dentistBusy = Dentist::factory()->create([
            'is_active' => true,
            'working_days' => ['monday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
        ]);

        $dentistFree = Dentist::factory()->create([
            'is_active' => true,
            'working_days' => ['monday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
        ]);

        $dentistOtherService = Dentist::factory()->create([
            'is_active' => true,
            'working_days' => ['tuesday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
        ]);

        $serviceTarget = Service::factory()->create(['duration_minutes' => 45]);
        $serviceTarget->dentists()->attach([$dentistBusy->id, $dentistFree->id]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $nextTuesday = Carbon::now()->next(Carbon::TUESDAY)->toDateString();

        // Mark dentistBusy as busy on next Monday at 10:00
        Appointment::factory()->create([
            'dentist_id' => $dentistBusy->id,
            'service_id' => $serviceTarget->id,
            'appointment_date' => $nextMonday,
            'appointment_time' => '10:00',
            'duration_minutes' => 45,
            'status' => 'confirmed',
        ]);

        $appointmentToReschedule = Appointment::factory()->create([
            'dentist_id' => $dentistBusy->id,
            'service_id' => $serviceTarget->id,
            'appointment_date' => Carbon::today()->toDateString(),
            'appointment_time' => '14:00',
            'duration_minutes' => 45,
            'status' => 'confirmed',
        ]);

        $test = Livewire::actingAs($admin)->test(ListAppointments::class);

        // 1. Verify Tuesday has NO slots for serviceTarget (since dentistOtherService is not attached to serviceTarget)
        $tuesdaySlots = $test->instance()->getRescheduleAvailableSlots($nextTuesday, null, $serviceTarget->id, $appointmentToReschedule->id);
        $this->assertFalse($tuesdaySlots['has_available_slots']);

        // 2. Reschedule with null dentistId ("Primer especialista disponible") at 10:00 on Monday
        // Since dentistBusy is booked at 10:00, it MUST automatically assign dentistFree!
        $res = $test->instance()->drawerReschedule(
            $appointmentToReschedule->id,
            $nextMonday,
            '10:00',
            $serviceTarget->id,
            null
        );

        $this->assertTrue($res['success']);
        $appointmentToReschedule->refresh();
        $this->assertEquals($dentistFree->id, $appointmentToReschedule->dentist_id);
    }

    public function test_drawer_reschedule_rejects_unqualified_dentist(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $qualifiedDentist = Dentist::factory()->create(['is_active' => true]);
        $unqualifiedDentist = Dentist::factory()->create(['is_active' => true]);

        $service = Service::factory()->create(['duration_minutes' => 45]);
        $service->dentists()->attach([$qualifiedDentist->id]);

        $appointment = Appointment::factory()->create([
            'dentist_id' => $qualifiedDentist->id,
            'service_id' => $service->id,
        ]);

        $test = Livewire::actingAs($admin)->test(ListAppointments::class);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        // Check slots call returns error
        $slots = $test->instance()->getRescheduleAvailableSlots($nextMonday, $unqualifiedDentist->id, $service->id, $appointment->id);
        $this->assertFalse($slots['has_available_slots']);
        $this->assertEquals('El especialista seleccionado no realiza este tratamiento.', $slots['error']);

        // Check drawerReschedule rejects with error
        $res = $test->instance()->drawerReschedule(
            $appointment->id,
            $nextMonday,
            '10:00',
            $service->id,
            $unqualifiedDentist->id
        );

        $this->assertFalse($res['success']);
        $this->assertEquals('El especialista seleccionado no realiza este tratamiento.', $res['error']);
    }
}
