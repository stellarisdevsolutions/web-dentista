<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Widgets\LatestAppointmentsWidget;
use App\Filament\Widgets\WelcomeBannerWidget;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TimeFormatTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_appointment_format_time_helper_in_12h_mode(): void
    {
        config(['app.time_format' => '12h']);

        $this->assertSame('09:00 AM', Appointment::formatTime('09:00'));
        $this->assertSame('09:00 AM', Appointment::formatTime('09:00:00'));
        $this->assertSame('12:00 PM', Appointment::formatTime('12:00'));
        $this->assertSame('02:30 PM', Appointment::formatTime('14:30'));
        $this->assertSame('11:59 PM', Appointment::formatTime('23:59'));
        $this->assertSame('12:00 AM', Appointment::formatTime('00:00'));
        $this->assertSame('--:--', Appointment::formatTime(null));
        $this->assertSame('--:--', Appointment::formatTime(''));
    }

    public function test_appointment_format_time_helper_in_24h_mode(): void
    {
        config(['app.time_format' => '24h']);

        $this->assertSame('09:00', Appointment::formatTime('09:00'));
        $this->assertSame('09:00', Appointment::formatTime('09:00:00'));
        $this->assertSame('12:00', Appointment::formatTime('12:00'));
        $this->assertSame('14:30', Appointment::formatTime('14:30'));
        $this->assertSame('23:59', Appointment::formatTime('23:59'));
        $this->assertSame('00:00', Appointment::formatTime('00:00'));
        $this->assertSame('--:--', Appointment::formatTime(null));
        $this->assertSame('--:--', Appointment::formatTime(''));
    }

    public function test_appointment_formatted_time_accessor_and_whatsapp_url(): void
    {
        $appointment = Appointment::factory()->create([
            'appointment_time' => '14:30',
            'patient_phone' => '123456789',
        ]);

        // 12h mode
        config(['app.time_format' => '12h']);
        $this->assertSame('02:30 PM', $appointment->formatted_time);
        $this->assertStringContainsString('02:30 PM', urldecode($appointment->whats_app_url));

        // 24h mode
        config(['app.time_format' => '24h']);
        $this->assertSame('14:30', $appointment->formatted_time);
        $this->assertStringContainsString('14:30', urldecode($appointment->whats_app_url));
    }

    public function test_public_available_slots_api_returns_formatted_labels(): void
    {
        $dentist = Dentist::factory()->create([
            'is_active' => true,
            'weekly_schedule' => [
                'monday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '10:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '15:00',
                ],
            ],
        ]);
        $service = Service::factory()->create(['duration_minutes' => 30]);
        $monday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        // 12h mode
        config(['app.time_format' => '12h']);
        $res12h = $this->getJson("/api/appointments/available-slots?date={$monday}&dentist_id={$dentist->id}&service_id={$service->id}");
        $res12h->assertOk();
        $morning12h = $res12h->json('morning_slots');
        $afternoon12h = $res12h->json('afternoon_slots');
        $this->assertNotEmpty($morning12h);
        $this->assertNotEmpty($afternoon12h);
        $this->assertSame('09:00 AM', $morning12h[0]['label']);
        $this->assertSame('02:00 PM', $afternoon12h[0]['label']);

        // 24h mode
        config(['app.time_format' => '24h']);
        $res24h = $this->getJson("/api/appointments/available-slots?date={$monday}&dentist_id={$dentist->id}&service_id={$service->id}");
        $res24h->assertOk();
        $morning24h = $res24h->json('morning_slots');
        $afternoon24h = $res24h->json('afternoon_slots');
        $this->assertSame('09:00', $morning24h[0]['label']);
        $this->assertSame('14:00', $afternoon24h[0]['label']);
    }

    public function test_public_appointment_booking_response_contains_formatted_time(): void
    {
        $dentist = Dentist::factory()->create([
            'is_active' => true,
            'weekly_schedule' => [
                'monday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '12:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);
        $service = Service::factory()->create(['duration_minutes' => 30]);
        $monday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        // Book appointment in 12h mode
        config(['app.time_format' => '12h']);
        $response12h = $this->postJson('/appointments', [
            'dentist_id' => $dentist->id,
            'service_id' => $service->id,
            'appointment_date' => $monday,
            'appointment_time' => '14:00',
            'patient_name' => 'Carlos 12h',
            'patient_email' => 'carlos12@example.com',
            'patient_phone' => '1234567890',
        ]);

        $response12h->assertCreated();
        $this->assertSame('02:00 PM', $response12h->json('appointment.time'));
        $this->assertSame('14:00', $response12h->json('appointment.time_raw'));

        // Book appointment in 24h mode
        config(['app.time_format' => '24h']);
        $response24h = $this->postJson('/appointments', [
            'dentist_id' => $dentist->id,
            'service_id' => $service->id,
            'appointment_date' => $monday,
            'appointment_time' => '15:00',
            'patient_name' => 'Carlos 24h',
            'patient_email' => 'carlos24@example.com',
            'patient_phone' => '1234567891',
        ]);

        $response24h->assertCreated();
        $this->assertSame('15:00', $response24h->json('appointment.time'));
        $this->assertSame('15:00', $response24h->json('appointment.time_raw'));
    }

    public function test_admin_global_search_and_drawer_details_use_configured_time_format(): void
    {
        $dentist = Dentist::factory()->create();
        $service = Service::factory()->create();
        $appointment = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'service_id' => $service->id,
            'appointment_time' => '16:45',
        ]);

        // Global search details in 12h mode
        config(['app.time_format' => '12h']);
        $details12h = AppointmentResource::getGlobalSearchResultDetails($appointment);
        $this->assertStringContainsString('04:45 PM', $details12h['Fecha']);

        // Global search details in 24h mode
        config(['app.time_format' => '24h']);
        $details24h = AppointmentResource::getGlobalSearchResultDetails($appointment);
        $this->assertStringContainsString('16:45', $details24h['Fecha']);

        // Drawer details in ListAppointments in 12h mode
        config(['app.time_format' => '12h']);
        $page = new ListAppointments;
        $drawerData12h = $page->getAppointmentDetails($appointment->id);
        $this->assertSame('04:45 PM', $drawerData12h['appointment_time']);
        $this->assertSame('16:45', $drawerData12h['appointment_time_raw']);

        // Drawer details in ListAppointments in 24h mode
        config(['app.time_format' => '24h']);
        $drawerData24h = $page->getAppointmentDetails($appointment->id);
        $this->assertSame('16:45', $drawerData24h['appointment_time']);
        $this->assertSame('16:45', $drawerData24h['appointment_time_raw']);
    }

    public function test_dashboard_welcome_banner_and_latest_appointments_widget_respect_time_format(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');

        $dentist = Dentist::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
            'email' => 'doc_time@example.com',
        ]);

        $appointment = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'appointment_date' => Carbon::today(),
            'appointment_time' => '15:30',
            'status' => 'confirmed',
        ]);

        // 12h mode
        config(['app.time_format' => '12h']);
        Livewire::actingAs($user)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('03:30 PM');

        Livewire::actingAs($user)
            ->test(LatestAppointmentsWidget::class)
            ->assertSee('03:30 PM');

        // 24h mode
        config(['app.time_format' => '24h']);
        Livewire::actingAs($user)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('15:30');

        Livewire::actingAs($user)
            ->test(LatestAppointmentsWidget::class)
            ->assertSee('15:30');
    }
}
