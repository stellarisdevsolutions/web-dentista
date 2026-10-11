<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Widgets\KpiCardsWidget;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use App\Services\DashboardKpiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KpiCardsWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_correct_counts_by_status_excluding_cancelled(): void
    {
        Carbon::setTestNow('2026-10-11 10:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // 2 confirmadas hoy
        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
            'duration_minutes' => 60,
        ]);
        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'in_progress',
            'duration_minutes' => 30,
        ]);

        // 1 pendiente hoy
        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'pending',
            'duration_minutes' => 45,
        ]);

        // 2 canceladas hoy (deben excluirse de programadas y confirmadas)
        Appointment::factory()->count(2)->create([
            'appointment_date' => Carbon::today(),
            'status' => 'cancelled',
            'duration_minutes' => 60,
        ]);

        $service = app(DashboardKpiService::class);
        $data = $service->getKpiData($admin);

        // Programadas (no canceladas): 2 confirmadas/en sala + 1 pendiente = 3
        $this->assertSame(3, $data->scheduledCount);
        // Confirmadas/en sala/completada: 2
        $this->assertSame(2, $data->confirmedCount);
        $this->assertSame('de 3 programadas', $data->confirmedSubtitle);
        // Pendiente: 1
        $this->assertSame(1, $data->pendingCount);
        $this->assertSame('Requieren confirmación de recepción', $data->pendingSubtitle);
    }

    public function test_dentist_only_counts_their_own_appointments_and_sees_role_titles(): void
    {
        Carbon::setTestNow('2026-10-11 10:00:00');

        $dentistA = Dentist::factory()->create(['name' => 'Dr. A', 'is_active' => true]);
        $dentistB = Dentist::factory()->create(['name' => 'Dr. B', 'is_active' => true]);

        $userDentistA = User::factory()->create([
            'name' => 'Dentista A',
            'role' => UserRole::Dentist,
            'dentist_id' => $dentistA->id,
        ]);

        // Citas Dentist A hoy: 2 confirmadas
        Appointment::factory()->count(2)->create([
            'dentist_id' => $dentistA->id,
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
            'duration_minutes' => 60,
        ]);

        // Citas Dentist B hoy: 5 confirmadas (no deben sumarse al Dentista A)
        Appointment::factory()->count(5)->create([
            'dentist_id' => $dentistB->id,
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
            'duration_minutes' => 60,
        ]);

        $service = app(DashboardKpiService::class);
        $data = $service->getKpiData($userDentistA);

        $this->assertSame('MIS CITAS CONFIRMADAS HOY', $data->confirmedTitle);
        $this->assertSame('MI OCUPACIÓN HOY', $data->occupancyTitle);
        $this->assertSame(2, $data->confirmedCount);
        $this->assertSame(2, $data->scheduledCount);

        // Renderizado en Livewire
        Livewire::actingAs($userDentistA)
            ->test(KpiCardsWidget::class)
            ->assertSee('MIS CITAS CONFIRMADAS HOY')
            ->assertSee('MI OCUPACIÓN HOY');
    }

    public function test_occupancy_calculation_with_known_durations_division_by_zero_and_cap_at_100(): void
    {
        Carbon::setTestNow('2026-10-11 10:00:00');

        $dentist = Dentist::factory()->create([
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00', // 8 horas = 480 minutos
            'is_active' => true,
        ]);

        $dentistUser = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
        ]);

        // Caso 1: 240 minutos reservados de 480 minutos disponibles = 50%
        Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'appointment_date' => Carbon::today(),
            'duration_minutes' => 240,
            'status' => 'confirmed',
        ]);

        $service = app(DashboardKpiService::class);
        $data = $service->getKpiData($dentistUser);

        $this->assertSame(50, $data->occupancyPercentage);
        $this->assertSame(50, $data->freePercentage);

        // Caso 2: Citas sobrecargadas superando los 480 minutos (ej. 600 min) -> limitado a 100%
        Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'appointment_date' => Carbon::today(),
            'duration_minutes' => 360, // 240 + 360 = 600 min
            'status' => 'confirmed',
        ]);

        $dataOverbooked = $service->getKpiData($dentistUser);
        $this->assertSame(100, $dataOverbooked->occupancyPercentage);
        $this->assertSame(0, $dataOverbooked->freePercentage);

        // Caso 3: División por cero evitada cuando availableMinutes = 0
        $dentistZero = Dentist::factory()->create([
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '09:00:00', // 0 minutos
            'is_active' => true,
        ]);
        $userZero = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentistZero->id,
        ]);
        $dataZero = $service->getKpiData($userZero);
        $this->assertIsInt($dataZero->occupancyPercentage);
        $this->assertGreaterThanOrEqual(0, $dataZero->occupancyPercentage);
    }

    public function test_late_night_appointment_at_23_30_falls_on_correct_day(): void
    {
        Carbon::setTestNow('2026-10-11 23:30:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'appointment_time' => '23:30',
            'duration_minutes' => 45,
            'status' => 'confirmed',
        ]);

        $service = app(DashboardKpiService::class);
        $data = $service->getKpiData($admin);

        $this->assertSame(1, $data->confirmedCount);
        $this->assertSame(1, $data->scheduledCount);
    }

    public function test_empty_states_display_expected_friendly_texts(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // Sin citas creadas
        $service = app(DashboardKpiService::class);
        $data = $service->getKpiData($admin);

        $this->assertSame(0, $data->scheduledCount);
        $this->assertSame('Sin citas programadas hoy', $data->confirmedSubtitle);
        $this->assertSame(0, $data->pendingCount);
        $this->assertSame('Todo confirmado', $data->pendingSubtitle);

        Livewire::actingAs($admin)
            ->test(KpiCardsWidget::class)
            ->assertSee('Sin citas programadas hoy')
            ->assertSee('Todo confirmado');
    }

    public function test_active_services_chips_display_top_3_and_more_badge(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // Crear 5 servicios activos
        $services = Service::factory()->count(5)->create(['is_active' => true]);

        // Dar citas al servicio 0 y 1
        Appointment::factory()->count(3)->create([
            'service_id' => $services[0]->id,
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
        ]);
        Appointment::factory()->count(2)->create([
            'service_id' => $services[1]->id,
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
        ]);

        $kpiService = app(DashboardKpiService::class);
        $data = $kpiService->getKpiData($admin);

        $this->assertCount(3, $data->topServices);
        $this->assertSame(2, $data->remainingServicesCount); // 5 - 3 = 2
        $this->assertSame($services[0]->name, $data->topServices[0]);

        Livewire::actingAs($admin)
            ->test(KpiCardsWidget::class)
            ->assertSee('+2');
    }

    public function test_render_empty_pending_state_shows_al_dia_chip_and_no_reminders_button(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(KpiCardsWidget::class)
            ->assertSee('Al día')
            ->assertSee('No hay recordatorios pendientes')
            ->assertDontSee('Enviar recordatorios');
    }

    public function test_render_singular_scheduled_appointment_shows_de_1_programada(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
            'duration_minutes' => 45,
        ]);

        $service = app(DashboardKpiService::class);
        $data = $service->getKpiData($admin);
        $this->assertSame('de 1 programada', $data->confirmedSubtitle);

        Livewire::actingAs($admin)
            ->test(KpiCardsWidget::class)
            ->assertSee('de 1 programada');
    }
}
