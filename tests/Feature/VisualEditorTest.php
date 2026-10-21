<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClinicSetting;
use App\Models\Dentist;
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

    public function test_empty_benefits_are_hidden_and_do_not_render_default_text(): void
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
                        'eyebrow' => 'Atención VIP',
                        'title' => 'Odontología Avanzada',
                        'subtitle' => 'Clínica con tecnología 3D',
                        'benefit_1' => '',
                        'benefit_2' => 'Solo este beneficio activo',
                        'button_primary_text' => 'Agendar',
                        'badge_title' => '24h',
                        'badge_subtitle' => 'Urgencias',
                    ],
                ],
                [
                    'section_key' => 'cta_booking',
                    'is_active' => true,
                    'order' => 9,
                    'content' => [
                        'eyebrow' => 'Reserva Online',
                        'title' => 'Agenda tu cita',
                        'subtitle' => 'Horarios flexibles',
                        'check_1' => 'Primer check activo',
                        'check_2' => '',
                        'check_3' => '',
                        'button_text' => 'Reservar',
                        'button_url' => '/reservar',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload);

        $response->assertSuccessful();

        $landing = $this->get('/');
        $landing->assertSuccessful();

        // Benefit 1 must be hidden with display: none and must NOT show default text
        $landing->assertDontSee('Odontólogos y Cirujanos Especialistas Certificados');
        $landing->assertSee('Solo este beneficio activo');
        $landing->assertSee('data-cms-row="benefit_1"', false);
        $landing->assertSee('data-cms-row="benefit_2"', false);

        $landingHtml = $landing->getContent();
        $this->assertMatchesRegularExpression('/data-cms-row="benefit_1"[^>]*style="display:\s*none;"/', $landingHtml);

        // CTA booking checks 2 & 3 must be hidden and must not show default text
        $landing->assertSee('Primer check activo');
        $landing->assertDontSee('Especialistas certificados en estética y cirugía reconstructiva.');
        $landing->assertDontSee('Horarios flexibles organizados por Turno Mañana y Turno Tarde.');
        $landing->assertSee('data-cms-row="check_1"', false);
        $landing->assertSee('data-cms-row="check_2"', false);
        $landing->assertSee('data-cms-row="check_3"', false);
        $this->assertMatchesRegularExpression('/data-cms-row="check_2"[^>]*style="display:\s*none;"/', $landingHtml);
        $this->assertMatchesRegularExpression('/data-cms-row="check_3"[^>]*style="display:\s*none;"/', $landingHtml);
    }

    public function test_admin_can_access_about_page_in_visual_editor(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/editor?page=about');

        $response->assertSuccessful();
        $response->assertSee('Portada Nosotros (Hero)');
    }

    public function test_admin_can_update_about_hero_section_and_reflects_on_nosotros_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'HISTORIA DE AURA DENTAL',
                        'title' => '25 Años Transformando la Odontología en Bolivia',
                        'subtitle' => 'Pioneros en odontología digital asistida por computadora y sedación consciente.',
                        'button_primary_text' => 'Ver Nuestro Staff',
                        'button_primary_url' => '#dentists',
                        'button_secondary_text' => 'Pedir Turno Hoy',
                        'button_secondary_url' => '/reservar',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload);

        $response->assertSuccessful();

        $aboutResponse = $this->get('/nosotros');
        $aboutResponse->assertSuccessful();
        $aboutResponse->assertSee('HISTORIA DE AURA DENTAL');
        $aboutResponse->assertSee('25 Años Transformando la Odontología en Bolivia');
        $aboutResponse->assertSee('Pioneros en odontología digital asistida por computadora');
        $aboutResponse->assertSee('Ver Nuestro Staff');
        $aboutResponse->assertSee('Pedir Turno Hoy');
    }

    public function test_about_page_embeds_editor_bridge_when_preview_param_present(): void
    {
        $response = $this->get('/nosotros?editor_preview=1');

        $response->assertSuccessful();
        $response->assertSee('data-cms-section="about_hero"', false);
        $response->assertSee('cms-floating-badge', false);
    }

    public function test_empty_secondary_button_in_about_hero_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'HISTORIA',
                        'title' => 'Nuestra Trayectoria',
                        'subtitle' => 'Excelencia médica comprobada.',
                        'button_primary_text' => 'Solo Botón Primario',
                        'button_primary_url' => '#dentists',
                        'button_secondary_text' => '',
                        'button_secondary_url' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('Solo Botón Primario');
        $response->assertDontSee('Agendar Consulta Online »');
        $this->assertMatchesRegularExpression('/data-cms-row="button_secondary_text"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_update_about_story_section_and_reflects_on_nosotros_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_story',
                    'is_active' => true,
                    'order' => 2,
                    'content' => [
                        'eyebrow' => 'NUESTRA MISIÓN Y VALORES',
                        'title' => 'Tecnología de Punta y Calidez Humana',
                        'description' => 'Tratamientos personalizados para devolverte la seguridad al sonreír.',
                        'feature_1_title' => 'Diagnóstico 3D HD',
                        'feature_1_desc' => 'Visualización milimétrica previa al tratamiento.',
                        'feature_2_title' => 'Sedación Segura',
                        'feature_2_desc' => 'Procedimientos 100% indoloros.',
                        'check_text' => 'Garantía clínica por escrito en cada procedimiento.',
                        'button_text' => 'Conocer Nuestra Historia',
                        'button_url' => '/contacto',
                        'image_1_url' => 'https://example.com/clinic-1.jpg',
                        'image_2_url' => 'https://example.com/clinic-2.jpg',
                        'badge_years' => '30+',
                        'badge_text' => 'Años Cuidando Familias',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('NUESTRA MISIÓN Y VALORES');
        $response->assertSee('Tecnología de Punta y Calidez Humana');
        $response->assertSee('Tratamientos personalizados para devolverte la seguridad al sonreír.');
        $response->assertSee('Diagnóstico 3D HD');
        $response->assertSee('Visualización milimétrica previa al tratamiento.');
        $response->assertSee('Sedación Segura');
        $response->assertSee('Procedimientos 100% indoloros.');
        $response->assertSee('Garantía clínica por escrito en cada procedimiento.');
        $response->assertSee('Conocer Nuestra Historia');
        $response->assertSee('30+');
        $response->assertSee('Años Cuidando Familias');
        $response->assertSee('https://example.com/clinic-1.jpg');
        $response->assertSee('https://example.com/clinic-2.jpg');
    }

    public function test_empty_check_and_features_in_about_story_are_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_story',
                    'is_active' => true,
                    'order' => 2,
                    'content' => [
                        'eyebrow' => 'FILOSOFÍA',
                        'title' => 'Nuestra Visión',
                        'description' => 'Excelencia médica.',
                        'feature_1_title' => 'Solo Valor 1',
                        'feature_1_desc' => 'Descripción del valor 1.',
                        'feature_2_title' => '',
                        'feature_2_desc' => '',
                        'check_text' => '',
                        'button_text' => '',
                        'button_url' => '',
                        'image_1_url' => '',
                        'image_2_url' => '',
                        'badge_years' => '15+',
                        'badge_text' => 'Años',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();

        $response->assertSee('Solo Valor 1');
        $response->assertDontSee('Especialistas Certificados');
        $response->assertDontSee('Contamos con quirófanos digitales, escáner intraoral 3D y sedación consciente.');
        $response->assertDontSee('Agendar Cita con un Especialista »');

        $html = $response->getContent();
        $this->assertMatchesRegularExpression('/data-cms-row="feature_2_title"[^>]*style="display:\s*none;"/', $html);
        $this->assertMatchesRegularExpression('/data-cms-row="check_text"[^>]*style="display:\s*none;"/', $html);
        $this->assertMatchesRegularExpression('/data-cms-row="button_text"[^>]*style="display:\s*none;"/', $html);
    }

    public function test_admin_can_update_about_trustbar_section_and_reflects_on_nosotros_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_trustbar',
                    'is_active' => true,
                    'order' => 3,
                    'content' => [
                        'stat_1_number' => '999+',
                        'stat_1_label' => 'Pacientes Satisfechos',
                        'stat_2_number' => '15K+',
                        'stat_2_label' => 'Tratamientos Exitosos',
                        'stat_3_number' => '500+',
                        'stat_3_label' => 'Implantes Colocados',
                        'stat_4_number' => '99.8%',
                        'stat_4_label' => 'Calidad Acreditada',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('999+');
        $response->assertSee('Pacientes Satisfechos');
        $response->assertSee('15K+');
        $response->assertSee('Tratamientos Exitosos');
        $response->assertSee('500+');
        $response->assertSee('Implantes Colocados');
        $response->assertSee('99.8%');
        $response->assertSee('Calidad Acreditada');
    }

    public function test_empty_stat_in_about_trustbar_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_trustbar',
                    'is_active' => true,
                    'order' => 3,
                    'content' => [
                        'stat_1_number' => '100+',
                        'stat_1_label' => 'Pacientes',
                        'stat_2_number' => '200+',
                        'stat_2_label' => 'Atenciones',
                        'stat_3_number' => '300+',
                        'stat_3_label' => 'Sonrisas',
                        'stat_4_number' => '',
                        'stat_4_label' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('100+');
        $response->assertDontSee('Certificaciones de Calidad');
        $this->assertMatchesRegularExpression('/data-cms-row="stat_4_number"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_update_about_specialists_section_and_reflects_on_about_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_specialists',
                    'is_active' => true,
                    'order' => 4,
                    'content' => [
                        'eyebrow' => 'ESPECIALISTAS DE CLASE MUNDIAL',
                        'title' => 'Conoce a Nuestros Cirujanos y Odontólogos',
                        'subtitle' => 'Un staff de primer nivel certificado por colegios odontológicos internacionales.',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('ESPECIALISTAS DE CLASE MUNDIAL');
        $response->assertSee('Conoce a Nuestros Cirujanos y Odontólogos');
        $response->assertSee('Un staff de primer nivel certificado por colegios odontológicos internacionales.');
    }

    public function test_empty_eyebrow_or_subtitle_in_about_specialists_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'about',
            'sections' => [
                [
                    'section_key' => 'about_specialists',
                    'is_active' => true,
                    'order' => 4,
                    'content' => [
                        'eyebrow' => '',
                        'title' => 'Solo Título Sin Subtítulo',
                        'subtitle' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('Solo Título Sin Subtítulo');
        $response->assertDontSee('CUERPO MÉDICO DE ÉLITE');
        $this->assertMatchesRegularExpression('/data-cms-row="eyebrow"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="subtitle"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_about_specialists_renders_database_dentists(): void
    {
        Dentist::factory()->create([
            'name' => 'Dra. Valentina Montes',
            'specialty' => 'Ortodoncia Invisible',
            'is_active' => true,
        ]);

        $response = $this->get('/nosotros');
        $response->assertSuccessful();
        $response->assertSee('Dra. Valentina Montes');
        $response->assertSee('Ortodoncia Invisible');
    }

    public function test_admin_can_access_services_page_in_visual_editor(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/editor?page=services');

        $response->assertSuccessful();
        $response->assertSee('Portada de Servicios (Hero)');
    }

    public function test_admin_can_update_services_hero_and_reflects_on_services_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'services',
            'sections' => [
                [
                    'section_key' => 'services_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'TRATAMIENTOS DE ALTA TECNOLOGÍA',
                        'title' => 'Especialidades y Procedimientos Clínicos',
                        'subtitle' => 'Diseño digital de sonrisa, implantes guiados y odontología estética avanzada.',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/servicios');
        $response->assertSuccessful();
        $response->assertSee('TRATAMIENTOS DE ALTA TECNOLOGÍA');
        $response->assertSee('Especialidades y Procedimientos Clínicos');
        $response->assertSee('Diseño digital de sonrisa, implantes guiados y odontología estética avanzada.');
    }

    public function test_empty_eyebrow_or_subtitle_in_services_hero_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'services',
            'sections' => [
                [
                    'section_key' => 'services_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => '',
                        'title' => 'Catálogo Completo de Tratamientos',
                        'subtitle' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/servicios');
        $response->assertSuccessful();
        $response->assertSee('Catálogo Completo de Tratamientos');
        $response->assertDontSee('EXCELENCIA CLÍNICA');
        $this->assertMatchesRegularExpression('/data-cms-row="eyebrow"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="subtitle"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_access_blog_page_in_visual_editor(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/editor?page=blog');

        $response->assertSuccessful();
        $response->assertSee('Portada del Blog (Hero)');
        $response->assertSee('Banner Consulta Personalizada');
    }

    public function test_admin_can_update_blog_hero_and_reflects_on_blog_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'blog',
            'sections' => [
                [
                    'section_key' => 'blog_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'ACTUALIDAD ODONTOLÓGICA Y CONSEJOS',
                        'title' => 'Guías Clínicas para Pacientes',
                        'subtitle' => 'Descubre consejos de nuestros cirujanos para mantener una dentadura sana.',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/blog');
        $response->assertSuccessful();
        $response->assertSee('ACTUALIDAD ODONTOLÓGICA Y CONSEJOS');
        $response->assertSee('Guías Clínicas para Pacientes');
        $response->assertSee('Descubre consejos de nuestros cirujanos para mantener una dentadura sana.');
    }

    public function test_empty_eyebrow_or_subtitle_in_blog_hero_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'blog',
            'sections' => [
                [
                    'section_key' => 'blog_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => '',
                        'title' => 'Solo Título del Blog',
                        'subtitle' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/blog');
        $response->assertSuccessful();
        $response->assertSee('Solo Título del Blog');
        $response->assertDontSee('DIVULGACIÓN CIENTÍFICA & SALUD BUCAL');
        $this->assertMatchesRegularExpression('/data-cms-row="eyebrow"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="subtitle"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_update_blog_cta_banner_and_reflects_on_blog_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'blog',
            'sections' => [
                [
                    'section_key' => 'blog_cta',
                    'is_active' => true,
                    'order' => 2,
                    'content' => [
                        'eyebrow' => 'ASESORÍA GRATUITA',
                        'title' => '¿Quieres saber si eres candidato para ortodoncia?',
                        'subtitle' => 'Agenda tu evaluación clínica sin costo y con escaneo 3D incluido.',
                        'button_primary_text' => 'Solicitar Evaluación »',
                        'button_primary_url' => '/reservar',
                        'button_secondary_text' => 'Escribir por WhatsApp »',
                        'button_secondary_url' => '/contacto',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/blog');
        $response->assertSuccessful();
        $response->assertSee('ASESORÍA GRATUITA');
        $response->assertSee('¿Quieres saber si eres candidato para ortodoncia?');
        $response->assertSee('Agenda tu evaluación clínica sin costo y con escaneo 3D incluido.');
        $response->assertSee('Solicitar Evaluación »');
        $response->assertSee('Escribir por WhatsApp »');
    }

    public function test_empty_secondary_button_in_blog_cta_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'blog',
            'sections' => [
                [
                    'section_key' => 'blog_cta',
                    'is_active' => true,
                    'order' => 2,
                    'content' => [
                        'eyebrow' => 'CONSULTA DIRECTA',
                        'title' => 'Cita Inmediata',
                        'subtitle' => 'Atención rápida',
                        'button_primary_text' => 'Reservar Ahora',
                        'button_primary_url' => '/reservar',
                        'button_secondary_text' => '',
                        'button_secondary_url' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/blog');
        $response->assertSuccessful();
        $response->assertSee('Reservar Ahora');
        $this->assertMatchesRegularExpression('/data-cms-row="button_secondary_text"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_access_contact_page_in_visual_editor(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/editor?page=contact');

        $response->assertSuccessful();
        $response->assertSee('Portada de Contacto (Hero)');
    }

    public function test_admin_can_update_contact_hero_and_reflects_on_contact_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'contact',
            'sections' => [
                [
                    'section_key' => 'contact_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'ATENCIÓN CLÍNICA INMEDIATA',
                        'title' => 'Comunícate con Nuestro Equipo Odontológico',
                        'subtitle' => 'Estamos a tu entera disposición para resolver cualquier duda o agendar tu evaluación.',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/contacto');
        $response->assertSuccessful();
        $response->assertSee('ATENCIÓN CLÍNICA INMEDIATA');
        $response->assertSee('Comunícate con Nuestro Equipo Odontológico');
        $response->assertSee('Estamos a tu entera disposición para resolver cualquier duda o agendar tu evaluación.');
    }

    public function test_empty_eyebrow_or_subtitle_in_contact_hero_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'contact',
            'sections' => [
                [
                    'section_key' => 'contact_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => '',
                        'title' => 'Solo Título de Contacto',
                        'subtitle' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/contacto');
        $response->assertSuccessful();
        $response->assertSee('Solo Título de Contacto');
        $response->assertDontSee('ESTAMOS PARA AYUDARTE');
        $this->assertMatchesRegularExpression('/data-cms-row="eyebrow"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="subtitle"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_update_contact_cards_section_and_reflects_on_contact_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'contact',
            'sections' => [
                [
                    'section_key' => 'contact_cards',
                    'is_active' => true,
                    'order' => 2,
                    'content' => [
                        'card_1_title' => 'Línea de Emergencias Dentales',
                        'card_1_desc' => 'Atención telefónica permanente para urgencias y dolor agudo.',
                        'card_2_title' => 'Atención al Paciente por Correo',
                        'card_2_desc' => 'Escríbenos para presupuestos, seguros o preguntas generales.',
                        'card_3_title' => 'Nuestras Instalaciones Clínicas',
                        'card_3_desc' => 'Contamos con estacionamiento propio y acceso accesible.',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/contacto');
        $response->assertSuccessful();
        $response->assertSee('Línea de Emergencias Dentales');
        $response->assertSee('Atención telefónica permanente para urgencias y dolor agudo.');
        $response->assertSee('Atención al Paciente por Correo');
        $response->assertSee('Escríbenos para presupuestos, seguros o preguntas generales.');
        $response->assertSee('Nuestras Instalaciones Clínicas');
        $response->assertSee('Contamos con estacionamiento propio y acceso accesible.');

        // Verify registered clinic details are present
        $response->assertSee('+1 (555) 123-4567');
        $response->assertSee('info@auradental.com');
        $response->assertSee('Av. Siempre Viva 742');

        // Verify "Respuesta en menos de 2 horas hábiles" has been removed
        $response->assertDontSee('Respuesta en menos de 2 horas hábiles');
    }

    public function test_empty_card_desc_in_contact_cards_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'contact',
            'sections' => [
                [
                    'section_key' => 'contact_cards',
                    'is_active' => true,
                    'order' => 2,
                    'content' => [
                        'card_1_title' => 'Solo Teléfono',
                        'card_1_desc' => '',
                        'card_2_title' => 'Solo Email',
                        'card_2_desc' => '',
                        'card_3_title' => 'Solo Ubicación',
                        'card_3_desc' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/contacto');
        $response->assertSuccessful();
        $response->assertSee('Solo Teléfono');
        $this->assertMatchesRegularExpression('/data-cms-row="card_1_desc"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="card_2_desc"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="card_3_desc"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_update_contact_faqs_section_and_reflects_on_contact_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'contact',
            'sections' => [
                [
                    'section_key' => 'contact_faqs',
                    'is_active' => true,
                    'order' => 3,
                    'content' => [
                        'eyebrow' => 'PREGUNTAS COMUNES DE CONTACTO',
                        'title' => 'Dudas Frecuentes al Comunicarte',
                        'subtitle' => 'Encuentra soluciones rápidas sobre tu visita clínica.',
                        'items' => [
                            [
                                'question' => '¿Tienen disponibilidad de citas en el mismo día?',
                                'answer' => 'Sí, disponemos de cupos prioritarios matutinos y vespertinos para atenciones de emergencia.',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/contacto');
        $response->assertSuccessful();
        $response->assertSee('PREGUNTAS COMUNES DE CONTACTO');
        $response->assertSee('Dudas Frecuentes al Comunicarte');
        $response->assertSee('Encuentra soluciones rápidas sobre tu visita clínica.');
        $response->assertSee('¿Tienen disponibilidad de citas en el mismo día?');
        $response->assertSee('Sí, disponemos de cupos prioritarios matutinos y vespertinos para atenciones de emergencia.');
    }

    public function test_empty_eyebrow_or_subtitle_in_contact_faqs_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'contact',
            'sections' => [
                [
                    'section_key' => 'contact_faqs',
                    'is_active' => true,
                    'order' => 3,
                    'content' => [
                        'eyebrow' => '',
                        'title' => 'Solo Título FAQ',
                        'subtitle' => '',
                        'items' => [],
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/contacto');
        $response->assertSuccessful();
        $response->assertSee('Solo Título FAQ');
        $response->assertDontSee('PREGUNTAS FRECUENTES');
        $this->assertMatchesRegularExpression('/data-cms-row="eyebrow"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="subtitle"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_admin_can_access_visual_editor_for_schedule_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/editor?page=schedule');

        $response->assertSuccessful();
        $response->assertSee('Editor Visual');
        $response->assertSee('schedule_hero');
        $response->assertSee('Portada de Reservas (Hero)');
    }

    public function test_admin_can_update_schedule_hero_section_and_changes_reflect_on_schedule_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'schedule',
            'sections' => [
                [
                    'section_key' => 'schedule_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => 'RESERVA EXPRESS 24/7',
                        'title' => 'Tu Sonrisa en Manos de Expertos',
                        'subtitle' => 'Selecciona a tu odontólogo de preferencia y agenda en segundos.',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/reservar');
        $response->assertSuccessful();
        $response->assertSee('RESERVA EXPRESS 24/7');
        $response->assertSee('Tu Sonrisa en Manos de Expertos');
        $response->assertSee('Selecciona a tu odontólogo de preferencia y agenda en segundos.');
    }

    public function test_empty_eyebrow_or_subtitle_in_schedule_hero_is_hidden(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $payload = [
            'page' => 'schedule',
            'sections' => [
                [
                    'section_key' => 'schedule_hero',
                    'is_active' => true,
                    'order' => 1,
                    'content' => [
                        'eyebrow' => '',
                        'title' => 'Título de Reserva Único',
                        'subtitle' => '',
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->postJson('/admin/editor/save', $payload)
            ->assertSuccessful();

        $response = $this->get('/reservar');
        $response->assertSuccessful();
        $response->assertSee('Título de Reserva Único');
        $response->assertDontSee('RESERVA ONLINE 24/7');
        $this->assertMatchesRegularExpression('/data-cms-row="eyebrow"[^>]*style="display:\s*none;"/', $response->getContent());
        $this->assertMatchesRegularExpression('/data-cms-row="subtitle"[^>]*style="display:\s*none;"/', $response->getContent());
    }

    public function test_schedule_page_includes_editor_bridge_when_previewing(): void
    {
        $response = $this->get('/reservar?editor_preview=1');
        $response->assertSuccessful();
        $response->assertSee('data-cms-section="schedule_hero"', false);
        $response->assertSee('cms-floating-badge', false);
    }

    public function test_editor_bridge_disables_navbar_and_footer_navigation_in_preview(): void
    {
        $previewResponse = $this->get('/?editor_preview=1');
        $previewResponse->assertSuccessful();
        $previewResponse->assertSee('CMS_NAVBAR_CLICKED', false);
        $previewResponse->assertSee('header a, footer a', false);
        $previewResponse->assertSee('cursor: default !important', false);

        $regularResponse = $this->get('/');
        $regularResponse->assertSuccessful();
        $regularResponse->assertDontSee('CMS_NAVBAR_CLICKED', false);
        $regularResponse->assertDontSee('header a, footer a', false);
    }

    public function test_admin_can_fetch_page_sections_dynamically_via_json(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->getJson('/admin/editor?page=about');

        $response->assertSuccessful();
        $response->assertJsonPath('currentPage', 'about');
        $response->assertJsonStructure([
            'currentPage',
            'availablePages',
            'sections',
        ]);
    }
}
