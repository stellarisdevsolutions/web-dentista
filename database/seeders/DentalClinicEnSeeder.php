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

class DentalClinicEnSeeder extends Seeder
{
    /**
     * Run the database seeds in English.
     */
    public function run(): void
    {
        // 1. Clinic Settings
        ClinicSetting::updateOrCreate(
            ['id' => 1],
            [
                'clinic_name' => 'Aura Dental Studio',
                'tagline' => 'Modern, comprehensive, and pain-free dentistry for radiant smiles.',
                'phone' => '+1 (555) 234-5678',
                'whatsapp' => '+15552345678',
                'email' => 'hello@auradental.com',
                'address' => '742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210',
                'google_maps_url' => 'https://maps.google.com/?q=Beverly+Hills+CA',
                'opening_hours' => "Monday to Friday: 8:00 AM - 7:00 PM\nSaturday: 9:00 AM - 3:00 PM\nSunday: Emergency Care Only",
                'emergency_phone' => '+1 (555) 911-7775',
                'logo_url' => null,
                'show_language_switcher' => true,
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
                'name' => 'General Dental Checkup & Consultation',
                'category' => 'General Dentistry',
                'short_description' => 'Comprehensive oral examination, digital 3D diagnosis, and personalized treatment plan.',
                'description' => 'The general dental checkup is the cornerstone of long-term oral health. Includes a thorough clinical examination of teeth and gums, early cavity screening, occlusion analysis, and an honest personalized roadmap.',
                'image' => 'services/01M5JNAKPJ8DE5A0AB46QYR6BN.webp',
                'price' => 45.00,
                'duration_minutes' => 30,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'slug' => 'comprehensive-exam-cleaning',
                'name' => 'Comprehensive Exam & 3D Prophylaxis',
                'category' => 'General & Prevention',
                'short_description' => '3D diagnostic digital scan, painless ultrasonic cleaning, and oral cancer screening.',
                'description' => 'Our thorough evaluation utilizes high-precision 3D intraoral scanning, ultra-low dose digital X-rays, and warm ultrasonic prophylaxis to remove plaque and tartar with zero discomfort.',
                'image' => 'services/01M5JNSN4XYJFHFTGME0ZXS72F.webp',
                'price' => 85.00,
                'duration_minutes' => 45,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'slug' => 'laser-teeth-whitening',
                'name' => 'Laser Teeth Whitening',
                'category' => 'Cosmetic Dentistry',
                'short_description' => 'Up to 8 shades brighter in a single 60-minute session with gentle cold-light laser.',
                'description' => 'Professional clinical whitening engineered to protect your enamel and minimize post-treatment sensitivity. We utilize desensitizing agents and cold-laser activation for dazzling, luminous results.',
                'image' => 'services/01M5JP8TQJKQ5BDD44M4RB9PCA.webp',
                'price' => 240.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 3,
            ],
            [
                'slug' => 'clear-aligners-orthodontics',
                'name' => 'Clear Aligners & Digital Orthodontics',
                'category' => 'Orthodontics',
                'short_description' => 'Transparent, removable aligners for teens and adults with precision 3D smile simulation.',
                'description' => 'Straighten your teeth seamlessly without metal brackets or wires. Preview your finished smile before beginning treatment using our digital outcome simulator and customized aligners.',
                'image' => 'services/01M5JPK5SSC4KW3RK1RWGRESR1.webp',
                'price' => 1800.00,
                'duration_minutes' => 45,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 4,
            ],
            [
                'slug' => 'dental-implants',
                'name' => 'Guided Dental Implants',
                'category' => 'Implantology & Surgery',
                'short_description' => 'Permanent, natural tooth replacement with computer-guided 3D surgical precision.',
                'description' => 'Restore missing teeth with biocompatible titanium or ceramic implants placed via computer-guided micro-surgery, reinstating full chewing function and facial aesthetic harmony.',
                'image' => 'services/01M5JPR7PTPVP8YWB0RB42S1T5.webp',
                'price' => 950.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => true,
                'order' => 5,
            ],
            [
                'slug' => 'porcelain-veneers',
                'name' => 'Porcelain Veneers & Smile Design',
                'category' => 'Cosmetic Dentistry',
                'short_description' => 'Ultra-thin handcrafted ceramic veneers correcting shape, gaps, and shade effortlessly.',
                'description' => 'Custom-sculpted ceramic veneers designed in harmony with your facial symmetry. Highly stain-resistant, exceptionally durable, and finished with a 100% natural luster.',
                'image' => 'services/01M5JPSPCTZ78D7NTJG6TAT89D.webp',
                'price' => 420.00,
                'duration_minutes' => 60,
                'allows_online_booking' => true,
                'is_featured' => false,
                'order' => 6,
            ],
            [
                'slug' => 'emergency-dental-care',
                'name' => 'Emergency Dental Care',
                'category' => 'Emergency & Pain Relief',
                'short_description' => 'Same-day clinical relief for acute toothache, broken crowns, fractures, or oral infection.',
                'description' => 'Immediate priority triage to diagnose and resolve acute dental pain, manage abscesses, and repair damaged teeth with gentle, pain-free local anesthesia.',
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
                'title' => 'Director of Oral Surgery & Implantology',
                'license_number' => 'DDS-94821',
                'specialty' => 'Implantology & Reconstructive Surgery',
                'bio' => 'With over 14 years of surgical experience and postgraduate training at NYU, Dr. Vance specializes in computer-guided implant placement and bone regeneration with minimal downtime.',
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
                'title' => 'Chief Orthodontist & Smile Aesthetics Specialist',
                'license_number' => 'DMD-61028',
                'specialty' => 'Clear Aligners & Digital Smile Design',
                'bio' => 'Dr. Rostova is an accredited Diamond Apex clear aligner provider. She merges biomechanical precision with facial harmony to create healthy, confident smiles.',
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
                'title' => 'Endodontist & Restorative Specialist',
                'license_number' => 'DDS-73419',
                'specialty' => 'Microscopic Root Canal Therapy',
                'bio' => 'Dr. Sterling utilizes high-magnification surgical dental microscopes and 3D CBCT imaging to preserve natural teeth through virtually painless endodontic therapy.',
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
                'title' => 'Pediatric Dentist & Family Prevention',
                'license_number' => 'DDS-88204',
                'specialty' => 'Pediatric Care & Anxiety-Free Dentistry',
                'bio' => 'Warm, patient, and dedicated to transforming dental visits into fun, positive experiences for both young children and apprehensive adult patients.',
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
                'symptoms' => 'Periodic checkup and deep dental prophylaxis. Slight cold sensitivity.',
                'internal_notes' => 'Patient requested gentle ultrasonic cleaning. First clinic visit.',
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
                'symptoms' => 'Clear aligner assessment and 3D simulation review.',
                'internal_notes' => 'Patient inquiring about 12-month interest-free financing.',
                'preferred_locale' => 'en',
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
                'symptoms' => 'Wedding in 3 weeks, requested laser teeth whitening package.',
                'internal_notes' => 'Applied seasonal promo discount code.',
                'preferred_locale' => 'en',
                'status' => 'confirmed',
            ],
            [
                'code' => 'CIT-2026-D492',
                'service_id' => $createdServices['dental-implants']->id,
                'dentist_id' => $createdDentists['dr-julian-vance']->id,
                'patient_name' => 'Robert Gomez',
                'patient_email' => 'robert.gomez@example.com',
                'patient_phone' => '+15556632198',
                'appointment_date' => Carbon::today()->subDays(2)->format('Y-m-d'),
                'appointment_time' => '09:30',
                'duration_minutes' => 60,
                'is_first_visit' => false,
                'symptoms' => 'Post-op follow-up check for upper right molar implant.',
                'internal_notes' => 'Optimal tissue healing. Periapical control radiograph completed.',
                'preferred_locale' => 'en',
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
                'symptoms' => 'Acute lower molar throbbing when drinking cold fluids.',
                'internal_notes' => 'Emergency same-day priority slot reserved.',
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
                'title' => 'Complete Smile Alignment with Clear Aligners',
                'service_id' => $createdServices['clear-aligners-orthodontics']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'before_image' => 'cases/case-1-before.jpg',
                'after_image' => 'cases/case-1-after.jpg',
                'description' => 'Severe crowding and deep overbite corrected in 9 months without metal brackets.',
                'patient_testimonial' => 'I never imagined my teeth could look this straight, so comfortably and quickly!',
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'title' => 'Cosmetic Makeover with Porcelain Veneers',
                'service_id' => $createdServices['porcelain-veneers']->id,
                'dentist_id' => $createdDentists['dr-elena-rostova']->id,
                'before_image' => 'cases/case-2-before.jpg',
                'after_image' => 'cases/case-2-after.jpg',
                'description' => 'Correction of chipped enamel, dark staining, and diastema using 8 bespoke veneers.',
                'patient_testimonial' => 'The clinical team completely restored my confidence. The result is glowing and natural.',
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'Single Tooth Replacement with Guided Titanium Implant',
                'service_id' => $createdServices['dental-implants']->id,
                'dentist_id' => $createdDentists['dr-julian-vance']->id,
                'before_image' => 'cases/case-3-before.jpg',
                'after_image' => 'cases/case-3-after.jpg',
                'description' => 'Flapless guided implant restoration restoring chewing function and gum harmony.',
                'patient_testimonial' => 'The procedure was swift and completely painless. It feels exactly like my original tooth.',
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
                'treatment' => 'Clear Aligners & Whitening',
                'rating' => 5,
                'comment' => 'The most welcoming and technologically advanced dental studio I have ever visited. Dr. Rostova explained every detail of the digital simulation. Simply top tier.',
                'source' => 'Google Reviews',
                'is_verified' => true,
                'order' => 1,
            ],
            [
                'patient_name' => 'David Morales',
                'treatment' => 'Dental Implant & Crown',
                'rating' => 5,
                'comment' => 'I had serious dental anxiety, but Dr. Vance and his team made the implant placement 100% painless. Recovery was smooth and quick. Highly recommended!',
                'source' => 'Verified Patient',
                'is_verified' => true,
                'order' => 2,
            ],
            [
                'patient_name' => 'Emma Watson-Lee',
                'treatment' => 'Laser Teeth Whitening',
                'rating' => 5,
                'comment' => 'Arrived with years of coffee stains and walked out an hour later with a dazzling smile. Zero sensitivity in the days after. Impeccable care!',
                'source' => 'Google Reviews',
                'is_verified' => true,
                'order' => 3,
            ],
            [
                'patient_name' => 'Santiago Ramirez',
                'treatment' => 'Comprehensive Exam & Cleaning',
                'rating' => 5,
                'comment' => 'Ultra-modern clinical facilities and honest, upfront pricing. They never push unnecessary procedures. My teeth feel cleaner than ever.',
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
                'badge' => 'Spring Clinical Special',
                'title' => 'Transform Your Smile with Modern, Pain-Free Dentistry',
                'subtitle' => 'Complimentary 3D intraoral digital scan and comprehensive aesthetic consult with certified specialists.',
                'button_text' => 'Book Your Consultation',
                'button_url' => '#booking',
                'background_image' => 'media/01M5JGJHXTGN38D2Y1EZHHBZ9W.webp',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'badge' => 'Invisible Orthodontics',
                'title' => 'Get the Straight, Confident Smile You Deserve Without Metal Braces',
                'subtitle' => 'Bespoke clear aligners from $150/month with flexible, interest-free payment arrangements.',
                'button_text' => 'Discover Aligners',
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
                'category' => 'Appointments & Booking',
                'question' => 'How can I schedule or reschedule an appointment?',
                'answer' => 'You can schedule 24/7 directly through our online booking wizard or by contacting our team via WhatsApp. If you need to reschedule, please notify us at least 24 hours in advance.',
                'order' => 1,
            ],
            [
                'category' => 'Treatments & Technology',
                'question' => 'Will my dental treatment or cleaning hurt?',
                'answer' => 'Not at all. We specialize in pain-free, gentle dentistry. We use computer-controlled local anesthesia, warm ultrasonic instruments, and soothing techniques for zero pain.',
                'order' => 2,
            ],
            [
                'category' => 'Pricing & Insurance',
                'question' => 'Do you accept dental insurance or payment plans?',
                'answer' => 'Yes! We collaborate with major insurance providers and offer up to 12 months interest-free financing for comprehensive orthodontic and implant treatments.',
                'order' => 3,
            ],
            [
                'category' => 'First Visit',
                'question' => 'What should I expect during my initial dental consultation?',
                'answer' => 'Your first visit lasts approximately 45 minutes and includes high-definition digital X-rays, 3D intraoral scan, full periodontal checkup, and a personalized treatment roadmap.',
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
                'title' => 'Essential Care Tips for Clear Aligners: Expert Dentist Guidance',
                'category' => 'Orthodontics',
                'excerpt' => 'Learn daily oral hygiene habits to prevent staining, odors, and keep your transparent aligners crystal clear throughout your journey.',
                'content' => 'Wearing clear aligners is one of the most aesthetic and comfortable ways to align your teeth. However, daily maintenance is crucial to avoid bacteria and discoloration. Always rinse trays with lukewarm water when removing them—never hot water, which can distort thermoformed plastic—and brush them gently with clear antibacterial soap every morning and evening.',
                'cover_image' => 'blog/01M5JQC9FNTQ2H2Z4JFXGG8T7P.webp',
                'published_at' => Carbon::now()->subDays(5),
                'is_published' => true,
            ],
            [
                'slug' => 'teeth-whitening-facts-vs-myths',
                'title' => 'Professional Teeth Whitening: Facts vs. Myths You Must Know',
                'category' => 'Cosmetic Dentistry',
                'excerpt' => 'Does whitening damage enamel? How long do cold-laser results last? We debunk viral internet myths and explain the clinical facts.',
                'content' => 'A frequent misconception is that clinical whitening strips or weakens tooth enamel. Under dentist supervision, medical-grade peroxide gels gently oxidize embedded chromogens without altering the tooth mineral hardness. Avoid abrasive DIY remedies like baking soda or lemon juice, which permanently scratch enamel.',
                'cover_image' => 'blog/01M5JQBJJVNSMB7S37EWEY762G.webp',
                'published_at' => Carbon::now()->subDays(12),
                'is_published' => true,
            ],
            [
                'slug' => 'why-regular-dental-checkups-matter',
                'title' => 'Why Waiting for Tooth Pain is the Costliest Dental Mistake',
                'category' => 'Prevention',
                'excerpt' => 'Early decay rarely causes any pain. Discover how preventive six-month checkups safeguard your smile and avoid invasive procedures.',
                'content' => 'Tooth enamel contains zero nerve endings, meaning decay can advance quietly for months before reaching the inner pulp. Regular biannual preventative checkups detect microscopic issues early, resolving them comfortably, conservatively, and at a fraction of the cost.',
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
