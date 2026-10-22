<?php

namespace Database\Seeders;

use App\Models\SiteSection;
use Illuminate\Database\Seeder;

class SiteSectionEnSeeder extends Seeder
{
    /**
     * Run the database seeds in English.
     */
    public function run(): void
    {
        $homeSections = [
            [
                'page' => 'home',
                'section_key' => 'hero',
                'name' => 'Main Hero Section',
                'icon' => 'sparkles',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Your Smile Deserves the Best Clinical Care',
                    'title' => 'Gentle, Pain-Free Dentistry for Your Whole Family.',
                    'subtitle' => 'We combine cutting-edge 3D digital scanning and cold-laser technology with warm, human-centered care to deliver a healthy, radiant smile.',
                    'benefit_1' => 'Board-Certified Specialists & Oral Surgeons',
                    'benefit_2' => '100% Pain-Free Laser Treatments & Conscious Sedation',
                    'button_primary_text' => 'Book Online Consultation »',
                    'button_primary_url' => '#appointment-section',
                    'badge_title' => '24 Hours',
                    'badge_subtitle' => 'Emergency Care',
                    'image_url' => '/storage/media/01M5JGJHXTGN38D2Y1EZHHBZ9W.webp',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'quick_cards',
                'name' => 'Quick Contact Action Cards',
                'icon' => 'phone',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'emergency_title' => 'Emergency Dental Care',
                    'emergency_subtitle' => 'Same-day relief for acute toothache, fractures, or urgent infection.',
                    'schedule_title' => 'Book Your Appointment Online',
                    'schedule_subtitle' => 'Select your specialist and available schedule in seconds.',
                    'schedule_button_text' => 'Book Visit Now',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'about_preview',
                'name' => 'About Clinic (Homepage Preview)',
                'icon' => 'information-circle',
                'order' => 3,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Welcome to Our Studio',
                    'title' => 'Over 15 years transforming smiles with warmth, science, and clinical precision.',
                    'description' => 'We believe a trip to the dentist should be a relaxing, anxiety-free experience. We feature state-of-the-art 3D intraoral digital imaging and surgical suites sterilized under strict hospital-grade standards.',
                    'check_text' => 'Flexible schedules organized into Morning and Afternoon shifts for your convenience.',
                    'button_text' => 'Meet Our Specialists »',
                    'button_url' => '#dentists',
                    'image_1_url' => '/storage/media/01M5JGJ440P8R1Z0QC5Z3Y02WD.webp',
                    'image_2_url' => '/storage/media/01M5JGJ4A1DPQTAC9VR2S1K15N.webp',
                    'badge_years' => '20+',
                    'badge_text' => 'Years Exp.',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'trust_bar',
                'name' => 'Trust Bar & Key Statistics',
                'icon' => 'chart-bar',
                'order' => 4,
                'is_active' => true,
                'content' => [
                    'stat_1_number' => '25,000+',
                    'stat_1_label' => 'Happy Patients',
                    'stat_2_number' => '24/7',
                    'stat_2_label' => 'Emergency Support',
                    'stat_3_number' => '18,500+',
                    'stat_3_label' => 'Restored Smiles',
                    'stat_4_number' => '99.4%',
                    'stat_4_label' => 'Patient Satisfaction',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'how_it_works',
                'name' => 'Clinical Excellence Showcase',
                'icon' => 'numbered-list',
                'order' => 5,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Comprehensive Dental Solutions',
                    'title' => 'Advanced Dental Care Delivered with Gentle Comfort.',
                    'subtitle' => 'From deep preventive prophylaxis to handcrafted porcelain veneers and computer-guided 3D implants, we protect your oral health with surgical precision.',
                    'step_1_title' => 'Warm Ultrasonic Hygiene and Comprehensive Preventive Screening',
                    'step_2_title' => 'Certified Specialists in Aesthetic & Reconstructive Surgery',
                    'step_3_title' => 'Hospital-Grade Autoclave & Surgical Sterilization Protocols',
                    'step_4_title' => '100% Pain-Free Laser Dentistry & Conscious Sedation Options',
                    'image_1_url' => '/storage/media/01M5JGJ4J79Z8WR476FB9JAPK4.webp',
                    'image_2_url' => '/storage/media/01M5JGJ4QXMG3KX2V14DRRQ5MJ.webp',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'testimonials_cases',
                'name' => 'Patient Testimonials & Success Stories',
                'icon' => 'chat-bubble-left-right',
                'order' => 6,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'PATIENT TESTIMONIALS',
                    'title' => 'What Our Patients Say About Their Dental Journey.',
                    'subtitle' => 'Verified Google & WhatsApp Patient Reviews',
                    'image_url' => '/storage/media/01M5JGJ4XT6MQCK33A7DFCE3A1.webp',
                    'items' => [
                        [
                            'patient_name' => 'Claire Davenport',
                            'treatment' => 'Clear Aligners & Teeth Whitening',
                            'rating' => 5,
                            'comment' => 'The most welcoming and technologically advanced dental clinic I have visited. Dr. Rostova explained every detail of the digital simulation. The team is warm and exceptionally professional.',
                            'avatar_url' => '',
                        ],
                        [
                            'patient_name' => 'David Morales',
                            'treatment' => 'Guided Dental Implant & Crown',
                            'rating' => 4.5,
                            'comment' => 'I used to dread dental surgery, but Dr. Vance made the procedure 100% painless. Recovery was smooth and speedy. Would recommend to anyone!',
                            'avatar_url' => '',
                        ],
                        [
                            'patient_name' => 'Emma Watson-Lee',
                            'treatment' => 'Laser Teeth Whitening',
                            'rating' => 5,
                            'comment' => 'I came in with years of coffee stains and walked out an hour later with a dazzling smile. Zero sensitivity in the days that followed. Truly fantastic service!',
                            'avatar_url' => '',
                        ],
                        [
                            'patient_name' => 'Santiago Ramirez',
                            'treatment' => 'Deep Prophylaxis & Checkup',
                            'rating' => 4.5,
                            'comment' => 'Super modern facilities and clear, transparent pricing. They never push unneeded treatments. My teeth feel cleaner and healthier than ever.',
                            'avatar_url' => '',
                        ],
                    ],
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'services_catalog',
                'name' => 'Treatments Catalog Overview',
                'icon' => 'squares-2x2',
                'order' => 7,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Clinical Specialties',
                    'title' => 'Comprehensive treatments for every dental need',
                    'subtitle' => 'From preventive family dentistry to complex implantology and aesthetic smile makeovers with state-of-the-art technology.',
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'faqs',
                'name' => 'Frequently Asked Questions',
                'icon' => 'question-mark-circle',
                'order' => 8,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Common Inquiries',
                    'title' => 'Frequently Asked Questions',
                    'subtitle' => 'Everything you need to know prior to your initial clinical visit.',
                    'items' => [
                        [
                            'question' => 'How can I schedule or reschedule an appointment?',
                            'answer' => 'You can book 24/7 directly using our online booking wizard or via WhatsApp. If you need to reschedule, kindly notify us at least 24 hours in advance.',
                        ],
                        [
                            'question' => 'Will my cleaning or treatment cause discomfort?',
                            'answer' => 'No. We specialize in pain-free, gentle dentistry using computerized local anesthesia, warm ultrasonic instruments, and soothing clinical techniques.',
                        ],
                        [
                            'question' => 'Do you accept dental insurance or installment plans?',
                            'answer' => 'Yes! We collaborate with top insurance providers and provide interest-free monthly installment plans for orthodontic and implant procedures.',
                        ],
                        [
                            'question' => 'What happens during my first dental consultation?',
                            'answer' => 'Your first appointment takes approximately 45 minutes and includes digital HD radiographs, a 3D intraoral scan, periodontal screening, and a tailored treatment roadmap.',
                        ],
                    ],
                ],
            ],
            [
                'page' => 'home',
                'section_key' => 'cta_booking',
                'name' => 'Online Booking CTA Banner',
                'icon' => 'calendar',
                'order' => 9,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'Online Booking 24/7',
                    'title' => 'Book Your Dental Consultation Online',
                    'subtitle' => 'Select the care you require and choose your slot on the date and time that fits your everyday routine.',
                    'check_1' => 'Instant booking confirmation and automated WhatsApp reminders.',
                    'check_2' => 'Board-certified specialists in aesthetic and reconstructive surgery.',
                    'check_3' => 'Convenient schedule slots grouped into Morning and Afternoon shifts.',
                    'button_text' => 'Book Appointment Online »',
                    'button_url' => '/schedule',
                    'image_url' => '/storage/media/01M5JGJHPPB55Y34ZH5Q2Y8DFJ.webp',
                    'emergency_title' => '24/7 Emergency Priority Line',
                    'emergency_subtitle' => 'Same-day priority care for acute dental pain',
                ],
            ],
        ];

