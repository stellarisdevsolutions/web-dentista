<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Banner;
use App\Models\ClinicSetting;
use App\Models\Dentist;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Service;
use App\Models\SmileCase;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DentalClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clinic Settings
        ClinicSetting::updateOrCreate(
            ['id' => 1],
            [
                'clinic_name' => 'Aura Dental Studio',
                'tagline' => 'Odontología moderna, integral y sin dolor para sonrisas radiantes.',
                'phone' => '+1 (555) 234-5678',
                'whatsapp' => '+15552345678',
                'email' => 'hello@auradental.com',
                'address' => '742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210',
                'google_maps_url' => 'https://maps.google.com/?q=Beverly+Hills+CA',
                'opening_hours' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM\nDomingo: Solo Urgencias con Previa Cita",
                'emergency_phone' => '+1 (555) 911-7775',
                'logo_url' => null,
                'show_language_switcher' => false,
                'social_links' => [
                    'instagram' => 'https://instagram.com/auradental',
                    'facebook' => 'https://facebook.com/auradental',
                    'tiktok' => 'https://tiktok.com/@auradental',
                ],
            ]
        );

        // 2. Services
        $servicesData = [
            [
                'slug' => 'general-checkup-consultation',
                'name' => 'Revisión Dental General y Diagnóstico',
                'category' => 'Odontología General',
                'short_description' => 'Evaluación bucal completa, diagnóstico digital 3D y presupuesto sin compromiso.',
                'description' => 'La consulta y chequeo general es el punto de partida para una salud bucodental óptima. Incluye exploración clínica de dientes y encías, detección precoz de caries, revisión de oclusión y plan de tratamiento personalizado.',
                'image' => 'services/01M5JNAKPJ8DE5A0AB46QYR6BN.webp',
                'price' => 45.00,
                'duration_minutes' => 30,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'slug' => 'comprehensive-exam-cleaning',
                'name' => 'Examen Integral y Profilaxis Profunda',
                'category' => 'General y Prevención',
                'short_description' => 'Escaneo digital diagnóstico 3D, limpieza ultrasónica indolora y despistaje preventivo oral.',
                'description' => 'Nuestra evaluación completa utiliza escáner intraoral 3D, radiografías digitales de ultra baja radiación y limpieza con ultrasonido para eliminar sarro y placa sin molestias.',
                'image' => 'services/01M5JNSN4XYJFHFTGME0ZXS72F.webp',
                'price' => 85.00,
                'duration_minutes' => 45,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'slug' => 'laser-teeth-whitening',
                'name' => 'Blanqueamiento Dental Láser',
                'category' => 'Odontología Estética',
                'short_description' => 'Hasta 8 tonos más blanco en una sola sesión de 60 minutos con tecnología de luz fría.',
                'description' => 'Blanqueamiento clínico profesional sin dañar el esmalte y con mínima sensibilidad. Empleamos agentes desensibilizantes y activación láser para resultados luminosos.',
                'image' => 'services/01M5JP8TQJKQ5BDD44M4RB9PCA.webp',
                'price' => 240.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 3,
            ],
            [
                'slug' => 'clear-aligners-orthodontics',
                'name' => 'Alineadores Invisibles y Ortodoncia',
                'category' => 'Ortodoncia',
                'short_description' => 'Alineadores transparentes y removibles para adolescentes y adultos con simulación digital.',
                'description' => 'Corrige la posición de tus dientes sin alambres ni brackets. Visualiza la simulación 3D de tu sonrisa final antes de iniciar el tratamiento con férulas transparentes.',
                'image' => 'services/01M5JPK5SSC4KW3RK1RWGRESR1.webp',
                'price' => 1800.00,
                'duration_minutes' => 45,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 4,
            ],
            [
                'slug' => 'dental-implants',
                'name' => 'Implantes Dentales Guiados',
                'category' => 'Implantología y Cirugía',
                'short_description' => 'Reemplazo dental permanente y natural con cirugía guiada por tomografía computarizada.',
                'description' => 'Recupera piezas dentales ausentes con implantes biocompatibles de titanio o cerámica guiados por ordenador, restableciendo función masticatoria y estética total.',
                'image' => 'services/01M5JPR7PTPVP8YWB0RB42S1T5.webp',
                'price' => 950.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 5,
            ],
            [
                'slug' => 'porcelain-veneers',
                'name' => 'Carillas de Porcelana y Diseño de Sonrisa',
                'category' => 'Odontología Estética',
                'short_description' => 'Láminas ultrafinas de porcelana pura que corrigen forma, color y separaciones a la perfección.',
                'description' => 'Carillas de porcelana diseñadas a medida según la simetría de tu rostro. Altamente resistentes a manchas, duraderas y con brillo 100% natural.',
                'image' => 'services/01M5JPSPCTZ78D7NTJG6TAT89D.webp',
                'price' => 420.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => false,
                'order' => 6,
            ],
            [
                'slug' => 'emergency-dental-care',
                'name' => 'Atención de Urgencia Dental',
                'category' => 'Urgencias y Dolor',
                'short_description' => 'Alivio el mismo día para dolor agudo, fracturas dentales, coronas desprendidas o infecciones.',
                'description' => 'Atención prioritaria inmediata para diagnosticar y controlar el dolor dental agudo, tratar abscesos y reparar fracturas con anestesia indolora.',
                'image' => 'services/01M5JQ29S9WFB4TYJA2XF0G9J5.webp',
                'price' => 90.00,
                'duration_minutes' => 30,
                'allows_online_booking' => true,
                'is_featured' => false,
                'order' => 7,
            ],
        ];

        $createdServices = [];
        foreach ($servicesData as $item) {
            $createdServices[$item['slug']] = Service::updateOrCreate(['slug' => $item['slug']], $item);
        }

        // 3. Dentists
        $dentistsData = [
            [
                'name' => 'Dr. Julian Vance, DDS',
                'slug' => 'dr-julian-vance',
                'title' => 'Director de Implantología y Cirugía Oral',
                'license_number' => 'DDS-94821',
                'specialty' => 'Implantología y Cirugía Reconstructiva',
                'bio' => 'Con más de 14 años de experiencia quirúrgica y posgrados en NYU, el Dr. Vance se especializa en colocación de implantes guiados por ordenador y regeneración ósea con mínima molestia.',
                'photo' => 'dentists/01M5JN5N30ARVWEKMR7Y39ADKK.webp',
                'email' => 'dr.vance@auradental.com',
                'phone' => '+1 (555) 234-5671',
                'working_days' => ['monday', 'tuesday', 'thursday', 'friday'],
                'working_hours_start' => '09:00:00',
                'working_hours_end' => '17:30:00',
                'weekly_schedule' => [
                    'monday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '17:30'],
                    'tuesday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '17:30'],
                    'thursday' => ['is_working' => true, 'morning_start' => null, 'morning_end' => null, 'afternoon_start' => '14:00', 'afternoon_end' => '18:00'],
                    'friday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => null, 'afternoon_end' => null],
                ],
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Dr. Elena Rostova, DMD',
                'slug' => 'dr-elena-rostova',
                'title' => 'Ortodoncista Principal y Especialista en Estética',
                'license_number' => 'DMD-61028',
                'specialty' => 'Invisalign y Diseño Digital de Sonrisas',
                'bio' => 'La Dra. Rostova es proveedora certificada Diamond Apex de Invisalign. Combina precisión biomecánica con armonía facial para crear sonrisas sanas y seguras.',
                'photo' => 'dentists/01M5JMYNJ69H60ZH2V1DEMVWKE.webp',
                'email' => 'dr.rostova@auradental.com',
                'phone' => '+1 (555) 234-5672',
                'working_days' => ['monday', 'wednesday', 'friday', 'saturday'],
                'working_hours_start' => '08:30:00',
                'working_hours_end' => '18:30:00',
                'weekly_schedule' => [
                    'monday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '18:00'],
                    'wednesday' => ['is_working' => true, 'morning_start' => null, 'morning_end' => null, 'afternoon_start' => '14:00', 'afternoon_end' => '18:30'],
                    'friday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => null, 'afternoon_end' => null],
                    'saturday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => null, 'afternoon_end' => null],
                ],
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Dr. Marcus Sterling, DDS',
                'slug' => 'dr-marcus-sterling',
                'title' => 'Endodoncista y Odontología Restauradora',
                'license_number' => 'DDS-73419',
                'specialty' => 'Tratamientos de Conducto Microscópicos',
                'bio' => 'El Dr. Sterling emplea microscopía dental de alta magnificación e imagen 3D para preservar dientes naturales mediante endodoncias prácticamente indoloras.',
                'photo' => 'dentists/01M5JMZJBEFET4WGKCTNT31SEP.webp',
                'email' => 'dr.sterling@auradental.com',
                'phone' => '+1 (555) 234-5673',
                'working_days' => ['tuesday', 'wednesday', 'friday'],
                'working_hours_start' => '09:00:00',
                'working_hours_end' => '17:00:00',
                'weekly_schedule' => [
                    'tuesday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '17:00'],
                    'wednesday' => ['is_working' => true, 'morning_start' => '09:00', 'morning_end' => '13:00', 'afternoon_start' => null, 'afternoon_end' => null],
                    'friday' => ['is_working' => true, 'morning_start' => null, 'morning_end' => null, 'afternoon_start' => '14:00', 'afternoon_end' => '17:00'],
                ],
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Dr. Sofia Chen, DDS',
                'slug' => 'dr-sofia-chen',
                'title' => 'Odontopediatra y Prevención Familiar',
                'license_number' => 'DDS-88204',
                'specialty' => 'Atención Infantil y Odontología sin Ansiedad',
                'bio' => 'Cálida, paciente y comprometida en transformar cada visita al dentista en una experiencia agradable y positiva tanto para niños como para adultos ansiosos.',
                'photo' => 'dentists/01M5JN0C9TTZFNPGZXMP4GH6JW.webp',
                'email' => 'dr.chen@auradental.com',
                'phone' => '+1 (555) 234-5674',
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'working_hours_start' => '08:30:00',
                'working_hours_end' => '16:30:00',
                'weekly_schedule' => [
                    'monday' => ['is_working' => true, 'morning_start' => '08:30', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '16:30'],
                    'tuesday' => ['is_working' => true, 'morning_start' => '08:30', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '16:30'],
                    'wednesday' => ['is_working' => true, 'morning_start' => '08:30', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '16:30'],
                    'thursday' => ['is_working' => true, 'morning_start' => '08:30', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '16:30'],
                    'friday' => ['is_working' => true, 'morning_start' => '08:30', 'morning_end' => '13:00', 'afternoon_start' => '14:00', 'afternoon_end' => '16:30'],
                ],
                'is_active' => true,
                'order' => 4,
            ],
        ];

        $createdDentists = [];
        foreach ($dentistsData as $item) {
            $createdDentists[$item['slug']] = Dentist::updateOrCreate(['slug' => $item['slug']], $item);
        }

        // Link treatments/services to specialists according to clinical scope:
        $createdDentists['dr-elena-rostova']->services()->sync([
            $createdServices['general-checkup-consultation']->id,
            $createdServices['clear-aligners-orthodontics']->id,
            $createdServices['porcelain-veneers']->id,
            $createdServices['laser-teeth-whitening']->id,
        ]);

        $createdDentists['dr-julian-vance']->services()->sync([
            $createdServices['general-checkup-consultation']->id,
            $createdServices['dental-implants']->id,
            $createdServices['emergency-dental-care']->id,
            $createdServices['comprehensive-exam-cleaning']->id,
        ]);

        $createdDentists['dr-marcus-sterling']->services()->sync([
            $createdServices['general-checkup-consultation']->id,
            $createdServices['emergency-dental-care']->id,
            $createdServices['comprehensive-exam-cleaning']->id,
        ]);

        $createdDentists['dr-sofia-chen']->services()->sync([
            $createdServices['general-checkup-consultation']->id,
            $createdServices['comprehensive-exam-cleaning']->id,
            $createdServices['laser-teeth-whitening']->id,
        ]);

        // 4. Appointments
        $appointmentsData = [
            [
                'code' => 'CIT-2026-A109',
                'service_id' => $createdServices['comprehensive-exam-cleaning']->id,
                'dentist_id' => $createdDentists['dr-sofia-chen']->id,
                'patient_name' => 'Michael Lawson',
                'patient_email' => 'michael.lawson@example.com',
                'patient_phone' => '+15554921029',
                'appointment_date' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'appointment_time' => '10:00',
                'duration_minutes' => 45,
                'is_first_visit' => true,
                'symptoms' => 'Revisión periódica y profilaxis dental profunda. Ligera sensibilidad.',
                'internal_notes' => 'El paciente solicita limpieza suave. Primera vez en la clínica.',
                'preferred_locale' => 'es',
                'status' => 'confirmed',
            ],
            [
                'code' => 'CIT-2026-B244',
                'service_id' => $createdServices['clear-aligners-orthodontics']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'patient_name' => 'Camila Fernandez',
                'patient_email' => 'camila.fernandez@example.com',
                'patient_phone' => '+15557819920',
                'appointment_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'appointment_time' => '11:30',
                'duration_minutes' => 45,
                'is_first_visit' => false,
                'symptoms' => 'Evaluación para alineadores transparentes. Consulta de presupuesto.',
                'internal_notes' => 'Paciente interesada en financiamiento a 12 meses.',
                'preferred_locale' => 'es',
                'status' => 'pending',
            ],
            [
                'code' => 'CIT-2026-C381',
                'service_id' => $createdServices['laser-teeth-whitening']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'patient_name' => 'Oliver Bennett',
                'patient_email' => 'oliver.b@example.com',
                'patient_phone' => '+15553018892',
                'appointment_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'appointment_time' => '14:00',
                'duration_minutes' => 60,
                'is_first_visit' => true,
                'symptoms' => 'Matrimonio en 3 semanas, interesado en paquete de blanqueamiento láser.',
                'internal_notes' => 'Código especial de promoción aplicado.',
                'preferred_locale' => 'es',
                'status' => 'confirmed',
            ],
            [
                'code' => 'CIT-2026-D492',
                'service_id' => $createdServices['dental-implants']->id,
                'dentist_id' => $createdDentists['dr-julian-vance']->id,
                'patient_name' => 'Roberto Gómez',
                'patient_email' => 'roberto.gomez@example.com',
                'patient_phone' => '+15556632198',
                'appointment_date' => Carbon::today()->subDays(2)->format('Y-m-d'),
                'appointment_time' => '09:30',
                'duration_minutes' => 60,
                'is_first_visit' => false,
                'symptoms' => 'Control postquirúrgico de implante en molar superior derecho.',
                'internal_notes' => 'Cicatrización óptima. Se toma radiografía periapical de control.',
                'preferred_locale' => 'es',
                'status' => 'completed',
            ],
            [
                'code' => 'CIT-2026-E510',
                'service_id' => $createdServices['emergency-dental-care']->id,
                'dentist_id' => $createdDentists['dr-marcus-sterling']->id,
                'patient_name' => 'Sarah Jenkins',
                'patient_email' => 'sarah.j@example.com',
                'patient_phone' => '+15551928374',
                'appointment_date' => Carbon::today()->format('Y-m-d'),
                'appointment_time' => '16:00',
                'duration_minutes' => 30,
                'is_first_visit' => true,
                'symptoms' => 'Dolor agudo en molar inferior al beber líquidos fríos.',
                'internal_notes' => 'Horario de urgencia reservado con prioridad.',
                'preferred_locale' => 'es',
                'status' => 'pending',
            ],
        ];

        foreach ($appointmentsData as $item) {
            Appointment::updateOrCreate(['code' => $item['code']], $item);
        }

        // 5. Smile Cases (Before & After)
        $smileCasesData = [
            [
                'title' => 'Alineación Dental Completa con Alineadores',
                'service_id' => $createdServices['clear-aligners-orthodontics']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'before_image' => 'cases/case-1-before.jpg',
                'after_image' => 'cases/case-1-after.jpg',
                'description' => 'Apiñamiento severo y sobremordida corregidos en 9 meses sin brackets metálicos.',
                'patient_testimonial' => '¡Nunca imaginé que mis dientes pudieran quedar tan rectos de forma tan cómoda y rápida!',
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'title' => 'Transformación Estética con Carillas de Porcelana',
                'service_id' => $createdServices['porcelain-veneers']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'before_image' => 'cases/case-2-before.jpg',
                'after_image' => 'cases/case-2-after.jpg',
                'description' => 'Corrección de incisivos fracturados, decoloración y diastema con 8 carillas personalizadas.',
                'patient_testimonial' => 'El equipo transformó mi confianza por completo. El resultado es súper natural y brillante.',
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'Reemplazo Dental con Implante de Titanio',
                'service_id' => $createdServices['dental-implants']->id,
                'dentist_id' => $createdDentists['dr-julian-vance']->id,
                'before_image' => 'cases/case-3-before.jpg',
                'after_image' => 'cases/case-3-after.jpg',
                'description' => 'Cirugía guiada sin colgajo restaurando función masticatoria y armonía de la encía.',
                'patient_testimonial' => 'La cirugía fue rapidísima e indolora. Se siente exactamente como mi diente original.',
                'is_featured' => true,
                'order' => 3,
            ],
        ];

        foreach ($smileCasesData as $item) {
            SmileCase::updateOrCreate(['title' => $item['title']], $item);
        }

        // 6. Testimonials
        $testimonialsData = [
            [
                'patient_name' => 'Claire Davenport',
                'treatment' => 'Alineadores Invisibles y Blanqueamiento',
                'rating' => 5,
                'comment' => 'La clínica más acogedora y avanzada tecnológicamente que he visitado. La Dra. Rostova me explicó cada detalle de la simulación digital. El equipo es súper profesional y cálido.',
                'source' => 'Google Reviews',
                'is_verified' => true,
                'order' => 1,
            ],
            [
                'patient_name' => 'David Morales',
                'treatment' => 'Implante Dental y Corona',
                'rating' => 5,
                'comment' => 'Tenía mucho miedo a la cirugía dental, pero el Dr. Vance y su equipo hicieron que el implante fuera 100% indoloro. La recuperación fue rapidísima. ¡Totalmente recomendados!',
                'source' => 'Verified Patient',
                'is_verified' => true,
                'order' => 2,
            ],
            [
                'patient_name' => 'Emma Watson-Lee',
                'treatment' => 'Blanqueamiento Dental Láser',
                'rating' => 5,
                'comment' => 'Llegué con manchas de café de años y salí una hora después con una sonrisa radiante. Cero sensibilidad en los días posteriores. ¡Atención impecable!',
                'source' => 'Google Reviews',
                'is_verified' => true,
                'order' => 3,
            ],
            [
                'patient_name' => 'Santiago Ramírez',
                'treatment' => 'Limpieza y Revisión General',
                'rating' => 5,
                'comment' => 'Instalaciones ultra modernas y precios transparentes. No intentan venderte cosas innecesarias. Mis dientes se sienten más limpios que nunca.',
                'source' => 'Verified Patient',
                'is_verified' => true,
                'order' => 4,
            ],
        ];

        foreach ($testimonialsData as $item) {
            Testimonial::updateOrCreate(['patient_name' => $item['patient_name']], $item);
        }

        // 7. Banners
        $bannersData = [
            [
                'badge' => 'Especial de Primavera',
                'title' => 'Transforma tu Sonrisa con Odontología Moderna y sin Dolor',
                'subtitle' => 'Escaneo intraoral 3D y consulta de valoración sin costo con nuestros especialistas certificados.',
                'button_text' => 'Agenda tu Consulta',
                'button_url' => '#booking',
                'background_image' => 'media/01M5JGJHXTGN38D2Y1EZHHBZ9W.webp',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'badge' => 'Ortodoncia Invisible',
                'title' => 'Consigue la Sonrisa Alineada que Mereces sin Brackets Metálicos',
                'subtitle' => 'Alineadores transparentes a medida desde $150/mes con planes de financiamiento sin intereses.',
                'button_text' => 'Conocer Alineadores',
                'button_url' => '#services',
                'background_image' => 'media/01M5JGJ4J79Z8WR476FB9JAPK4.webp',
                'is_active' => true,
                'order' => 2,
            ],
        ];

        foreach ($bannersData as $item) {
            Banner::updateOrCreate(['title' => $item['title']], $item);
        }

        // 8. FAQs
        $faqsData = [
            [
                'category' => 'Citas y Reservas',
                'question' => '¿Cómo puedo agendar o reagendar una cita?',
                'answer' => 'Puedes agendar directamente a través de nuestro formulario web 24/7 o escribiendo a nuestro WhatsApp. Si necesitas reagendar, avísanos con al menos 24 horas de anticipación.',
                'order' => 1,
            ],
            [
                'category' => 'Tratamientos y Tecnología',
                'question' => '¿Dolerán los tratamientos o la limpieza dental?',
                'answer' => 'No. Nos enfocamos en odontología indolora y confortable. Utilizamos anestesia local computarizada, instrumental ultrasónico tibio y técnicas suaves para garantizar cero dolor.',
                'order' => 2,
            ],
            [
                'category' => 'Precios y Pagos',
                'question' => '¿Aceptan seguros médicos o planes de pago?',
                'answer' => '¡Sí! Trabajamos con las principales aseguradoras y ofrecemos planes de financiamiento sin intereses de hasta 12 meses para tratamientos como ortodoncia e implantes.',
                'order' => 3,
            ],
            [
                'category' => 'Primera Visita',
                'question' => '¿Qué debo esperar durante mi primera consulta dental?',
                'answer' => 'Tu primera cita dura aproximadamente 45 minutos e incluye radiografías digitales HD, escaneo 3D intraoral, revisión periodontal completa y tu plan de tratamiento personalizado.',
                'order' => 4,
            ],
        ];

        foreach ($faqsData as $item) {
            Faq::updateOrCreate(['question' => $item['question']], $item);
        }

        // 9. Blog Posts
        $postsData = [
            [
                'slug' => 'how-to-care-for-clear-aligners',
                'title' => 'Consejos Clave para Cuidar tus Alineadores Invisibles: Lo que Recomiendan los Dentistas',
                'category' => 'Ortodoncia',
                'excerpt' => 'Aprende los hábitos diarios de higiene para evitar manchas, olores y mantener tus férulas transparentes impecables durante tu tratamiento.',
                'content' => 'Usar alineadores transparentes es una de las maneras más estéticas y cómodas de alinear tu dentadura. Sin embargo, su cuidado diario es clave para evitar sarro y olores. Enjuágalos siempre con agua templada al retirarlos, nunca uses agua caliente porque puede deformar el plástico termoconformado, y cepíllalos suavemente con jabón neutro todos los días.',
                'cover_image' => 'blog/01M5JQC9FNTQ2H2Z4JFXGG8T7P.webp',
                'published_at' => Carbon::now()->subDays(5),
                'is_published' => true,
            ],
            [
                'slug' => 'teeth-whitening-facts-vs-myths',
                'title' => 'Blanqueamiento Dental: Mitos y Verdades que Debes Saber Antes de Realizártelo',
                'category' => 'Odontología Estética',
                'excerpt' => '¿El blanqueamiento daña el esmalte? ¿Cuánto duran los resultados del láser? Desmentimos mitos populares de internet y explicamos la realidad clínica.',
                'content' => 'Un mito común es que el blanqueamiento clínico desgasta el esmalte dental. Cuando es supervisado por odontólogos, los geles de peróxido actúan oxidando los pigmentos internos sin alterar la dureza mineral del diente. Es vital evitar remedios caseros como carbón activado o limón, ya que son altamente abrasivos y desgastan el esmalte de forma irreversible.',
                'cover_image' => 'blog/01M5JQBJJVNSMB7S37EWEY762G.webp',
                'published_at' => Carbon::now()->subDays(12),
                'is_published' => true,
            ],
            [
                'slug' => 'why-regular-dental-checkups-matter',
                'title' => 'Por Qué Esperar a que Duela es el Error Dental más Costoso',
                'category' => 'Prevención',
                'excerpt' => 'La caries en etapas tempranas rara vez produce dolor. Descubre cómo las revisiones preventivas semestrales evitan tratamientos invasivos y altos costos.',
                'content' => 'El esmalte no tiene terminaciones nerviosas, por lo que una caries puede avanzar silenciosamente durante meses antes de alcanzar la pulpa y provocar dolor. Las revisiones preventivas periódicas permiten detectar y resolver problemas microscópicos de forma rápida, conservadora y a una fracción del costo.',
                'cover_image' => 'blog/01M5JQD9DS1T7P8FBFKRAWRAK8.webp',
                'published_at' => Carbon::now()->subDays(20),
                'is_published' => true,
            ],
        ];

        foreach ($postsData as $item) {
            Post::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
