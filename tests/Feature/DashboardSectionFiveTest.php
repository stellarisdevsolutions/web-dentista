<?php

namespace Tests\Feature;

use App\Filament\Widgets\LatestAppointmentsWidget;
use App\Filament\Widgets\TodaySpecialistsWidget;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\User;
use App\Services\DashboardSpecialistsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardSectionFiveTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_specialists_widget_visibility_and_column_spans_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        $receptionist = User::factory()->receptionist()->create();
        $dentist = Dentist::factory()->create();
        $dentistUser = User::factory()->dentist()->create(['dentist_id' => $dentist->id]);

        // Admin
        $this->actingAs($admin);
        $this->assertTrue(TodaySpecialistsWidget::canView());
        $tableWidget = new LatestAppointmentsWidget;
        $specialistsWidget = new TodaySpecialistsWidget;
        $this->assertEquals(8, $tableWidget->getColumnSpan()['lg']);
        $this->assertEquals(4, $specialistsWidget->getColumnSpan()['lg']);

        // Receptionist
        $this->actingAs($receptionist);
        $this->assertTrue(TodaySpecialistsWidget::canView());
        $this->assertEquals(8, $tableWidget->getColumnSpan()['lg']);
        $this->assertEquals(4, $specialistsWidget->getColumnSpan()['lg']);

        // Dentist
        $this->actingAs($dentistUser);
        $this->assertFalse(TodaySpecialistsWidget::canView());
        $this->assertEquals(12, $tableWidget->getColumnSpan()['lg']);
    }

    public function test_specialists_service_calculates_statuses_and_in_shift_count_correctly(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 11:00:00'));
        $admin = User::factory()->admin()->create();

        // 3 dentistas
        $d1 = Dentist::factory()->create(['name' => 'Carlos Gomez', 'specialty' => 'Ortodoncia', 'order' => 1]);
        $d2 = Dentist::factory()->create(['name' => 'Mariana Ruiz', 'specialty' => 'Endodoncia', 'order' => 2]);
        $d3 = Dentist::factory()->create(['name' => 'Roberto Paez', 'specialty' => null, 'order' => 3]);

        // d1 tiene una cita en consulta hoy
        Appointment::factory()->create([
            'dentist_id' => $d1->id,
            'appointment_date' => '2026-10-15',
            'status' => 'in_progress',
        ]);

        // d2 tiene 2 citas confirmadas hoy (ninguna en consulta)
        Appointment::factory()->count(2)->create([
            'dentist_id' => $d2->id,
            'appointment_date' => '2026-10-15',
            'status' => 'confirmed',
        ]);

        // d3 no tiene citas hoy

        $service = app(DashboardSpecialistsService::class);
        $summary = $service->getSpecialistsSummary($admin);

        $this->assertCount(3, $summary->specialists);
        $this->assertEquals(2, $summary->inShiftCount); // d1 (en consulta) + d2 (disponible)

        // d1
        $this->assertEquals('Carlos Gomez', $summary->specialists[0]->name);
        $this->assertEquals('in_consultation', $summary->specialists[0]->statusKey);
        $this->assertEquals('En consulta', $summary->specialists[0]->statusLabel);
        $this->assertEquals('1 cita hoy', $summary->specialists[0]->todayAppointmentsText);
        $this->assertEquals('CG', $summary->specialists[0]->initials);

        // d2
        $this->assertEquals('Mariana Ruiz', $summary->specialists[1]->name);
        $this->assertEquals('available', $summary->specialists[1]->statusKey);
        $this->assertEquals('Disponible', $summary->specialists[1]->statusLabel);
        $this->assertEquals('2 citas hoy', $summary->specialists[1]->todayAppointmentsText);

        // d3
        $this->assertEquals('Roberto Paez', $summary->specialists[2]->name);
        $this->assertEquals('no_appointments', $summary->specialists[2]->statusKey);
        $this->assertEquals('Sin citas hoy', $summary->specialists[2]->statusLabel);
        $this->assertEquals('0 citas hoy', $summary->specialists[2]->todayAppointmentsText);
        $this->assertEquals('Odontología general', $summary->specialists[2]->specialty);
    }

    public function test_cancelled_and_other_dates_are_excluded_from_specialists(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));
        $admin = User::factory()->admin()->create();

        $d = Dentist::factory()->create(['name' => 'Lucia Salas']);

        // Cita cancelada hoy
        Appointment::factory()->create([
            'dentist_id' => $d->id,
            'appointment_date' => '2026-10-15',
            'status' => 'cancelled',
        ]);

        // Cita de ayer
        Appointment::factory()->create([
            'dentist_id' => $d->id,
            'appointment_date' => '2026-10-14',
            'status' => 'confirmed',
        ]);

        $service = app(DashboardSpecialistsService::class);
        $summary = $service->getSpecialistsSummary($admin);

        $this->assertEquals(0, $summary->specialists[0]->todayAppointmentsCount);
        $this->assertEquals('no_appointments', $summary->specialists[0]->statusKey);
        $this->assertEquals(0, $summary->inShiftCount);
    }

    public function test_specialists_empty_state(): void
    {
        $admin = User::factory()->admin()->create();

        $service = app(DashboardSpecialistsService::class);
        $summary = $service->getSpecialistsSummary($admin);

        $this->assertTrue($summary->isEmpty);
        $this->assertCount(0, $summary->specialists);
        $this->assertEquals(0, $summary->inShiftCount);
    }

    public function test_dentist_only_sees_their_own_appointments_in_table_view_data(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 09:00:00'));

        $dentistA = Dentist::factory()->create(['name' => 'Doctor A']);
        $dentistB = Dentist::factory()->create(['name' => 'Doctor B']);

        $dentistUserA = User::factory()->dentist()->create(['dentist_id' => $dentistA->id]);
        $admin = User::factory()->admin()->create();

        Appointment::factory()->create([
            'dentist_id' => $dentistA->id,
            'appointment_date' => '2026-10-15',
            'patient_name' => 'Paciente de A',
            'status' => 'confirmed',
        ]);

        Appointment::factory()->create([
            'dentist_id' => $dentistB->id,
            'appointment_date' => '2026-10-15',
            'patient_name' => 'Paciente de B',
            'status' => 'confirmed',
        ]);

        // Como Dentista A
        $this->actingAs($dentistUserA);
        $widgetA = new LatestAppointmentsWidget;
        $viewDataA = $widgetA->getViewData();

        $this->assertCount(1, $viewDataA['appointments']);
        $this->assertEquals('Paciente de A', $viewDataA['appointments'][0]->patient_name);
        $this->assertEquals(1, $viewDataA['counts']['all']);

        // Como Admin
        $this->actingAs($admin);
        $widgetAdmin = new LatestAppointmentsWidget;
        $viewDataAdmin = $widgetAdmin->getViewData();

        $this->assertCount(2, $viewDataAdmin['appointments']);
        $this->assertEquals(2, $viewDataAdmin['counts']['all']);
    }

    public function test_table_tabs_filtering_by_status(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 09:00:00'));

        $admin = User::factory()->admin()->create();

        Appointment::factory()->create([
            'appointment_date' => '2026-10-15',
            'status' => 'pending',
        ]);

        Appointment::factory()->create([
            'appointment_date' => '2026-10-15',
            'status' => 'confirmed',
        ]);

        Appointment::factory()->create([
            'appointment_date' => '2026-10-15',
            'status' => 'in_progress',
        ]);

        $this->actingAs($admin);

        // Tab 'all': 3 citas
        $widget = new LatestAppointmentsWidget;
        $widget->setTab('all');
        $viewData = $widget->getViewData();
        $this->assertCount(3, $viewData['appointments']);
        $this->assertEquals(3, $viewData['counts']['all']);
        $this->assertEquals(2, $viewData['counts']['confirmed']);
        $this->assertEquals(1, $viewData['counts']['pending']);

        // Tab 'confirmed': 2 citas (confirmed + in_progress)
        $widget->setTab('confirmed');
        $viewData = $widget->getViewData();
        $this->assertCount(2, $viewData['appointments']);

        // Tab 'pending': 1 cita
        $widget->setTab('pending');
        $viewData = $widget->getViewData();
        $this->assertCount(1, $viewData['appointments']);
    }

    public function test_table_renders_visually_without_individual_action_buttons(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 09:00:00'));

        $admin = User::factory()->admin()->create();

        Appointment::factory()->create([
            'appointment_date' => '2026-10-15',
            'patient_name' => 'Ana Morales',
            'status' => 'confirmed',
        ]);

        $this->actingAs($admin);

        Livewire::test(LatestAppointmentsWidget::class)
            ->assertSee('Ana Morales')
            ->assertSee('Confirmados')
            ->assertSee('Sin confirmar')
            ->assertDontSee('wire:click="startConsultation"')
            ->assertDontSee('wire:click="cancelAppointment"');
    }
}