        $aboutSections = [
            [
                'page' => 'about',
                'section_key' => 'about_hero',
                'name' => 'About Us Hero Banner',
                'icon' => 'sparkles',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'WELCOME TO AURA DENTAL STUDIO',
                    'title' => 'Over 20+ Years Delivering Clinical Excellence',
                    'subtitle' => 'A multidisciplinary team of dental specialists committed to your wellness, minimally invasive technology, and a soothing, pain-free experience.',
                    'button_primary_text' => 'Meet Our Specialists ↓',
                    'button_primary_url' => '#dentists',
                    'button_secondary_text' => 'Book Consultation Online »',
                    'button_secondary_url' => '/schedule',
                ],
            ],
            [
                'page' => 'about',
                'section_key' => 'about_story',
                'name' => 'Philosophy & Heritage',
                'icon' => 'heart',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'OUR MEDICAL PHILOSOPHY',
                    'title' => 'Comprehensive, Compassionate, and Pain-Free Dentistry.',
                    'description' => 'At Aura Dental Studio, we fuse cutting-edge clinical science with genuine human empathy. Our mission is to make every visit tranquil and rewarding, safeguarding both the functional longevity of your teeth and the organic beauty of your smile.',
                    'feature_1_title' => 'Personalized Guidance',
                    'feature_1_desc' => 'Transparent communication and support before, during, and after every treatment.',
                    'feature_2_title' => 'Certified Specialists',
                    'feature_2_desc' => 'University postgraduates with ongoing training in every dental discipline.',
                    'check_text' => 'Featuring digital surgical suites, 3D intraoral scanners, and conscious sedation.',
                    'button_text' => 'Book with a Specialist »',
                    'button_url' => '/schedule',
                    'image_1_url' => '/storage/media/01M5JGJ4QXMG3KX2V14DRRQ5MJ.webp',
                    'image_2_url' => '/storage/media/01M5JGJ4A1DPQTAC9VR2S1K15N.webp',
                    'badge_years' => '20+',
                    'badge_text' => 'Years of Excellence',
                ],
            ],
            [
                'page' => 'about',
                'section_key' => 'about_trustbar',
                'name' => 'Key Milestones & Trust Metrics',
                'icon' => 'chart-bar',
                'order' => 3,
                'is_active' => true,
                'content' => [
                    'stat_1_number' => '567+',
                    'stat_1_label' => 'Happy Patients',
                    'stat_2_number' => '23K+',
                    'stat_2_label' => 'Procedures Performed',
                    'stat_3_number' => '241+',
                    'stat_3_label' => 'Smile Makeovers',
                    'stat_4_number' => '16+',
                    'stat_4_label' => 'Quality Certifications',
                ],
            ],
            [
                'page' => 'about',
                'section_key' => 'about_specialists',
                'name' => 'Clinical Specialists Team Header',
                'icon' => 'user-group',
                'order' => 4,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'ELITE DENTAL TEAM',
                    'title' => 'Our Dental Specialists',
                    'subtitle' => 'Licensed clinicians with postgraduate certifications and extensive experience in surgery, implantology, orthodontics, and smile design.',
                ],
            ],
        ];

        $servicesSections = [
            [
                'page' => 'services',
                'section_key' => 'services_hero',
                'name' => 'Services Catalog Hero Banner',
                'icon' => 'sparkles',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'CLINICAL EXCELLENCE',
                    'title' => 'Our Treatments & Procedures',
                    'subtitle' => 'Comprehensive, modern, and pain-free dental care backed by 3D intraoral precision and Swiss-standard biocompatible materials.',
                ],
            ],
        ];

        $blogSections = [
            [
                'page' => 'blog',
                'section_key' => 'blog_hero',
                'name' => 'Blog & Clinical Insights Hero',
                'icon' => 'sparkles',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'SCIENTIFIC INSIGHTS & ORAL HEALTH',
                    'title' => 'Clinical Blog & Advice',
                    'subtitle' => 'Evidence-based articles and actionable wellness tips from our dentists to protect your teeth and gums every single day.',
                ],
            ],
            [
                'page' => 'blog',
                'section_key' => 'blog_cta',
                'name' => 'Personalized Consultation Banner',
                'icon' => 'chat-bubble-bottom-center-text',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'PERSONALIZED INQUIRY',
                    'title' => 'Have questions about a dental symptom or procedure?',
                    'subtitle' => 'Our certified clinicians are ready to evaluate your smile in person or answer your inquiries right away.',
                    'button_primary_text' => 'Book Appointment Online »',
                    'button_primary_url' => '/schedule',
                    'button_secondary_text' => 'Contact Team »',
                    'button_secondary_url' => '/contact',
                ],
            ],
        ];

        $contactSections = [
            [
                'page' => 'contact',
                'section_key' => 'contact_hero',
                'name' => 'Contact Us Hero Banner',
                'icon' => 'sparkles',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'WE ARE HERE TO HELP',
                    'title' => 'Contact Aura Dental Studio',
                    'subtitle' => 'Have a question about a treatment or need immediate urgent care? Call us, drop a message, or visit our clinical facilities.',
                ],
            ],
            [
                'page' => 'contact',
                'section_key' => 'contact_cards',
                'name' => 'Contact Cards & Location Info',
                'icon' => 'phone',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'card_1_title' => 'Direct Phone & Emergency Hotline',
                    'card_1_desc' => 'Direct clinical line for appointments and 24/7 dental emergency triage.',
                    'card_2_title' => 'Official Email Box',
                    'card_2_desc' => 'Send us your questions, clinical records, or treatment estimate requests.',
                    'card_3_title' => 'Clinic Location & Suites',
                    'card_3_desc' => 'Prime central location with dedicated complimentary patient parking.',
                ],
            ],
            [
                'page' => 'contact',
                'section_key' => 'contact_faqs',
                'name' => 'Patient Guidance FAQ Accordion',
                'icon' => 'question-mark-circle',
                'order' => 3,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'PATIENT GUIDANCE',
                    'title' => 'Helpful Information',
                    'subtitle' => 'Answers to frequent questions prior to your clinical visit.',
                    'items' => [
                        [
                            'question' => 'Should I reserve a slot beforehand for routine visits or emergencies?',
                            'answer' => 'For scheduled visits, we advise booking online ahead of time. For acute dental emergencies, we prioritize walk-ins with a quick prior phone call.',
                        ],
                        [
                            'question' => 'Do you offer dental financing and insurance plans?',
                            'answer' => 'Yes, we work with leading dental insurance carriers and provide fixed-rate installment plans for orthodontic and implant procedures.',
                        ],
                        [
                            'question' => 'Where can I park when arriving at the clinic?',
                            'answer' => 'We offer exclusive, free designated patient parking stalls right at our main entrance.',
                        ],
                    ],
                ],
            ],
        ];

        $scheduleSections = [
            [
                'page' => 'schedule',
                'section_key' => 'schedule_hero',
                'name' => 'Online Booking Hero Banner',
                'icon' => 'calendar',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'eyebrow' => 'ONLINE SCHEDULING 24/7',
                    'title' => 'Book Your Dental Visit in 3 Easy Steps',
                    'subtitle' => 'Choose your treatment, select your preferred shift (Morning or Afternoon), and get instant WhatsApp confirmation with our reception team.',
                ],
            ],
        ];

        foreach (array_merge($homeSections, $aboutSections, $servicesSections, $blogSections, $contactSections, $scheduleSections) as $section) {
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
