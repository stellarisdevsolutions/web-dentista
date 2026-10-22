<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Dentists\DentistResource;
use App\Filament\Resources\Dentists\Pages\EditDentist;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CreateAndEditRedirectNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_service_redirects_to_index_with_notification_and_data_is_immediately_in_table(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('service.jpg', 1200, 900);

        Livewire::test(CreateService::class)
            ->fillForm([
                'name' => 'Profilaxis Ultrasónica',
                'slug' => 'profilaxis-ultrasonica',
                'category' => 'Higiene',
                'image' => $file,
                'duration_minutes' => 45,
            ])
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(ServiceResource::getUrl('index'))
            ->assertNotified('Tratamiento registrado exitosamente');

        // Verify that following the redirect to index immediately shows the treatment in the table HTML
        $response = $this->get(ServiceResource::getUrl('index'));
        $response->assertSuccessful();
        $response->assertSee('Profilaxis Ultrasónica');
    }

    public function test_create_user_redirects_to_index_with_notification_and_data_is_immediately_in_table(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Dra. Patricia Silva',
                'email' => 'patricia@auradental.com',
                'password' => 'secret1234',
                'role' => UserRole::Receptionist->value,
            ])
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(UserResource::getUrl('index'))
            ->assertNotified('Usuario registrado exitosamente');

        $response = $this->get(UserResource::getUrl('index'));
        $response->assertSuccessful();
        $response->assertSee('Dra. Patricia Silva');
        $response->assertSee('patricia@auradental.com');
    }

    public function test_edit_dentist_redirects_to_index_with_notification_and_updated_data(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        $dentist = Dentist::factory()->create([
            'name' => 'Dr. Nombre Anterior',
            'title' => 'Cirujano Dentista',
        ]);

        Livewire::test(EditDentist::class, ['record' => $dentist->id])
            ->fillForm([
                'name' => 'Dr. Nombre Actualizado',
                'title' => 'Cirujano Dentista',
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(DentistResource::getUrl('index'))
            ->assertNotified('Especialista actualizado exitosamente');

        $response = $this->get(DentistResource::getUrl('index'));
        $response->assertSuccessful();
        $response->assertSee('Dr. Nombre Actualizado');
    }

    public function test_edit_appointment_redirects_to_index_with_notification(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        $dentist = Dentist::factory()->create();
        $service = Service::factory()->create();

        $appointment = Appointment::factory()->create([
            'dentist_id' => $dentist->id,
            'service_id' => $service->id,
            'patient_name' => 'Juan Paciente',
            'patient_email' => 'juan@test.com',
            'patient_phone' => '+123456789',
            'appointment_date' => now()->next(Carbon::MONDAY)->toDateString(),
            'appointment_time' => '10:00:00',
        ]);

        Livewire::test(EditAppointment::class, ['record' => $appointment->id])
            ->fillForm([
                'notes' => 'Paciente requiere anestesia suave.',
            ])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(AppointmentResource::getUrl('index'))
            ->assertNotified('Cita actualizada exitosamente');
    }
}
