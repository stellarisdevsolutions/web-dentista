<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Widgets\WelcomeBannerWidget;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WelcomeBannerWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_greeting_changes_according_to_time_of_day(): void
    {
        $widget = new WelcomeBannerWidget;

        // Morning: 05:00 - 11:59
        Carbon::setTestNow('2026-10-11 08:30:00');
        $this->assertSame('Buenos días', $widget->getGreeting());

        // Afternoon: 12:00 - 18:59
        Carbon::setTestNow('2026-10-11 14:15:00');
        $this->assertSame('Buenas tardes', $widget->getGreeting());

        // Evening / Night: 19:00 - 04:59
        Carbon::setTestNow('2026-10-11 21:00:00');
        $this->assertSame('Buenas noches', $widget->getGreeting());

        Carbon::setTestNow('2026-10-11 02:45:00');
        $this->assertSame('Buenas noches', $widget->getGreeting());
    }

    public function test_no_titles_dr_or_dra_appear_in_welcome_banner(): void
    {
        $widget = new WelcomeBannerWidget;

        $userWithDr = User::factory()->make(['name' => 'Dr. Carlos Mendoza']);
        $this->assertSame('Carlos Mendoza', $widget->getCleanUserName($userWithDr));

        $userWithDra = User::factory()->make(['name' => 'Dra. María Elena']);
        $this->assertSame('María Elena', $widget->getCleanUserName($userWithDra));

        $userWithDoctor = User::factory()->make(['name' => 'Doctor Fernando']);
        $this->assertSame('Fernando', $widget->getCleanUserName($userWithDoctor));

        $userWithPlain = User::factory()->make(['name' => 'Ana Morales']);
        $this->assertSame('Ana Morales', $widget->getCleanUserName($userWithPlain));

        // When rendered in Livewire
        Carbon::setTestNow('2026-10-11 09:00:00');
        $user = User::factory()->create([
            'name' => 'Dr. Roberto Gomez',
            'role' => UserRole::Admin,
        ]);

        Livewire::actingAs($user)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('Roberto Gomez')
            ->assertDontSee('Dr. Roberto Gomez');
    }

    public function test_admin_sees_admin_specific_content_and_buttons(): void
    {
        Carbon::setTestNow('2026-10-11 10:00:00');
        $admin = User::factory()->create([
            'name' => 'Administrador Aura',
            'role' => UserRole::Admin,
        ]);

        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'pending',
        ]);
        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
        ]);

        Livewire::actingAs($admin)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('Monitorea citas, solicitudes de pacientes y especialistas en tiempo real.')
            ->assertSee('+ Nueva cita')
            ->assertSee('Ver agenda')
            ->assertSee('citas hoy')
            ->assertSee('por confirmar')
            ->assertSee('2') // total citas hoy
            ->assertSee('1'); // por confirmar
    }

    public function test_receptionist_sees_receptionist_specific_content_and_buttons(): void
    {
        Carbon::setTestNow('2026-10-11 10:00:00');
        $receptionist = User::factory()->create([
            'name' => 'Lucía Recepción',
            'role' => UserRole::Receptionist,
        ]);

        Appointment::factory()->count(2)->create([
            'appointment_date' => Carbon::today(),
            'status' => 'pending',
        ]);
        Appointment::factory()->create([
            'appointment_date' => Carbon::today(),
            'status' => 'in_progress',
        ]);

        Livewire::actingAs($receptionist)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('Hay 2 citas por confirmar y 1 paciente en sala. Empieza por las pendientes.')
            ->assertSee('+ Nueva cita')
            ->assertSee('Confirmar pendientes')
            ->assertSee('en sala ahora')
            ->assertSee('por confirmar');
    }

    public function test_dentist_only_counts_their_own_appointments_and_sees_next_appointment(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');

        $dentist1 = Dentist::factory()->create(['name' => 'Carlos Odontólogo']);
        $dentist2 = Dentist::factory()->create(['name' => 'Sofía Odontóloga']);

        $dentistUser = User::factory()->create([
            'name' => 'Carlos Odontólogo',
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist1->id,
        ]);

        // Citas de Dentist 1 hoy
        Appointment::factory()->create([
            'dentist_id' => $dentist1->id,
            'appointment_date' => Carbon::today(),
            'appointment_time' => '10:30',
            'status' => 'confirmed',
        ]);
        Appointment::factory()->create([
            'dentist_id' => $dentist1->id,
            'appointment_date' => Carbon::today(),
            'appointment_time' => '15:00',
            'status' => 'pending',
        ]);

        // Citas de Dentist 2 hoy (no deben sumarse al Dentist 1)
        Appointment::factory()->count(5)->create([
            'dentist_id' => $dentist2->id,
            'appointment_date' => Carbon::today(),
            'status' => 'confirmed',
        ]);

        $test = Livewire::actingAs($dentistUser)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('Tienes 2 pacientes hoy. Tu próxima cita es a las 10:30.')
            ->assertSee('Ver mi agenda')
            ->assertSee('Mis pacientes')
            ->assertSee('mis citas hoy')
            ->assertSee('por confirmar');

        /** @var WelcomeBannerWidget $instance */
        $instance = $test->instance();
        $viewData = $instance->getViewData();

        $this->assertSame(2, $viewData['cards'][0]['value']);
        $this->assertSame(1, $viewData['cards'][1]['value']);
    }

    public function test_dentist_shows_fallback_when_no_appointments_today(): void
    {
        Carbon::setTestNow('2026-10-11 09:00:00');

        $dentist = Dentist::factory()->create();
        $dentistUser = User::factory()->create([
            'name' => 'Andrés Odontólogo',
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
        ]);

        Livewire::actingAs($dentistUser)
            ->test(WelcomeBannerWidget::class)
            ->assertSee('No tienes citas pendientes hoy.');
    }
}
