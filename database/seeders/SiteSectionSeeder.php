<?php

namespace Database\Seeders;

use App\Models\SiteSection;
use Illuminate\Database\Seeder;

class SiteSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $homeSections = [
            [
                'page' => 'home',
                'section_key' => 'hero',
                'name' => 'Portada Principal (Hero)',
                'icon' => 'sparkles',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Tu Sonrisa Merece la Mejor Atención',
                    'title' => 'Odontología Suave, Sin Dolor para Toda tu Familia.',
                    'subtitle' => 'Combinamos escáner digital 3D y tecnología láser avanzada con un trato cálido y humano para devolverte una sonrisa radiante y saludable.',
                    'benefit_1' => 'Odontólogos y Cirujanos Especialistas Certificados',
                    'benefit_2' => 'Tratamientos Láser 100% Indoloros y Sedación Consciente',
                    'button_primary_text' => 'Agendar Consulta Online »',
                    'button_primary_url' => '#appointment-section',
                    'badge_title' => '24 Horas',
                    'badge_subtitle' => 'Urgencias',
                    'image_url' => 'https://masalaserclinic.com/wp-content/uploads/elementor/thumbs/dentist-making-professional-teeth-cleaning-withb-cotton-female-young-patient-dental-office-scaled-rmzddojmvr5b2ndthwo8x3mhck1ct16aflega90jqw.jpg',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'quick_cards',
                'name' => 'Tarjetas de Contacto Rápido',
                'icon' => 'phone',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'emergency_title' => 'Atención de Urgencia Dental',
                    'emergency_subtitle' => 'Dolor agudo, fracturas o emergencias dentales hoy mismo.',
                    'schedule_title' => 'Agenda tu Cita en Línea',
                    'schedule_subtitle' => 'Elige a tu especialista y horario disponible en segundos.',
                    'schedule_button_text' => 'Reservar Cita Ahora',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'about_preview',
                'name' => 'Sobre la Clínica (Resumen)',
                'icon' => 'information-circle',
                'order' => 3,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Conoce Nuestra Clínica',
                    'title' => 'Más de 15 años transformando sonrisas con calidez, ciencia y precisión.',
                    'description' => 'En nuestra clínica creemos que una visita al dentista debe ser una experiencia reconfortante y libre de ansiedad. Contamos con tecnología de diagnóstico digital 3D y quirófanos esterilizados bajo estándares hospitalarios internacionales.',
                    'check_text' => 'Horarios organizados por Turno Mañana y Turno Tarde para tu comodidad.',
                    'button_text' => 'Conocer a los Especialistas »',
                    'button_url' => '#dentists',
                    'image_1_url' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80',
                    'image_2_url' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=600&q=80',
                    'badge_years' => '20+',
                    'badge_text' => 'Años Exp.',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'trust_bar',
                'name' => 'Barra de Confianza y Estadísticas',
                'icon' => 'chart-bar',
                'order' => 4,
                'is_active' => true,
                'content' => [
                    'stat_1_number' => '25,000+',
                    'stat_1_label' => 'Pacientes Felices',
                    'stat_2_number' => '24/7',
                    'stat_2_label' => 'Soporte Urgencias',
                    'stat_3_number' => '18,500+',
                    'stat_3_label' => 'Sonrisas Renovadas',
                    'stat_4_number' => '99.4%',
                    'stat_4_label' => 'Satisfacción Clínica',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'how_it_works',
                'name' => 'Cómo Trabajamos',
                'icon' => 'numbered-list',
                'order' => 5,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Nuestras Soluciones Dentales',
                    'title' => 'Soluciones Dentales Completas con la Mayor Suavidad.',
                    'subtitle' => 'Desde limpiezas preventivas profundas hasta carillas de porcelana ultra finas e implantes guiados en 3D, cuidamos tu salud bucal con precisión quirúrgica y confort total.',
                    'step_1_title' => 'Higiene y Prevención Dental Integral con Ultrasonido Tibio',
                    'step_2_title' => 'Especialistas Certificados en Estética y Cirugía Reconstructiva',
                    'step_3_title' => 'Protocolos Estrictos de Esterilización Quirúrgica Grado Hospital',
                    'step_4_title' => 'Odontología Láser Indolora y Sedación Consciente',
                    'image_1_url' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=800&q=80',
                    'image_2_url' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'testimonials_cases',
                'name' => 'Casos de Éxito y Testimonios',
                'icon' => 'chat-bubble-left-right',
                'order' => 6,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'TESTIMONIOS DE PACIENTES',
                    'title' => 'Lo que Dicen Nuestros Pacientes sobre su Experiencia Dental.',
                    'subtitle' => 'Opinión Verificada de Google & WhatsApp',
                    'image_url' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1000&q=80',
                    'items' => [
                        [
                            'patient_name' => 'Claire Davenport',
                            'treatment' => 'Alineadores Invisibles y Blanqueamiento',
                            'rating' => 5,
                            'comment' => 'La clínica más acogedora y avanzada tecnológicamente que he visitado. La Dra. Rostova me explicó cada detalle de la simulación digital. El equipo es súper profesional y cálido.',
                            'avatar_url' => '',
                        ],
                        [
                            'patient_name' => 'David Morales',
                            'treatment' => 'Implante Dental y Corona Circonio',
                            'rating' => 4.5,
                            'comment' => 'Tenía mucho miedo a la cirugía dental, pero el Dr. Vance y su equipo hicieron que el implante fuera 100% indoloro. La recuperación fue rapidísima. ¡Totalmente recomendados!',
                            'avatar_url' => '',
                        ],
                        [
                            'patient_name' => 'Emma Watson-Lee',
                            'treatment' => 'Blanqueamiento Dental Láser',
                            'rating' => 5,
                            'comment' => 'Llegué con manchas de café de años y salí una hora después con una sonrisa radiante. Cero sensibilidad en los días posteriores. ¡Atención impecable!',
                            'avatar_url' => '',
                        ],
                        [
                            'patient_name' => 'Santiago Ramírez',
                            'treatment' => 'Limpieza y Revisión General',
                            'rating' => 4.5,
                            'comment' => 'Instalaciones ultra modernas y precios transparentes. No intentan venderte cosas innecesarias. Mis dientes se sienten más limpios que nunca.',
                            'avatar_url' => '',
                        ],
                    ],
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'services_catalog',
                'name' => 'Catálogo de Tratamientos',
                'icon' => 'squares-2x2',
                'order' => 7,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Especialidades Odontológicas',
                    'title' => 'Tratamientos integrales para cada necesidad dental',
                    'subtitle' => 'Desde odontología preventiva hasta cirugías e implantología compleja con tecnología de última generación.',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'faqs',
                'name' => 'Preguntas Frecuentes',
                'icon' => 'question-mark-circle',
                'order' => 8,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Dudas Comunes',
                    'title' => 'Preguntas Frecuentes',
                    'subtitle' => 'Todo lo que necesitas saber antes de tu primera consulta odontológica.',
                    'items' => [
                        [
                            'question' => '¿Cómo puedo agendar o reagendar una cita?',
                            'answer' => 'Puedes agendar directamente a través de nuestro formulario web 24/7 o escribiendo a nuestro WhatsApp. Si necesitas reagendar, avísanos con al menos 24 horas de anticipación.',
                        ],
                        [
                            'question' => '¿Dolerán los tratamientos o la limpieza dental?',
                            'answer' => 'No. Nos enfocamos en odontología indolora y confortable. Utilizamos anestesia local computarizada, instrumental ultrasónico tibio y técnicas suaves para garantizar cero dolor.',
                        ],
                        [
                            'question' => '¿Aceptan seguros médicos o planes de pago?',
                            'answer' => '¡Sí! Trabajamos con las principales aseguradoras y ofrecemos planes de financiamiento sin intereses de hasta 12 meses para tratamientos como ortodoncia e implantes.',
                        ],
                        [
                            'question' => '¿Qué debo esperar durante mi primera consulta dental?',
                            'answer' => 'Tu primera cita dura aproximadamente 45 minutos e incluye radiografías digitales HD, escaneo 3D intraoral, revisión periodontal completa y tu plan de tratamiento personalizado.',
                        ],
                    ],
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'cta_booking',
                'name' => 'Módulo de Reserva Online',
                'icon' => 'calendar',
                'order' => 9,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Reserva Online 24/7',
                    'title' => 'Reserva tu Cita Odontológica Online',
                    'subtitle' => 'Selecciona el tratamiento que necesitas y reserva tu turno en el día y horario que mejor se adapte a tu rutina.',
                    'check_1' => 'Confirmación instantánea de cita y recordatorios automáticos por WhatsApp.',
                    'check_2' => 'Especialistas certificados en estética y cirugía reconstructiva.',
                    'check_3' => 'Horarios flexibles organizados por Turno Mañana y Turno Tarde.',
                    'button_text' => 'Reservar Cita Online »',
                    'button_url' => '/reservar',
                    'image_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=600&q=80',
                    'emergency_title' => 'Llamada de Urgencia 24/7',
                    'emergency_subtitle' => 'Atención el mismo día para dolor agudo',
                ],
            ],
        ];

        foreach ($homeSections as $section) {
            SiteSection::updateOrCreate(
                [
                    'page' => $section['page'],
                    'section_key' => $section['section_key'],
                ],
                $section
            );
        }
    }
}
