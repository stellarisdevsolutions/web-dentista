<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\Post;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DentalClinicPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_clinic_data_and_sections(): void
    {
        $clinic = ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'tagline' => 'Odontología moderna de alta calidad.',
            'phone' => '+1 (555) 000-1111',
            'whatsapp' => '+15550001111',
            'email' => 'info@auradental.com',
            'address' => 'Calle Salud 123',
        ]);

        $service = Service::create([
            'name' => 'Blanqueamiento Premium',
            'slug' => 'blanqueamiento-premium',
            'category' => 'Estética',
            'short_description' => 'Blanqueamiento dental en una sola sesión.',
            'price' => 199.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $dentist = Dentist::create([
            'name' => 'Dra. Maria López',
            'slug' => 'dra-maria-lopez',
            'specialty' => 'Estética Dental',
            'license_number' => 'DDS-12345',
            'bio' => 'Especialista en carillas y estética dental.',
            'is_active' => true,
        ]);

        $dentist->services()->attach($service->id);

        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Aura Dental Studio Test');
        $response->assertSee('Blanqueamiento Premium');
        $response->assertSee('Reserva Online 24/7');
    }

    public function test_about_page_renders_specialists_team(): void
    {
        $clinic = ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 000-1111',
            'whatsapp' => '+15550001111',
            'email' => 'info@auradental.com',
            'address' => 'Calle Salud 123',
        ]);

        Dentist::create([
            'name' => 'Dra. Maria López',
            'slug' => 'dra-maria-lopez',
            'specialty' => 'Estética Dental',
            'license_number' => 'DDS-12345',
            'bio' => 'Especialista en carillas y estética dental.',
            'is_active' => true,
        ]);

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('Dra. Maria López');
        $response->assertSee('Estética Dental');

        $responseAbout = $this->get('/about');
        $responseAbout->assertSuccessful();
        $responseAbout->assertSee('Dra. Maria López');
    }

    public function test_schedule_page_renders_booking_wizard(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 000-1111',
            'whatsapp' => '+15550001111',
            'email' => 'info@auradental.com',
            'address' => 'Calle Salud 123',
        ]);

        Service::create([
            'name' => 'Blanqueamiento Premium',
            'slug' => 'blanqueamiento-premium',
            'price' => 199.00,
            'is_active' => true,
        ]);

        $response = $this->get('/reservar');
        $response->assertSuccessful();
        $response->assertSee('Agenda tu Cita Dental en 3 Pasos');
        $response->assertSee('Blanqueamiento Premium');

        $responseSchedule = $this->get('/schedule');
        $responseSchedule->assertSuccessful();
        $responseSchedule->assertSee('Agenda tu Cita Dental en 3 Pasos');
    }

    public function test_patient_can_book_appointment_online(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio',
            'phone' => '+15552345678',
            'whatsapp' => '+15552345678',
            'email' => 'contact@auradental.com',
            'address' => 'Av. Central 123',
        ]);

        $service = Service::create([
            'name' => 'Limpieza Dental Ultrasonido',
            'slug' => 'limpieza-dental-ultrasonido',
            'price' => 75.00,
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $dentist = Dentist::create([
            'name' => 'Dr. Carlos Mendoza',
            'slug' => 'dr-carlos-mendoza',
            'specialty' => 'Prevención y Salud Oral',
            'is_active' => true,
        ]);

        $dentist->services()->attach($service->id);

        $appointmentDate = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $payload = [
            'service_id' => $service->id,
            'dentist_id' => $dentist->id,
            'appointment_date' => $appointmentDate,
            'appointment_time' => '10:30',
            'patient_name' => 'Ana Sofía Ruiz',
            'patient_email' => 'ana.ruiz@example.com',
            'patient_phone' => '+15559876543',
            'is_first_visit' => 1,
            'symptoms' => 'Revisión general preventiva',
        ];

        $response = $this->postJson('/appointments', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'appointment' => [
                'service' => 'Limpieza Dental Ultrasonido',
                'dentist' => 'Dr. Carlos Mendoza',
                'time' => Appointment::formatTime('10:30'),
                'time_raw' => '10:30',
                'patient_name' => 'Ana Sofía Ruiz',
            ],
        ]);

        $this->assertDatabaseHas('appointments', [
            'service_id' => $service->id,
            'dentist_id' => $dentist->id,
            'patient_name' => 'Ana Sofía Ruiz',
            'patient_email' => 'ana.ruiz@example.com',
            'status' => 'pending',
        ]);

        $created = Appointment::where('patient_email', 'ana.ruiz@example.com')->first();
        $this->assertNotNull($created);
        $this->assertStringStartsWith('CIT-'.date('Y').'-', $created->code);
    }

    public function test_appointment_booking_validates_required_fields(): void
    {
        $response = $this->postJson('/appointments', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'service_id',
            'appointment_date',
            'appointment_time',
            'patient_name',
            'patient_email',
            'patient_phone',
        ]);
    }

    public function test_service_dentists_api_returns_assigned_specialists(): void
    {
        $service = Service::create([
            'name' => 'Ortodoncia Invisible',
            'slug' => 'ortodoncia-invisible-api',
            'is_active' => true,
        ]);

        $dentist1 = Dentist::create([
            'name' => 'Dr. Especialista Ortodoncia',
            'slug' => 'dr-especialista-ortodoncia',
            'specialty' => 'Ortodoncia',
            'is_active' => true,
        ]);

        $dentist2 = Dentist::create([
            'name' => 'Dr. Cirujano',
            'slug' => 'dr-cirujano',
            'specialty' => 'Cirugía',
            'is_active' => true,
        ]);

        // Only dentist1 offers invisible aligners
        $service->dentists()->attach($dentist1->id);

        $response = $this->getJson("/api/services/{$service->id}/dentists");

        $response->assertSuccessful();
        $response->assertJsonCount(1, 'dentists');
        $response->assertJsonPath('dentists.0.name', 'Dr. Especialista Ortodoncia');
    }

    public function test_available_slots_api_returns_time_intervals(): void
    {
        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->getJson("/api/appointments/available-slots?date={$date}");

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'date',
            'dentist_id',
            'slots' => [
                '*' => ['time', 'available'],
            ],
            'morning_slots',
            'afternoon_slots',
        ]);
    }

    public function test_dentist_shift_schedule_respects_morning_afternoon_and_lunch_break(): void
    {
        // Pick a Wednesday
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');

        $dentist = Dentist::create([
            'name' => 'Dra. Especialista Horarios',
            'slug' => 'dra-especialista-horarios',
            'is_active' => true,
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        $response = $this->getJson("/api/appointments/available-slots?date={$wednesday}&dentist_id={$dentist->id}");

        $response->assertSuccessful();

        $slots = collect($response->json('slots'));
        $times = $slots->pluck('time')->all();

        // Must include 30-minute interval morning slots up to 12:00 (which ends at 12:45 before 13:00 lunch)
        $this->assertContains('09:00', $times);
        $this->assertContains('09:30', $times);
        $this->assertContains('10:00', $times);
        $this->assertContains('10:30', $times);
        $this->assertContains('12:00', $times);

        // 12:30 with 45m duration exceeds 13:00 morning shift end (13:15), so it is not offered as a start time
        $this->assertNotContains('12:30', $times);

        // 09:00 with 45m duration fits inside 09:00-13:00 shift, so it must be available
        $slot0900 = $slots->firstWhere('time', '09:00');
        $this->assertNotNull($slot0900);
        $this->assertTrue($slot0900['available']);

        // Must NOT include lunch break (13:00 to 14:00)
        $this->assertNotContains('13:00', $times);
        $this->assertNotContains('13:15', $times);
        $this->assertNotContains('13:30', $times);

        // Must include afternoon slots in 30-minute intervals
        $this->assertContains('14:00', $times);
        $this->assertContains('14:30', $times);
        $this->assertContains('15:00', $times);
    }

    public function test_services_catalog_renders_active_services(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 000-1111',
            'whatsapp' => '+15550001111',
            'email' => 'info@auradental.com',
            'address' => 'Calle Salud 123',
        ]);

        Service::create([
            'name' => 'Implantes Dentales Titanio',
            'slug' => 'implantes-dentales-titanio',
            'category' => 'Implantología',
            'short_description' => 'Reemplazo permanente con tornillos de titanio grado médico.',
            'price' => 850.00,
            'duration_minutes' => 90,
            'is_active' => true,
        ]);

        $responseEs = $this->get('/servicios');
        $responseEs->assertSuccessful();
        $responseEs->assertSee('Implantes Dentales Titanio');
        $responseEs->assertSee('Implantología');

        $responseEn = $this->get('/services');
        $responseEn->assertSuccessful();
        $responseEn->assertSee('Implantes Dentales Titanio');
    }

    public function test_service_detail_page_renders_with_assigned_dentists(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 000-1111',
            'whatsapp' => '+15550001111',
            'email' => 'info@auradental.com',
            'address' => 'Calle Salud 123',
        ]);

        $service = Service::create([
            'name' => 'Carillas de Porcelana',
            'slug' => 'carillas-de-porcelana',
            'category' => 'Estética',
            'short_description' => 'Transforma tu sonrisa con carillas ultra-finas.',
            'description' => 'Láminas cerámicas de alta resistencia diseñadas a medida.',
            'price' => 450.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $dentist = Dentist::create([
            'name' => 'Dr. Roberto Sánchez',
            'slug' => 'dr-roberto-sanchez',
            'specialty' => 'Estética y Rehabilitación Oral',
            'is_active' => true,
        ]);

        $service->dentists()->attach($dentist->id);

        $responseEs = $this->get('/servicios/'.$service->slug);
        $responseEs->assertSuccessful();
        $responseEs->assertSee('Carillas de Porcelana');
        $responseEs->assertSee('Dr. Roberto Sánchez');
        $responseEs->assertSee('450');

        $responseEn = $this->get('/services/'.$service->slug);
        $responseEn->assertSuccessful();
        $responseEn->assertSee('Carillas de Porcelana');
    }

    public function test_contact_page_renders_clinic_information(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 333-4444',
            'whatsapp' => '+15553334444',
            'email' => 'contacto@auradental.com',
            'address' => 'Av. Los Próceres 888',
        ]);

        $responseEs = $this->get('/contacto');
        $responseEs->assertSuccessful();
        $responseEs->assertSee('+1 (555) 333-4444');
        $responseEs->assertSee('contacto@auradental.com');
        $responseEs->assertSee('Av. Los Próceres 888');

        $responseEn = $this->get('/contact');
        $responseEn->assertSuccessful();
        $responseEn->assertSee('+1 (555) 333-4444');
    }

    public function test_contact_form_submits_and_stores_message(): void
    {
        $payload = [
            'name' => 'Valeria Morales',
            'email' => 'valeria@example.com',
            'phone' => '+15554443322',
            'subject' => 'Consulta de Carillas',
            'message' => 'Buenas tardes, quisiera consultar el costo aproximado del diseño de sonrisa completo.',
        ];

        $response = $this->postJson('/contacto', $payload);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => 'Valeria Morales',
            ],
        ]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Valeria Morales',
            'email' => 'valeria@example.com',
            'phone' => '+15554443322',
            'subject' => 'Consulta de Carillas',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->postJson('/contacto', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'email',
            'message',
        ]);
    }

    public function test_blog_index_page_renders_published_articles(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 333-4444',
            'whatsapp' => '+15553334444',
            'email' => 'contacto@auradental.com',
            'address' => 'Av. Los Próceres 888',
        ]);

        Post::create([
            'title' => 'Guía Completa de Alineadores Invisibles',
            'slug' => 'guia-completa-alineadores-invisibles',
            'category' => 'Ortodoncia',
            'excerpt' => 'Conoce cómo funcionan los alineadores transparentes paso a paso.',
            'content' => 'Los alineadores transparentes corrigen la posición dental sin brackets metálicos.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/blog');
        $response->assertSuccessful();
        $response->assertSee('Guía Completa de Alineadores Invisibles');
        $response->assertSee('Ortodoncia');
    }

    public function test_blog_detail_page_renders_post_content(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 333-4444',
            'whatsapp' => '+15553334444',
            'email' => 'contacto@auradental.com',
            'address' => 'Av. Los Próceres 888',
        ]);

        $post = Post::create([
            'title' => 'Mitos del Blanqueamiento Dental',
            'slug' => 'mitos-del-blanqueamiento-dental',
            'category' => 'Estética',
            'excerpt' => 'Aclaramos si el peróxido daña el esmalte dental.',
            'content' => 'El blanqueamiento dental supervisado por odontólogos es 100% seguro y eficaz.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/blog/'.$post->slug);
        $response->assertSuccessful();
        $response->assertSee('Mitos del Blanqueamiento Dental');
        $response->assertSee('100% seguro y eficaz');
    }

    public function test_booking_assigns_least_saturated_dentist_when_no_dentist_selected(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');

        $service = Service::create([
            'name' => 'Limpieza Avanzada',
            'slug' => 'limpieza-avanzada-test',
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $dentistA = Dentist::create([
            'name' => 'Dr. Dentista Ocupado',
            'slug' => 'dr-dentista-ocupado',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $dentistB = Dentist::create([
            'name' => 'Dra. Dentista Libre',
            'slug' => 'dra-dentista-libre',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $service->dentists()->attach([$dentistA->id, $dentistB->id]);

        // Dentist A has 2 appointments on that date
        Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistA->id,
            'patient_name' => 'Paciente Previo 1',
            'patient_email' => 'previo1@example.com',
            'patient_phone' => '+1555111222',
            'appointment_date' => $wednesday,
            'appointment_time' => '09:00',
            'duration_minutes' => 45,
            'status' => 'confirmed',
        ]);
        Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistA->id,
            'patient_name' => 'Paciente Previo 2',
            'patient_email' => 'previo2@example.com',
            'patient_phone' => '+1555111333',
            'appointment_date' => $wednesday,
            'appointment_time' => '14:00',
            'duration_minutes' => 45,
            'status' => 'pending',
        ]);

        // Dentist B has 0 appointments on that date.
        // A new patient books 10:30 with NO dentist selected:
        $response = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => null,
            'appointment_date' => $wednesday,
            'appointment_time' => '10:30',
            'patient_name' => 'Nuevo Paciente Asignado',
            'patient_email' => 'nuevo.asignado@example.com',
            'patient_phone' => '+1555999888',
            'is_first_visit' => 1,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('appointment.dentist', 'Dra. Dentista Libre');

        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'nuevo.asignado@example.com',
            'dentist_id' => $dentistB->id,
        ]);
    }

    public function test_45_minute_treatment_is_marked_unavailable_when_window_is_only_30_minutes(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');

        $dentist = Dentist::create([
            'name' => 'Dr. Control Tiempos',
            'slug' => 'dr-control-tiempos',
            'is_active' => true,
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        // Existing appointment starts at 10:30 (occupies 10:30 to 11:15)
        Appointment::create([
            'dentist_id' => $dentist->id,
            'patient_name' => 'Paciente Ocupa 10:30',
            'patient_email' => 'ocupa1030@example.com',
            'patient_phone' => '+1555000999',
            'appointment_date' => $wednesday,
            'appointment_time' => '10:30',
            'duration_minutes' => 45,
            'status' => 'confirmed',
        ]);

        // A 45-minute treatment starting at 10:00 would end at 10:45, colliding with 10:30 appointment.
        // It must NOT be available!
        $this->assertFalse($dentist->isTimeSlotAvailable($wednesday, '10:00', 45));

        // A 30-minute treatment starting at 10:00 ends exactly at 10:30, without collision.
        // It MUST be available!
        $this->assertTrue($dentist->isTimeSlotAvailable($wednesday, '10:00', 30));
    }

    public function test_returning_patient_can_choose_preferred_dentist_specifically(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');

        $service = Service::create([
            'name' => 'Ortodoncia Control',
            'slug' => 'ortodoncia-control-test',
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $dentistPreferred = Dentist::create([
            'name' => 'Dr. Dentista Preferido',
            'slug' => 'dr-dentista-preferido',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $dentistOther = Dentist::create([
            'name' => 'Dra. Otra Doctora',
            'slug' => 'dra-otra-doctora',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $service->dentists()->attach([$dentistPreferred->id, $dentistOther->id]);

        $response = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => $dentistPreferred->id,
            'appointment_date' => $wednesday,
            'appointment_time' => '11:15',
            'patient_name' => 'Paciente Recurrente Frecuente',
            'patient_email' => 'recurrente@example.com',
            'patient_phone' => '+1555777888',
            'is_first_visit' => 0,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('appointment.dentist', 'Dr. Dentista Preferido');

        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'recurrente@example.com',
            'dentist_id' => $dentistPreferred->id,
            'is_first_visit' => 0,
        ]);
    }

    public function test_slots_advance_every_30_minutes_and_multi_slot_treatment_disables_conflicting_slots(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Test',
            'phone' => '+1 (555) 000-1111',
            'whatsapp' => '+15550001111',
            'email' => 'info@auradental.com',
            'address' => 'Calle Salud 123',
        ]);

        $dentist = Dentist::create([
            'name' => 'Dr. Intervalos Test',
            'slug' => 'dr-intervalos-test',
            'is_active' => true,
            'working_days' => ['wednesday'],
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        $service60min = Service::create([
            'name' => 'Blanqueamiento 60m',
            'slug' => 'blanqueamiento-60m',
            'category' => 'Estética',
            'short_description' => 'Tratamiento de 60 minutos.',
            'price' => 200.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $service60min->dentists()->attach($dentist->id);

        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        // Existing appointment from 10:00 to 11:00 (occupies 10:00 and 10:30 slots)
        Appointment::create([
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'patient_name' => 'Paciente Existente',
            'patient_email' => 'existente@example.com',
            'patient_phone' => '+1555999000',
            'appointment_date' => $wednesday,
            'appointment_time' => '10:00',
            'duration_minutes' => 60,
            'status' => 'confirmed',
        ]);

        $response = $this->getJson("/api/appointments/available-slots?date={$wednesday}&dentist_id={$dentist->id}&service_id={$service60min->id}");
        $response->assertSuccessful();

        $slots = collect($response->json('slots'))->keyBy('time');

        // All morning slots are generated in 30-min intervals up to 12:00 (which ends at 13:00)
        $this->assertTrue($slots['09:00']['available']);  // 09:00-10:00 is free
        $this->assertFalse($slots['09:30']['available']); // 09:30-10:30 collides with 10:00-11:00
        $this->assertFalse($slots['10:00']['available']); // 10:00-11:00 is occupied
        $this->assertFalse($slots['10:30']['available']); // 10:30-11:30 collides with 10:00-11:00
        $this->assertTrue($slots['11:00']['available']);  // 11:00-12:00 is free
        $this->assertTrue($slots['11:30']['available']);  // 11:30-12:30 is free
        $this->assertTrue($slots['12:00']['available']);  // 12:00-13:00 is free
        $this->assertArrayNotHasKey('12:30', $slots);     // 12:30 is not generated because 60m overflows 13:00 morning shift end
    }

    public function test_new_patient_without_dentist_saves_and_assigns_least_busy_dentist(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $service = Service::create([
            'name' => 'Limpieza Ultrasonido',
            'slug' => 'limpieza-ultrasonido-test',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $dentistBusy = Dentist::create([
            'name' => 'Dr. Dentista Ocupado',
            'slug' => 'dr-dentista-ocupado',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $dentistFree = Dentist::create([
            'name' => 'Dr. Dentista Libre',
            'slug' => 'dr-dentista-libre',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $service->dentists()->attach([$dentistBusy->id, $dentistFree->id]);

        // Give dentistBusy an active appointment on Wednesday
        Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistBusy->id,
            'patient_name' => 'Paciente Previo',
            'patient_email' => 'previo@example.com',
            'patient_phone' => '+1555111222',
            'appointment_date' => $wednesday,
            'appointment_time' => '09:00',
            'duration_minutes' => 30,
            'status' => 'confirmed',
        ]);

        // New patient reserves without specifying dentist_id
        $response = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => null,
            'appointment_date' => $wednesday,
            'appointment_time' => '10:00',
            'patient_name' => 'Carlos Paciente Nuevo',
            'patient_email' => 'carlos.nuevo@example.com',
            'patient_phone' => '+1555222333',
            'is_first_visit' => 1,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('appointment.dentist', 'Dr. Dentista Libre');

        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'carlos.nuevo@example.com',
            'dentist_id' => $dentistFree->id,
            'is_first_visit' => 1,
            'appointment_time' => '10:00',
        ]);
    }

    public function test_returning_patient_without_dentist_saves_and_assigns_least_busy_dentist(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $service = Service::create([
            'name' => 'Control General',
            'slug' => 'control-general-test',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $dentistBusy = Dentist::create([
            'name' => 'Dra. Saturada',
            'slug' => 'dra-saturada',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $dentistFree = Dentist::create([
            'name' => 'Dra. Disponible',
            'slug' => 'dra-disponible',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $service->dentists()->attach([$dentistBusy->id, $dentistFree->id]);

        Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistBusy->id,
            'patient_name' => 'Cita Previa',
            'patient_email' => 'previa@example.com',
            'patient_phone' => '+1555333444',
            'appointment_date' => $wednesday,
            'appointment_time' => '09:00',
            'duration_minutes' => 30,
            'status' => 'confirmed',
        ]);

        // Returning patient chooses "primer disponible" (dentist_id = null, is_first_visit = 0)
        $response = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => null,
            'appointment_date' => $wednesday,
            'appointment_time' => '10:00',
            'patient_name' => 'Ana Recurrente',
            'patient_email' => 'ana.recurrente@example.com',
            'patient_phone' => '+1555444555',
            'is_first_visit' => 0,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('appointment.dentist', 'Dra. Disponible');

        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'ana.recurrente@example.com',
            'dentist_id' => $dentistFree->id,
            'is_first_visit' => 0,
        ]);
    }

    public function test_returning_patient_selecting_specific_dentist_saves_with_that_dentist_even_if_busier(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $service = Service::create([
            'name' => 'Blanqueamiento Dental',
            'slug' => 'blanqueamiento-especifico-test',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $dentistPreferred = Dentist::create([
            'name' => 'Dr. Favorito',
            'slug' => 'dr-favorito',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $dentistOther = Dentist::create([
            'name' => 'Dr. Totalmente Libre',
            'slug' => 'dr-totalmente-libre',
            'is_active' => true,
            'working_days' => ['wednesday'],
        ]);

        $service->dentists()->attach([$dentistPreferred->id, $dentistOther->id]);

        // Dr. Favorito already has 2 appointments
        Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistPreferred->id,
            'patient_name' => 'Cita 1',
            'patient_email' => 'c1@example.com',
            'patient_phone' => '+155500111',
            'appointment_date' => $wednesday,
            'appointment_time' => '09:00',
            'duration_minutes' => 30,
            'status' => 'confirmed',
        ]);
        Appointment::create([
            'service_id' => $service->id,
            'dentist_id' => $dentistPreferred->id,
            'patient_name' => 'Cita 2',
            'patient_email' => 'c2@example.com',
            'patient_phone' => '+155500222',
            'appointment_date' => $wednesday,
            'appointment_time' => '09:30',
            'duration_minutes' => 30,
            'status' => 'confirmed',
        ]);

        // Returning patient explicitly chooses Dr. Favorito at 10:00
        $response = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => $dentistPreferred->id,
            'appointment_date' => $wednesday,
            'appointment_time' => '10:00',
            'patient_name' => 'Elena Fiel',
            'patient_email' => 'elena.fiel@example.com',
            'patient_phone' => '+1555888999',
            'is_first_visit' => 0,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('appointment.dentist', 'Dr. Favorito');

        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'elena.fiel@example.com',
            'dentist_id' => $dentistPreferred->id,
            'is_first_visit' => 0,
            'appointment_time' => '10:00',
        ]);
    }

    public function test_booking_fails_and_does_not_save_when_treatment_exceeds_shift_end(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $dentist = Dentist::create([
            'name' => 'Dr. Turno Estricto',
            'slug' => 'dr-turno-estricto',
            'is_active' => true,
            'working_days' => ['wednesday'],
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        // 60-minute treatment
        $service60min = Service::create([
            'name' => 'Tratamiento Largo 60m',
            'slug' => 'tratamiento-largo-60m',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
        $service60min->dentists()->attach($dentist->id);

        // Trying to book at 12:30 for 60 min -> ends at 13:30 > 13:00 morning shift end!
        $response = $this->postJson('/appointments', [
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'appointment_date' => $wednesday,
            'appointment_time' => '12:30',
            'patient_name' => 'Intento Fuera de Turno',
            'patient_email' => 'fuera.turno@example.com',
            'patient_phone' => '+1555666777',
            'is_first_visit' => 1,
        ]);

        $response->assertStatus(422);

        // Must NOT save to database
        $this->assertDatabaseMissing('appointments', [
            'patient_email' => 'fuera.turno@example.com',
            'appointment_time' => '12:30',
        ]);
    }

    public function test_booking_fails_and_does_not_save_when_multi_slot_treatment_collides_with_existing_appointment(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $dentist = Dentist::create([
            'name' => 'Dr. Solapamiento Test',
            'slug' => 'dr-solapamiento-test',
            'is_active' => true,
            'working_days' => ['wednesday'],
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        $service60min = Service::create([
            'name' => 'Endodoncia 60m',
            'slug' => 'endodoncia-60m-test',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
        $service60min->dentists()->attach($dentist->id);

        // Pre-existing appointment from 10:00 to 11:00
        Appointment::create([
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'patient_name' => 'Cita En Curso',
            'patient_email' => 'en.curso@example.com',
            'patient_phone' => '+1555777888',
            'appointment_date' => $wednesday,
            'appointment_time' => '10:00',
            'duration_minutes' => 60,
            'status' => 'confirmed',
        ]);

        // Attempting to book 60 min at 09:30 (needs 09:30 to 10:30, collides with 10:00-11:00)
        $response = $this->postJson('/appointments', [
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'appointment_date' => $wednesday,
            'appointment_time' => '09:30',
            'patient_name' => 'Intento Solapado',
            'patient_email' => 'solapado@example.com',
            'patient_phone' => '+1555999111',
            'is_first_visit' => 1,
        ]);

        $response->assertStatus(422);

        // Must NOT save to database
        $this->assertDatabaseMissing('appointments', [
            'patient_email' => 'solapado@example.com',
            'appointment_time' => '09:30',
        ]);
    }

    public function test_booking_succeeds_and_saves_when_multi_slot_treatment_fits_entirely_in_free_window(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $dentist = Dentist::create([
            'name' => 'Dr. Espacio Limpio',
            'slug' => 'dr-espacio-limpio',
            'is_active' => true,
            'working_days' => ['wednesday'],
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        $service60min = Service::create([
            'name' => 'Implante 60m',
            'slug' => 'implante-60m-test',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
        $service60min->dentists()->attach($dentist->id);

        // Pre-existing appointment from 10:00 to 11:00
        Appointment::create([
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'patient_name' => 'Cita Anterior',
            'patient_email' => 'anterior@example.com',
            'patient_phone' => '+1555777888',
            'appointment_date' => $wednesday,
            'appointment_time' => '10:00',
            'duration_minutes' => 60,
            'status' => 'confirmed',
        ]);

        // Booking at 11:00 for 60 min (11:00 to 12:00) fits completely and cleanly!
        $response = $this->postJson('/appointments', [
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'appointment_date' => $wednesday,
            'appointment_time' => '11:00',
            'patient_name' => 'Paciente Con Espacio',
            'patient_email' => 'con.espacio@example.com',
            'patient_phone' => '+1555333222',
            'is_first_visit' => 1,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'con.espacio@example.com',
            'dentist_id' => $dentist->id,
            'appointment_time' => '11:00',
            'duration_minutes' => 60,
            'is_first_visit' => 1,
        ]);
    }

    public function test_when_1230_is_occupied_by_an_appointment_a_60_minute_treatment_at_1200_is_marked_unavailable_and_rejected(): void
    {
        $wednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $dentist = Dentist::create([
            'name' => 'Dr. Cita a las 12:30',
            'slug' => 'dr-cita-1230',
            'is_active' => true,
            'working_days' => ['wednesday'],
            'weekly_schedule' => [
                'wednesday' => [
                    'is_working' => true,
                    'morning_start' => '09:00',
                    'morning_end' => '13:00',
                    'afternoon_start' => '14:00',
                    'afternoon_end' => '18:00',
                ],
            ],
        ]);

        $service30min = Service::create([
            'name' => 'Diagnóstico Rápido 30m',
            'slug' => 'diagnostico-30m-test',
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $service60min = Service::create([
            'name' => 'Blanqueamiento 60m',
            'slug' => 'blanqueamiento-60m-1200-test',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        $service30min->dentists()->attach($dentist->id);
        $service60min->dentists()->attach($dentist->id);

        // Appointment already occupies 12:30 to 13:00
        Appointment::create([
            'service_id' => $service30min->id,
            'dentist_id' => $dentist->id,
            'patient_name' => 'Cita en 12:30',
            'patient_email' => 'cita1230@example.com',
            'patient_phone' => '+1555123000',
            'appointment_date' => $wednesday,
            'appointment_time' => '12:30',
            'duration_minutes' => 30,
            'status' => 'confirmed',
        ]);

        // When fetching slots for 60 min treatment:
        $response = $this->getJson("/api/appointments/available-slots?date={$wednesday}&dentist_id={$dentist->id}&service_id={$service60min->id}");
        $response->assertSuccessful();

        $slots = collect($response->json('slots'))->keyBy('time');

        // 11:30 (11:30 to 12:30) is free
        $this->assertTrue($slots['11:30']['available']);

        // 12:00 (12:00 to 13:00) collides with the 12:30-13:00 appointment! Must be marked unavailable!
        $this->assertFalse($slots['12:00']['available']);

        // Booking at 12:00 for 60 min must be REJECTED with 422
        $bookResponse = $this->postJson('/appointments', [
            'service_id' => $service60min->id,
            'dentist_id' => $dentist->id,
            'appointment_date' => $wednesday,
            'appointment_time' => '12:00',
            'patient_name' => 'Intento 12:00 Choque',
            'patient_email' => 'choque1200@example.com',
            'patient_phone' => '+1555120000',
            'is_first_visit' => 1,
        ]);

        $bookResponse->assertStatus(422);

        // Must NOT save to database
        $this->assertDatabaseMissing('appointments', [
            'patient_email' => 'choque1200@example.com',
            'appointment_time' => '12:00',
        ]);
    }

    public function test_available_slots_unified_only_includes_specialists_qualified_for_service(): void
    {
        $monday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $tuesday = Carbon::now()->next(Carbon::TUESDAY)->toDateString();

        $service = Service::create([
            'name' => 'Ortodoncia Exclusiva Test',
            'slug' => 'ortodoncia-exclusiva-test',
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $dentistMondayOnly = Dentist::create([
            'name' => 'Dra. Solo Lunes',
            'slug' => 'dra-solo-lunes-test',
            'is_active' => true,
            'working_days' => ['monday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '13:00:00',
        ]);

        $dentistTuesdayOnly = Dentist::create([
            'name' => 'Dr. Solo Martes',
            'slug' => 'dr-solo-martes-test',
            'is_active' => true,
            'working_days' => ['tuesday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
        ]);

        // Attach ONLY Dra. Solo Lunes to the service
        $service->dentists()->attach([$dentistMondayOnly->id]);

        // On Tuesday, Dr. Solo Martes is working, but does NOT perform this service.
        // Unified slots for $service on Tuesday MUST NOT include Dr. Solo Martes.
        $resTuesday = $this->getJson("/api/appointments/available-slots?date={$tuesday}&service_id={$service->id}");
        $resTuesday->assertSuccessful();
        $this->assertFalse($resTuesday->json('has_available_slots'));
        $this->assertEmpty($resTuesday->json('slots'));

        // On Monday, Dra. Solo Lunes works and is qualified for this service.
        $resMonday = $this->getJson("/api/appointments/available-slots?date={$monday}&service_id={$service->id}");
        $resMonday->assertSuccessful();
        $this->assertTrue($resMonday->json('has_available_slots'));
        $this->assertNotEmpty($resMonday->json('slots'));
    }

    public function test_booking_with_unqualified_specialist_is_rejected_with_422(): void
    {
        $monday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        $service = Service::create([
            'name' => 'Implante Quirurgico Test',
            'slug' => 'implante-quirurgico-test',
            'duration_minutes' => 45,
            'is_active' => true,
        ]);

        $qualifiedDentist = Dentist::create([
            'name' => 'Dr. Cirujano Calificado',
            'slug' => 'dr-cirujano-calificado',
            'is_active' => true,
            'working_days' => ['monday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
        ]);

        $unqualifiedDentist = Dentist::create([
            'name' => 'Dra. No Hace Implantes',
            'slug' => 'dra-no-hace-implantes',
            'is_active' => true,
            'working_days' => ['monday'],
            'working_hours_start' => '09:00:00',
            'working_hours_end' => '17:00:00',
        ]);

        $service->dentists()->attach([$qualifiedDentist->id]);

        // Try booking with unqualified dentist
        $response = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => $unqualifiedDentist->id,
            'appointment_date' => $monday,
            'appointment_time' => '10:00',
            'patient_name' => 'Paciente Rechazado',
            'patient_email' => 'rechazado@example.com',
            'patient_phone' => '+1555444555',
            'is_first_visit' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['dentist_id']);
        $this->assertDatabaseMissing('appointments', [
            'patient_email' => 'rechazado@example.com',
        ]);

        // Booking with qualified dentist succeeds
        $successResponse = $this->postJson('/appointments', [
            'service_id' => $service->id,
            'dentist_id' => $qualifiedDentist->id,
            'appointment_date' => $monday,
            'appointment_time' => '10:00',
            'patient_name' => 'Paciente Aceptado',
            'patient_email' => 'aceptado@example.com',
            'patient_phone' => '+1555444666',
            'is_first_visit' => 0,
        ]);

        $successResponse->assertStatus(201);
        $this->assertDatabaseHas('appointments', [
            'patient_email' => 'aceptado@example.com',
            'dentist_id' => $qualifiedDentist->id,
        ]);
    }
}
