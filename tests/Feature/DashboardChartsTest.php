<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use App\Services\DashboardChartService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardChartsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_demand_chart_always_returns_7_days_with_zeros_for_empty_days(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));

        $service = app(DashboardChartService::class);
        $user = User::factory()->admin()->create();

        // Crear una cita hoy y otra hace 2 días
        Appointment::factory()->create([
            'appointment_date' => '2026-10-15',
            'status' => 'confirmed',
        ]);
        Appointment::factory()->create([
            'appointment_date' => '2026-10-13',
            'status' => 'pending',
        ]);

        $data = $service->getDemandChartData($user);

        $this->assertCount(7, $data->labels);
        $this->assertCount(7, $data->counts);
        $this->assertCount(7, $data->backgroundColors);

        // Hace 6, 5, 4, 3, 2, 1 días y hoy
        // 2026-10-13 es index 4 (hace 2 días)
        // 2026-10-15 es index 6 (hoy)
        $this->assertEquals(0, $data->counts[0]); // 2026-10-09
        $this->assertEquals(0, $data->counts[1]); // 2026-10-10
        $this->assertEquals(0, $data->counts[2]); // 2026-10-11
        $this->assertEquals(0, $data->counts[3]); // 2026-10-12
        $this->assertEquals(1, $data->counts[4]); // 2026-10-13
        $this->assertEquals(0, $data->counts[5]); // 2026-10-14
        $this->assertEquals(1, $data->counts[6]); // 2026-10-15

        // Colores: anteriores #7DD3FC, hoy #0A1730
        $this->assertEquals('#7DD3FC', $data->backgroundColors[0]);
        $this->assertEquals('#7DD3FC', $data->backgroundColors[5]);
        $this->assertEquals('#0A1730', $data->backgroundColors[6]);

        $this->assertFalse($data->isEmpty);
        $this->assertEquals(2, $data->totalAppointments);
    }

    public function test_cancelled_appointments_are_excluded_from_both_charts(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));

        $clinicalService = Service::factory()->create(['name' => 'Ortodoncia']);
        $user = User::factory()->admin()->create();

        // Cita cancelada hoy
        Appointment::factory()->create([
            'service_id' => $clinicalService->id,
            'appointment_date' => '2026-10-15',
            'status' => 'cancelled',
        ]);

        $chartService = app(DashboardChartService::class);
        $demandData = $chartService->getDemandChartData($user);
        $distributionData = $chartService->getDistributionChartData($user);

        // No debe contar la cancelada
        $this->assertEquals(0, $demandData->totalAppointments);
        $this->assertTrue($demandData->isEmpty);

        $this->assertEquals(0, $distributionData->totalAppointments);
        $this->assertTrue($distributionData->isEmpty);
    }

    public function test_dentist_only_sees_their_own_appointments(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));

        $dentistA = Dentist::factory()->create(['name' => 'Dra. Ana']);
        $dentistB = Dentist::factory()->create(['name' => 'Dr. Bruno']);

        $dentistUser = User::factory()->dentist()->create(['dentist_id' => $dentistA->id]);
        $adminUser = User::factory()->admin()->create();

        $serviceModel = Service::factory()->create(['name' => 'Limpieza']);

        // 2 citas de Dra. Ana y 3 citas de Dr. Bruno
        Appointment::factory()->count(2)->create([
            'dentist_id' => $dentistA->id,
            'service_id' => $serviceModel->id,
            'appointment_date' => '2026-10-15',
            'status' => 'confirmed',
        ]);
        Appointment::factory()->count(3)->create([
            'dentist_id' => $dentistB->id,
            'service_id' => $serviceModel->id,
            'appointment_date' => '2026-10-15',
            'status' => 'confirmed',
        ]);

        $chartService = app(DashboardChartService::class);

        // Dentista solo ve 2
        $dentistDemand = $chartService->getDemandChartData($dentistUser);
        $dentistDist = $chartService->getDistributionChartData($dentistUser);
        $this->assertEquals(2, $dentistDemand->totalAppointments);
        $this->assertEquals(2, $dentistDist->totalAppointments);

        // Admin ve las 5
        $adminDemand = $chartService->getDemandChartData($adminUser);
        $adminDist = $chartService->getDistributionChartData($adminUser);
        $this->assertEquals(5, $adminDemand->totalAppointments);
        $this->assertEquals(5, $adminDist->totalAppointments);
    }

    public function test_top_4_services_plus_others_sums_exactly_to_100_percent(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));
        $user = User::factory()->admin()->create();

        // Crear 6 servicios con distintos volúmenes
        $s1 = Service::factory()->create(['name' => 'Servicio 1']); // 10
        $s2 = Service::factory()->create(['name' => 'Servicio 2']); // 7
        $s3 = Service::factory()->create(['name' => 'Servicio 3']); // 4
        $s4 = Service::factory()->create(['name' => 'Servicio 4']); // 3
        $s5 = Service::factory()->create(['name' => 'Servicio 5']); // 2
        $s6 = Service::factory()->create(['name' => 'Servicio 6']); // 1

        Appointment::factory()->count(10)->create(['service_id' => $s1->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);
        Appointment::factory()->count(7)->create(['service_id' => $s2->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);
        Appointment::factory()->count(4)->create(['service_id' => $s3->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);
        Appointment::factory()->count(3)->create(['service_id' => $s4->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);
        Appointment::factory()->count(2)->create(['service_id' => $s5->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);
        Appointment::factory()->count(1)->create(['service_id' => $s6->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);

        $chartService = app(DashboardChartService::class);
        $data = $chartService->getDistributionChartData($user);

        // Debe haber 5 elementos (Top 4 + Otros)
        $this->assertCount(5, $data->labels);
        $this->assertEquals('Otros', $data->labels[4]);
        $this->assertEquals(3, $data->counts[4]); // s5 (2) + s6 (1) = 3
        $this->assertEquals('#CFE3F4', $data->backgroundColors[4]);

        // Los porcentajes deben sumar exactamente 100
        $this->assertEquals(100, array_sum($data->percentages));
    }

    public function test_fewer_than_four_services_does_not_show_others(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));
        $user = User::factory()->admin()->create();

        $s1 = Service::factory()->create(['name' => 'Servicio 1']);
        $s2 = Service::factory()->create(['name' => 'Servicio 2']);

        Appointment::factory()->count(3)->create(['service_id' => $s1->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);
        Appointment::factory()->count(2)->create(['service_id' => $s2->id, 'appointment_date' => '2026-10-10', 'status' => 'confirmed']);

        $chartService = app(DashboardChartService::class);
        $data = $chartService->getDistributionChartData($user);

        $this->assertCount(2, $data->labels);
        $this->assertNotContains('Otros', $data->labels);
        $this->assertEquals(100, array_sum($data->percentages));
    }

    public function test_late_night_appointment_at_2330_falls_on_correct_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 23:45:00'));

        $user = User::factory()->admin()->create();

        // Cita programada para hoy a las 23:30
        Appointment::factory()->create([
            'appointment_date' => '2026-10-15',
            'appointment_time' => '23:30',
            'status' => 'confirmed',
        ]);

        $chartService = app(DashboardChartService::class);
        $data = $chartService->getDemandChartData($user);

        // El último elemento (índice 6) corresponde a hoy (2026-10-15)
        $this->assertEquals(1, $data->counts[6]);
        $this->assertEquals(1, $data->totalAppointments);
    }

    public function test_empty_states_for_both_charts(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));
        $user = User::factory()->admin()->create();

        $chartService = app(DashboardChartService::class);
        $demand = $chartService->getDemandChartData($user);
        $dist = $chartService->getDistributionChartData($user);

        // Demanda vacía
        $this->assertTrue($demand->isEmpty);
        $this->assertEquals(0, $demand->totalAppointments);
        $this->assertCount(7, $demand->counts);
        $this->assertEquals([0, 0, 0, 0, 0, 0, 0], $demand->counts);

        // Distribución vacía
        $this->assertTrue($dist->isEmpty);
        $this->assertEquals(0, $dist->totalAppointments);
        $this->assertEmpty($dist->labels);
        $this->assertStringContainsString('Aún no hay tratamientos registrados', $dist->ariaLabel);
    }

    public function test_dashboard_renders_both_chart_widgets(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertSuccessful();
        $response->assertSee('Demanda de citas · últimos 7 días');
        $response->assertSee('Distribución de tratamientos');
        $response->assertSee('Citas agendadas');
        $response->assertSee('Hoy');
    }
}
