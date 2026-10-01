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
                'tagline_en' => 'Modern, gentle & pain-free dental care for radiant smiles.',
                'tagline_es' => 'Odontología moderna, integral y sin dolor para sonrisas radiantes.',
                'phone' => '+1 (555) 234-5678',
                'whatsapp' => '+15552345678',
                'email' => 'hello@auradental.com',
                'address' => '742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210',
                'google_maps_url' => 'https://maps.google.com/?q=Beverly+Hills+CA',
                'opening_hours_en' => "Monday - Friday: 8:00 AM - 7:00 PM\nSaturday: 9:00 AM - 3:00 PM\nSunday: Emergency Appointments Only",
                'opening_hours_es' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM\nDomingo: Solo Urgencias con Previa Cita",
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
                'slug' => 'comprehensive-exam-cleaning',
                'name_en' => 'Comprehensive Exam & Prophylaxis',
                'name_es' => 'Examen Integral y Profilaxis Profunda',
                'category_en' => 'General & Preventive',
                'category_es' => 'General y Prevención',
                'short_description_en' => 'Complete digital diagnostic scanning, gentle ultrasonic cleaning, and oral cancer screening.',
                'short_description_es' => 'Escaneo digital diagnóstico 3D, limpieza ultrasónica indolora y despistaje preventivo oral.',
                'description_en' => 'Our comprehensive oral evaluation utilizes 3D intraoral digital scanners, high-definition digital x-rays with ultra-low radiation, and ultrasonic scaling to remove plaque and calculus comfortably.',
                'description_es' => 'Nuestra evaluación completa utiliza escáner intraoral 3D, radiografías digitales de ultra baja radiación y limpieza con ultrasonido para eliminar sarro y placa sin molestias.',
                'price' => 85.00,
                'duration_minutes' => 45,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'slug' => 'laser-teeth-whitening',
                'name_en' => 'Laser Teeth Whitening',
                'name_es' => 'Blanqueamiento Dental Láser',
                'category_en' => 'Cosmetic Dentistry',
                'category_es' => 'Odontología Estética',
                'short_description_en' => 'Up to 8 shades whiter in a single 60-minute session with advanced cold-light technology.',
                'short_description_es' => 'Hasta 8 tonos más blanco en una sola sesión de 60 minutos con tecnología de luz fría.',
                'description_en' => 'Experience professional dental whitening with zero enamel damage and minimal sensitivity. We use specialized desensitizing agents and advanced laser activation for dazzling results.',
                'description_es' => 'Blanqueamiento clínico profesional sin dañar el esmalte y con mínima sensibilidad. Empleamos agentes desensibilizantes y activación láser para resultados luminosos.',
                'price' => 240.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'slug' => 'clear-aligners-orthodontics',
                'name_en' => 'Clear Aligners & Orthodontics',
                'name_es' => 'Alineadores Invisibles y Ortodoncia',
                'category_en' => 'Orthodontics',
                'category_es' => 'Ortodoncia',
                'short_description_en' => 'Discreet, removable invisible aligners tailored for teens and adults with digital progression.',
                'short_description_es' => 'Alineadores transparentes y removibles para adolescentes y adultos con simulación digital.',
                'description_en' => 'Straighten your teeth without wires or brackets. Our 3D simulated treatment preview lets you see your final smile before you even start wearing your custom aligners.',
                'description_es' => 'Corrige la posición de tus dientes sin alambres ni brackets. Visualiza la simulación 3D de tu sonrisa final antes de iniciar el tratamiento con férulas transparentes.',
                'price' => 1800.00,
                'duration_minutes' => 45,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 3,
            ],
            [
                'slug' => 'dental-implants',
                'name_en' => 'Guided Dental Implants',
                'name_es' => 'Implantes Dentales Guiados',
                'category_en' => 'Implantology & Surgery',
                'category_es' => 'Implantología y Cirugía',
                'short_description_en' => 'Permanent, natural-looking tooth replacement with computer-guided surgical precision.',
                'short_description_es' => 'Reemplazo dental permanente y natural con cirugía guiada por tomografía computarizada.',
                'description_en' => 'Replace missing teeth with biocompatible titanium or ceramic implants that integrate seamlessly with your jawbone, restoring full chewing strength and aesthetics.',
                'description_es' => 'Recupera piezas dentales ausentes con implantes biocompatibles de titanio o cerámica guiados por ordenador, restableciendo función masticatoria y estética total.',
                'price' => 950.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 4,
            ],
            [
                'slug' => 'porcelain-veneers',
                'name_en' => 'Porcelain Veneers & Smile Design',
                'name_es' => 'Carillas de Porcelana y Diseño de Sonrisa',
                'category_en' => 'Cosmetic Dentistry',
                'category_es' => 'Odontología Estética',
                'short_description_en' => 'Ultra-thin custom porcelain shells that correct shape, color, chips, and gaps flawlessly.',
                'short_description_es' => 'Láminas ultrafinas de porcelana pura que corrigen forma, color y separaciones a la perfección.',
                'description_en' => 'Custom handcrafted porcelain veneers designed according to your facial symmetry. Long-lasting, stain-resistant, and completely natural in reflection.',
                'description_es' => 'Carillas de porcelana diseñadas a medida según la simetría de tu rostro. Altamente resistentes a manchas, duraderas y con brillo 100% natural.',
                'price' => 420.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => false,
                'order' => 5,
            ],
            [
                'slug' => 'emergency-dental-care',
                'name_en' => 'Emergency Dental Relief',
                'name_es' => 'Atención de Urgencia Dental',
                'category_en' => 'Emergency Care',
                'category_es' => 'Urgencias y Dolor',
                'short_description_en' => 'Same-day urgent relief for acute toothache, broken crowns, dental trauma, or infections.',
                'short_description_es' => 'Alivio el mismo día para dolor agudo, fracturas dentales, coronas desprendidas o infecciones.',
                'description_en' => 'Immediate priority care to diagnose and alleviate intense oral pain, treat dental abscesses, and stabilize dental fractures with gentle local anesthesia.',
                'description_es' => 'Atención prioritaria inmediata para diagnosticar y controlar el dolor dental agudo, tratar abscesos y reparar fracturas con anestesia indolora.',
                'price' => 90.00,
                'duration_minutes' => 30,
                'allows_online_booking' => true,
                'is_featured' => false,
                'order' => 6,
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
                'title_en' => 'Chief Implantologist & Oral Surgeon',
                'title_es' => 'Director de Implantología y Cirugía Oral',
                'license_number' => 'DDS-94821',
                'specialty_en' => 'Implantology & Reconstructive Surgery',
                'specialty_es' => 'Implantología y Cirugía Reconstructiva',
                'bio_en' => 'With over 14 years of surgical experience and postgraduate training at NYU, Dr. Vance specializes in computer-guided implant placement and complex bone regeneration with minimal discomfort.',
                'bio_es' => 'Con más de 14 años de experiencia quirúrgica y posgrados en NYU, el Dr. Vance se especializa en colocación de implantes guiados por ordenador y regeneración ósea con mínima molestia.',
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
                'title_en' => 'Lead Orthodontist & Aesthetic Specialist',
                'title_es' => 'Ortodoncista Principal y Especialista en Estética',
                'license_number' => 'DMD-61028',
                'specialty_en' => 'Invisalign & Digital Smile Design',
                'specialty_es' => 'Invisalign y Diseño Digital de Sonrisas',
                'bio_en' => 'Dr. Rostova is a recognized Diamond Apex Invisalign provider. She combines biomechanical precision with artistic facial balance to create healthy, confident smiles.',
                'bio_es' => 'La Dra. Rostova es proveedora certificada Diamond Apex de Invisalign. Combina precisión biomecánica con armonía facial para crear sonrisas sanas y seguras.',
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
                'title_en' => 'Endodontist & Conservative Care',
                'title_es' => 'Endodoncista y Odontología Restauradora',
                'license_number' => 'DDS-73419',
                'specialty_en' => 'Microscopic Root Canal Therapy',
                'specialty_es' => 'Tratamientos de Conducto Microscópicos',
                'bio_en' => 'Dr. Sterling utilizes high-magnification dental microscopes and 3D imaging to save natural teeth with virtually painless root canal treatments.',
                'bio_es' => 'El Dr. Sterling emplea microscopía dental de alta magnificación e imagen 3D para preservar dientes naturales mediante endodoncias prácticamente indoloras.',
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
                'title_en' => 'Pediatric & Preventive Dentist',
                'title_es' => 'Odontopediatra y Prevención Familiar',
                'license_number' => 'DDS-88204',
                'specialty_en' => 'Children & Anxiety-Free Dentistry',
                'specialty_es' => 'Atención Infantil y Odontología sin Ansiedad',
                'bio_en' => 'Warm, patient, and dedicated to transforming dental visits into cheerful experiences for children and fearful adults alike.',
                'bio_es' => 'Cálida, paciente y comprometida en transformar cada visita al dentista en una experiencia agradable y positiva tanto para niños como para adultos ansiosos.',
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
            $createdServices['clear-aligners-orthodontics']->id,
            $createdServices['porcelain-veneers']->id,
            $createdServices['laser-teeth-whitening']->id,
        ]);

        $createdDentists['dr-julian-vance']->services()->sync([
            $createdServices['dental-implants']->id,
            $createdServices['emergency-dental-care']->id,
            $createdServices['comprehensive-exam-cleaning']->id,
        ]);

        $createdDentists['dr-marcus-sterling']->services()->sync([
            $createdServices['emergency-dental-care']->id,
            $createdServices['comprehensive-exam-cleaning']->id,
        ]);

        $createdDentists['dr-sofia-chen']->services()->sync([
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
                'symptoms' => 'Routine checkup and dental cleaning. Slightly sensitive gums.',
                'internal_notes' => 'Patient requested gentle cleaning. First time at the clinic.',
                'preferred_locale' => 'en',
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
                'symptoms' => 'Wedding in 3 weeks, looking for laser whitening package.',
                'internal_notes' => 'Special promotion code APPLIED (Spring Promo).',
                'preferred_locale' => 'en',
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
                'symptoms' => 'Severe pain in lower molar when drinking cold liquids.',
                'internal_notes' => 'Immediate appointment slot reserved.',
                'preferred_locale' => 'en',
                'status' => 'pending',
            ],
        ];

        foreach ($appointmentsData as $item) {
            Appointment::updateOrCreate(['code' => $item['code']], $item);
        }

        // 5. Smile Cases (Before & After)
        $smileCasesData = [
            [
                'title_en' => 'Full Smile Alignment with Clear Aligners',
                'title_es' => 'Alineación Dental Completa con Alineadores',
                'service_id' => $createdServices['clear-aligners-orthodontics']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'before_image' => 'cases/case-1-before.jpg',
                'after_image' => 'cases/case-1-after.jpg',
                'description_en' => 'Severe crowding and deep overbite resolved in 9 months without metal brackets.',
                'description_es' => 'Apiñamiento severo y sobremordida corregidos en 9 meses sin brackets metálicos.',
                'patient_testimonial_en' => '"I never imagined my teeth could look this straight so quickly and comfortably!"',
                'patient_testimonial_es' => '"¡Nunca imaginé que mis dientes pudieran quedar tan rectos de forma tan cómoda y rápida!"',
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'title_en' => 'Porcelain Veneers Aesthetic Transformation',
                'title_es' => 'Transformación Estética con Carillas de Porcelana',
                'service_id' => $createdServices['porcelain-veneers']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'before_image' => 'cases/case-2-before.jpg',
                'after_image' => 'cases/case-2-after.jpg',
                'description_en' => 'Correction of chipped incisors, discoloration, and diastema with 8 custom veneers.',
                'description_es' => 'Corrección de incisivos fracturados, decoloración y diastema con 8 carillas personalizadas.',
                'patient_testimonial_en' => '"The team changed my self-confidence completely. Natural and dazzling result."',
                'patient_testimonial_es' => '"El equipo transformó mi confianza por completo. El resultado es súper natural y brillante."',
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title_en' => 'Single Tooth Titanium Implant Replacement',
                'title_es' => 'Reemplazo Dental con Implante de Titanio',
                'service_id' => $createdServices['dental-implants']->id,
                'dentist_id' => $createdDentists['dr-julian-vance']->id,
                'before_image' => 'cases/case-3-before.jpg',
                'after_image' => 'cases/case-3-after.jpg',
                'description_en' => 'Guided flapless implant surgery restoring full chewing function and pink aesthetics.',
                'description_es' => 'Cirugía guiada sin colgajo restaurando función masticatoria y armonía de la encía.',
                'patient_testimonial_en' => '"The surgery was so quick and completely painless. It feels exactly like my original tooth."',
                'patient_testimonial_es' => '"La cirugía fue rapidísima e indolora. Se siente exactamente como mi diente original."',
                'is_featured' => true,
                'order' => 3,
            ],
        ];

        foreach ($smileCasesData as $item) {
            SmileCase::updateOrCreate(['title_en' => $item['title_en']], $item);
        }

        // 6. Testimonials
        $testimonialsData = [
            [
                'patient_name' => 'Claire Davenport',
                'treatment_en' => 'Clear Aligners & Whitening',
                'treatment_es' => 'Alineadores Invisibles y Blanqueamiento',
                'rating' => 5,
                'comment_en' => 'The most comforting and technologically advanced dental clinic I have visited. Dr. Rostova explained every detail of my digital simulation. The staff is exceptionally warm and welcoming.',
                'comment_es' => 'La clínica más acogedora y avanzada tecnológicamente que he visitado. La Dra. Rostova me explicó cada detalle de la simulación digital. El equipo es súper profesional y cálido.',
                'source' => 'Google Reviews',
                'is_verified' => true,
                'order' => 1,
            ],
            [
                'patient_name' => 'David Morales',
                'treatment_en' => 'Dental Implant & Crown',
                'treatment_es' => 'Implante Dental y Corona',
                'rating' => 5,
                'comment_en' => 'I was terrified of dental surgery, but Dr. Vance and his team made the implant procedure 100% painless. The healing was smooth and fast. Highly recommended!',
                'comment_es' => 'Tenía mucho miedo a la cirugía dental, pero el Dr. Vance y su equipo hicieron que el implante fuera 100% indoloro. La recuperación fue rapidísima. ¡Totalmente recomendados!',
                'source' => 'Verified Patient',
                'is_verified' => true,
                'order' => 2,
            ],
            [
                'patient_name' => 'Emma Watson-Lee',
                'treatment_en' => 'Laser Teeth Whitening',
                'treatment_es' => 'Blanqueamiento Dental Láser',
                'rating' => 5,
                'comment_en' => 'Walked in with stained teeth from years of coffee and walked out one hour later with a bright, radiant smile. Zero sensitivity afterward. Exceptional care!',
                'comment_es' => 'Llegué con manchas de café de años y salí una hora después con una sonrisa radiante. Cero sensibilidad en los días posteriores. ¡Atención impecable!',
                'source' => 'Google Reviews',
                'is_verified' => true,
                'order' => 3,
            ],
            [
                'patient_name' => 'Santiago Ramírez',
                'treatment_en' => 'Limpieza y Revisión General',
                'treatment_es' => 'Limpieza y Revisión General',
                'rating' => 5,
                'comment_en' => 'Super modern facilities and honest pricing. They did not try to upsell procedures I did not need. My teeth feel cleaner than ever.',
                'comment_es' => 'Instalaciones ultra modernas y precios transparentes. No intentan venderte cosas innecesarias. Mis dientes se sienten más limpios que nunca.',
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
                'badge_en' => 'Spring Smile Makeover',
                'badge_es' => 'Especial de Primavera',
                'title_en' => 'Transform Your Smile with Modern, Pain-Free Dentistry',
                'title_es' => 'Transforma tu Sonrisa con Odontología Moderna y sin Dolor',
                'subtitle_en' => 'Complimentary 3D intraoral digital scan and consultation with our certified dental specialists.',
                'subtitle_es' => 'Escaneo intraoral 3D y consulta de valoración sin costo con nuestros especialistas certificados.',
                'button_text_en' => 'Book Your Consultation',
                'button_text_es' => 'Agenda tu Consulta',
                'button_url' => '#booking',
                'background_image' => 'banners/banner-hero-1.jpg',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'badge_en' => 'Invisible Orthodontics',
                'badge_es' => 'Ortodoncia Invisible',
                'title_en' => 'Get the Straight Smile You Deserve Without Metal Brackets',
                'title_es' => 'Consigue la Sonrisa Alineada que Mereces sin Brackets Metálicos',
                'subtitle_en' => 'Customized clear aligners starting from $150/month with zero interest financing plans.',
                'subtitle_es' => 'Alineadores transparentes a medida desde $150/mes con planes de financiamiento sin intereses.',
                'button_text_en' => 'Explore Clear Aligners',
                'button_text_es' => 'Conocer Alineadores',
                'button_url' => '#services',
                'background_image' => 'banners/banner-hero-2.jpg',
                'is_active' => true,
                'order' => 2,
            ],
        ];

        foreach ($bannersData as $item) {
            Banner::updateOrCreate(['title_en' => $item['title_en']], $item);
        }

        // 8. FAQs
        $faqsData = [
            [
                'category_en' => 'Appointments & Booking',
                'category_es' => 'Citas y Reservas',
                'question_en' => 'How can I schedule or reschedule an appointment?',
                'question_es' => '¿Cómo puedo agendar o reagendar una cita?',
                'answer_en' => 'You can schedule directly through our 24/7 online booking form on this website, or message our receptionist via WhatsApp. If you need to reschedule, please notify us at least 24 hours in advance.',
                'answer_es' => 'Puedes agendar directamente a través de nuestro formulario web 24/7 o escribiendo a nuestro WhatsApp. Si necesitas reagendar, avísanos con al menos 24 horas de anticipación.',
                'order' => 1,
            ],
            [
                'category_en' => 'Treatments & Technology',
                'category_es' => 'Tratamientos y Tecnología',
                'question_en' => 'Will dental treatments or cleanings hurt?',
                'question_es' => '¿Dolerán los tratamientos o la limpieza dental?',
                'answer_en' => 'No. We pride ourselves on gentle, pain-free dentistry. We utilize computer-assisted local anesthesia, ultrasonic tools with warm water, and soothing comfort amenities throughout your visit.',
                'answer_es' => 'No. Nos enfocamos en odontología indolora y confortable. Utilizamos anestesia local computarizada, instrumental ultrasónico tibio y técnicas suaves para garantizar cero dolor.',
                'order' => 2,
            ],
            [
                'category_en' => 'Pricing & Insurance',
                'category_es' => 'Precios y Pagos',
                'question_en' => 'Do you accept dental insurance or offer payment plans?',
                'question_es' => '¿Aceptan seguros médicos o planes de pago?',
                'answer_en' => 'Yes! We work with most major PPO dental insurances and offer flexible in-house 0% APR financing plans up to 12 months for treatments like aligners and dental implants.',
                'answer_es' => '¡Sí! Trabajamos con las principales aseguradoras y ofrecemos planes de financiamiento sin intereses de hasta 12 meses para tratamientos como ortodoncia e implantes.',
                'order' => 3,
            ],
            [
                'category_en' => 'First Visit',
                'category_es' => 'Primera Visita',
                'question_en' => 'What should I expect during my first dental consultation?',
                'question_es' => '¿Qué debo esperar durante mi primera consulta dental?',
                'answer_en' => 'Your first appointment lasts approximately 45 minutes and includes high-definition digital x-rays, an intraoral 3D camera scan, periodontal evaluation, and a personalized treatment roadmap.',
                'answer_es' => 'Tu primera cita dura aproximadamente 45 minutos e incluye radiografías digitales HD, escaneo 3D intraoral, revisión periodontal completa y tu plan de tratamiento personalizado.',
                'order' => 4,
            ],
        ];

        foreach ($faqsData as $item) {
            Faq::updateOrCreate(['question_en' => $item['question_en']], $item);
        }

        // 9. Blog Posts
        $postsData = [
            [
                'slug' => 'how-to-care-for-clear-aligners',
                'title_en' => 'Essential Care Tips for Your Clear Aligners: What Dentists Recommend',
                'title_es' => 'Consejos Clave para Cuidar tus Alineadores Invisibles: Lo que Recomiendan los Dentistas',
                'category_en' => 'Orthodontics',
                'category_es' => 'Ortodoncia',
                'excerpt_en' => 'Learn the daily hygiene habits that prevent staining, odors, and keep your invisible aligners crystal clear throughout your smile journey.',
                'excerpt_es' => 'Aprende los hábitos diarios de higiene para evitar manchas, olores y mantener tus férulas transparentes impecables durante tu tratamiento.',
                'content_en' => 'Wearing clear aligners is one of the most aesthetic and comfortable ways to achieve a straight smile. However, proper care is crucial to prevent plaque accumulation and maintain clarity. Always rinse your aligners with lukewarm water after removing them, never use hot water which can warp the medical-grade plastic, and clean them with a soft-bristled brush and mild antibacterial soap daily.',
                'content_es' => 'Usar alineadores transparentes es una de las maneras más estéticas y cómodas de alinear tu dentadura. Sin embargo, su cuidado diario es clave para evitar sarro y olores. Enjuágalos siempre con agua templada al retirarlos, nunca uses agua caliente porque puede deformar el plástico termoconformado, y cepíllalos suavemente con jabón neutro todos los días.',
                'cover_image' => 'blog/aligner-care.jpg',
                'published_at' => Carbon::now()->subDays(5),
                'is_published' => true,
            ],
            [
                'slug' => 'teeth-whitening-facts-vs-myths',
                'title_en' => 'Teeth Whitening: Myths vs Facts You Need to Know Before Treatment',
                'title_es' => 'Blanqueamiento Dental: Mitos y Verdades que Debes Saber Antes de Realizártelo',
                'category_en' => 'Cosmetic Dentistry',
                'category_es' => 'Odontología Estética',
                'excerpt_en' => 'Does whitening damage tooth enamel? How long do laser results last? We debunk common internet trends and explain clinical reality.',
                'excerpt_es' => '¿El blanqueamiento daña el esmalte? ¿Cuánto duran los resultados del láser? Desmentimos mitos populares de internet y explicamos la realidad clínica.',
                'content_en' => 'A frequent misconception is that professional teeth whitening wears away protective tooth enamel. When administered by certified dentists, clinical hydrogen peroxide gels penetrate tooth pores to oxidize deeply seated organic stains without altering structural mineral density. Avoiding DIY trends like charcoal powder or lemon juice is essential to protect your teeth.',
                'content_es' => 'Un mito común es que el blanqueamiento clínico desgasta el esmalte dental. Cuando es supervisado por odontólogos, los geles de peróxido actúan oxidando los pigmentos internos sin alterar la dureza mineral del diente. Es vital evitar remedios caseros como carbón activado o limón, ya que son altamente abrasivos y desgastan el esmalte de forma irreversible.',
                'cover_image' => 'blog/whitening-myths.jpg',
                'published_at' => Carbon::now()->subDays(12),
                'is_published' => true,
            ],
            [
                'slug' => 'why-regular-dental-checkups-matter',
                'title_en' => 'Why Waiting for Tooth Pain is the Costliest Dental Mistake',
                'title_es' => 'Por Qué Esperar a que Duela es el Error Dental más Costoso',
                'category_en' => 'Prevention',
                'category_es' => 'Prevención',
                'excerpt_en' => 'Early dental decay rarely produces pain. Discover how preventive 6-month checkups save you from invasive treatments and high costs.',
                'excerpt_es' => 'La caries en etapas tempranas rara vez produce dolor. Descubre cómo las revisiones preventivas semestrales evitan tratamientos invasivos y altos costos.',
                'content_en' => 'Enamel has no nerve endings, which means cavities can progress silently for months before reaching the dentin or pulp to trigger pain. Regular checkups using digital diagnostics allow dentists to treat micro-cavities non-invasively before they turn into complex root canals or crown restorations.',
                'content_es' => 'El esmalte no tiene terminaciones nerviosas, por lo que una caries puede avanzar silenciosamente durante meses antes de alcanzar la pulpa y provocar dolor. Las revisiones preventivas periódicas permiten detectar y resolver problemas microscópicos de forma rápida, conservadora y a una fracción del costo.',
                'cover_image' => 'blog/dental-checkup.jpg',
                'published_at' => Carbon::now()->subDays(20),
                'is_published' => true,
            ],
        ];

        foreach ($postsData as $item) {
            Post::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
