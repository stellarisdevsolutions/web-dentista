<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClinicSetting;
use App\Models\User;
use Database\Seeders\SiteSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio',
            'phone' => '+1 (555) 123-4567',
            'whatsapp' => '+15551234567',
            'email' => 'info@auradental.com',
            'address' => 'Av. Siempre Viva 742',
        ]);

        $this->seed(SiteSectionSeeder::class);
    }

    public function test_admin_can_access_visual_editor(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/editor');

        $response->assertSuccessful();
        $response->assertSee('Editor Visual');
        $response->assertSee('Portada Principal (Hero)');
        $response->assertSee('Publicar Cambios');
    }

    public function test_non_admin_cannot_access_visual_editor(): void
    {
        $receptionist = User::factory()->create([
            'role' => UserRole::Receptionist,
        ]);

        $response = $this->actingAs($receptionist)->get('/admin/editor');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/editor');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_save_section_updates_and_reflects_on_landing_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'home',
            'sections' => [
                [
                    'section_key' => 'hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'Atención Odontológica VIP',
                        'title' => 'Sonrisas Radiantes y Brillantes 2026',
                        'subtitle' => 'Tratamientos de alta tecnología y sedación consciente para toda la familia.',
                        'benefit_1' => 'Tecnología Láser 100% Indolora',
                        'benefit_2' => 'Doctores Certificados Internacionalmente',
                        'button_primary_text' => 'Reservar Ahora',
                        'badge_title' => '24 Horas',
                        'badge_subtitle' => 'Emergencias',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('site_sections', [
            'page' => 'home',
            'section_key' => 'hero',
            'is_active' => true,
        ]);

        $landingResponse = $this->get('/');
        $landingResponse->assertSuccessful();
        $landingResponse->assertSee('Sonrisas Radiantes y Brillantes 2026');
        $landingResponse->assertSee('Atención Odontológica VIP');
    }

    public function test_landing_page_embeds_editor_bridge_when_preview_param_present(): void
    {
        $response = $this->get('/?editor_preview=1');

        $response->assertSuccessful();
        $response->assertSee('data-cms-section="hero"', false);
        $response->assertSee('cms-floating-badge', false);
    }

    public function test_admin_can_save_testimonials_with_repeater_and_decimal_ratings(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'home',
            'sections' => [
                [
                    'section_key' => 'testimonials_cases',
                    'is_active' => true,
                    'order' => 6,
                    'content' => [
                        'eyebrow' => 'OPINIONES DE PACIENTES VIP',
                        'title' => 'Sonrisas Renovadas con Precisión Láser',
                        'subtitle' => 'Casos Clínicos Reales',
                        'image_url' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95',
                        'items' => [
                            [
                                'patient_name' => 'Valeria Montes',
                                'treatment' => 'Diseño de Sonrisa Digital',
                                'rating' => 4.5,
                                'comment' => 'El cambio en mi estética dental fue asombroso y totalmente sin dolor.',
                                'avatar_url' => '',
                            ],
                            [
                                'patient_name' => 'Carlos Mendoza',
                                'treatment' => 'Implante Guiado por Computadora',
                                'rating' => 5,
                                'comment' => 'Recuperé la confianza al sonreír gracias al equipo de especialistas.',
                                'avatar_url' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
        ]);

        $landing = $this->get('/');
        $landing->assertSuccessful();
        $landing->assertSee('OPINIONES DE PACIENTES VIP');
        $landing->assertSee('Valeria Montes');
        $landing->assertSee('Diseño de Sonrisa Digital');
        $landing->assertSee('El cambio en mi estética dental fue asombroso y totalmente sin dolor.');
    }

    public function test_admin_can_save_faqs_with_repeater(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'home',
            'sections' => [
                [
                    'section_key' => 'faqs',
                    'is_active' => true,
                    'order' => 8,
                    'content' => [
                        'eyebrow' => 'PREGUNTAS FRECUENTES VIP',
                        'title' => 'Dudas Resueltas sobre tu Salud Dental',
                        'subtitle' => 'Atención clara y transparente',
                        'items' => [
                            [
                                'question' => '¿Cuánto dura un blanqueamiento dental láser?',
                                'answer' => 'El tratamiento se realiza en una sola sesión de 45 minutos con resultados inmediatos.',
                            ],
                            [
                                'question' => '¿Qué cuidados debo tener tras un implante?',
                                'answer' => 'Recomendamos dieta blanda durante 48 horas y evitar esfuerzos físicos pesados.',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
        ]);

        $landing = $this->get('/');
        $landing->assertSuccessful();
        $landing->assertSee('PREGUNTAS FRECUENTES VIP');
        $landing->assertSee('Dudas Resueltas sobre tu Salud Dental');
        $landing->assertSee('¿Cuánto dura un blanqueamiento dental láser?');
        $landing->assertSee('El tratamiento se realiza en una sola sesión de 45 minutos con resultados inmediatos.');
    }

    public function test_admin_can_save_cta_booking_section(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'home',
            'sections' => [
                [
                    'section_key' => 'cta_booking',
                    'is_active' => true,
                    'order' => 9,
                    'content' => [
                        'eyebrow' => 'AGENDA VIP 2026',
                        'title' => 'Tu Sonrisa en Manos de Expertos',
                        'subtitle' => 'Turnos inmediatos sin esperas innecesarias.',
                        'check_1' => 'Tecnología de escaneo intraoral 3D HD',
                        'check_2' => 'Atención cálida e indolora',
                        'check_3' => 'Flexibilidad horaria mañana y tarde',
                        'button_text' => 'Agendar Mi Cita Hoy »',
                        'button_url' => '/reservar',
                        'image_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2',
                        'emergency_title' => 'Línea de Urgencias 24 Horas',
                        'emergency_subtitle' => 'Atención prioritaria inmediata',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
        ]);

        $landing = $this->get('/');
        $landing->assertSuccessful();
        $landing->assertSee('AGENDA VIP 2026');
        $landing->assertSee('Tu Sonrisa en Manos de Expertos');
        $landing->assertSee('Agendar Mi Cita Hoy »');
        $landing->assertSee('Línea de Urgencias 24 Horas');
    }
}
