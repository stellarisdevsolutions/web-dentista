<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => $clinic->clinic_name . ' | ' . __('Odontología Avanzada y Cuidado Dental'),
        'description' => $clinic->tagline ?? __('Cuidado dental suave, integral e indoloro para toda la familia.'),
        'ogType' => 'website',
    ])

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f0f7ff] text-slate-900 selection:bg-sky-500 selection:text-white" x-data="{ mobileMenuOpen: false, searchOpen: false, searchQuery: '' }">

    <!-- ========================================== -->
    <!-- GLOBAL NAVBAR                              -->
    <!-- ========================================== -->
    @include('partials.navbar')

    @php
        $sectionsMap = isset($sections) ? $sections : \App\Models\SiteSection::forPage('home')->get()->keyBy('section_key');
        $hero = $sectionsMap->get('hero')?->content ?? [];
        $heroActive = $sectionsMap->get('hero')?->is_active ?? true;
        $quickCards = $sectionsMap->get('quick_cards')?->content ?? [];
        $quickCardsActive = $sectionsMap->get('quick_cards')?->is_active ?? true;
        $aboutSec = $sectionsMap->get('about_preview')?->content ?? [];
        $aboutActive = $sectionsMap->get('about_preview')?->is_active ?? true;
        $trustbarSec = $sectionsMap->get('trust_bar')?->content ?? [];
        $trustbarActive = $sectionsMap->get('trust_bar')?->is_active ?? true;
        $howItWorksSec = $sectionsMap->get('how_it_works')?->content ?? [];
        $howItWorksActive = $sectionsMap->get('how_it_works')?->is_active ?? true;
        $testimonialsSec = $sectionsMap->get('testimonials_cases')?->content ?? [];
        $testimonialsActive = $sectionsMap->get('testimonials_cases')?->is_active ?? true;
        $servicesSec = $sectionsMap->get('services_catalog')?->content ?? [];
        $servicesActive = $sectionsMap->get('services_catalog')?->is_active ?? true;
        $faqsSec = $sectionsMap->get('faqs')?->content ?? [];
        $faqsActive = $sectionsMap->get('faqs')?->is_active ?? true;
        $ctaBookingSec = $sectionsMap->get('cta_booking')?->content ?? [];
        $ctaBookingActive = $sectionsMap->get('cta_booking')?->is_active ?? true;
        $isEditor = request()->has('editor_preview');
    @endphp

    <main class="min-h-screen bg-white text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- 3. HERO SECTION ASYMMETRIC (Vercel Style)   -->
        <!-- ========================================== -->
        <section id="inicio" data-cms-section="hero" data-cms-name="Portada Principal" class="relative bg-white pt-2 pb-10 overflow-hidden" @if(!$heroActive && !$isEditor) style="display: none;" @endif>
            <div class="w-full relative flex flex-col lg:flex-row items-stretch min-h-[550px]">
                
                <!-- Left Curved Dark Navy Column (rounded-r-[220px]) -->
                <div class="lg:w-7/12 bg-[#13223f] text-white p-8 sm:p-12 lg:p-16 rounded-b-[50px] lg:rounded-b-none lg:rounded-r-[220px] relative z-10 flex flex-col justify-center shadow-2xl transition-all duration-500">
                    
                    <!-- Decorative Background Watermark -->
                    <div class="absolute left-4 bottom-8 opacity-10 pointer-events-none hidden sm:block animate-float">
                        <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-32 h-32 text-sky-400">
                            <path d="M11 2v2"></path>
                            <path d="M5 2v2"></path>
                            <path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"></path>
                            <path d="M8 15a6 6 0 0 0 12 0v-3"></path>
                            <circle cx="20" cy="10" r="2"></circle>
                        </svg>
                    </div>

                    <div class="w-full max-w-xl mx-auto lg:ml-auto lg:mr-12 relative z-10 animate-fade-in-up">
                        <!-- Eyebrow Pill -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/20 text-sky-300 font-bold text-xs tracking-wider uppercase mb-5 border border-sky-500/30 animate-pulse-glow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-sky-400">
                                <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path>
                            </svg>
                            <span data-cms-field="eyebrow">{{ $hero['eyebrow'] ?? __('Tu Sonrisa Merece la Mejor Atención') }}</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 data-cms-field="title" class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[1.1] mb-5 tracking-tight">
                            {{ $hero['title'] ?? __('Odontología Suave, Sin Dolor para Toda tu Familia.') }}
                        </h1>

                        <!-- Description -->
                        <p data-cms-field="subtitle" class="text-slate-300 text-sm leading-relaxed mb-8 font-light max-w-lg">
                            {{ $hero['subtitle'] ?? ($clinic->tagline ?? __('Combinamos escáner digital 3D y tecnología láser avanzada con un trato cálido y humano para devolverte una sonrisa radiante y saludable.')) }}
                        </p>

                        <!-- Key Benefits / Bullet Checkmarks -->
                        @php
                            $benefit1 = $hero['benefit_1'] ?? null;
                            $benefit2 = $hero['benefit_2'] ?? null;
                            if (!array_key_exists('benefit_1', $hero)) {
                                $benefit1 = __('Odontólogos y Cirujanos Especialistas Certificados');
                            }
                            if (!array_key_exists('benefit_2', $hero)) {
                                $benefit2 = __('Tratamientos Láser 100% Indoloros y Sedación Consciente');
                            }
                            $hasBenefit1 = filled(trim($benefit1 ?? ''));
                            $hasBenefit2 = filled(trim($benefit2 ?? ''));
                        @endphp
                        <div data-cms-group="hero_benefits" class="space-y-3 mb-8" @if(!$hasBenefit1 && !$hasBenefit2) style="display: none;" @endif>
                            <div data-cms-row="benefit_1" class="flex items-center gap-3 text-xs sm:text-sm font-bold text-slate-200 hover:translate-x-1 transition-transform" @if(!$hasBenefit1) style="display: none;" @endif>
                                <div class="w-5 h-5 rounded-full bg-sky-500 text-white flex items-center justify-center text-xs flex-shrink-0">✓</div>
                                <span data-cms-field="benefit_1">{{ $benefit1 }}</span>
                            </div>
                            <div data-cms-row="benefit_2" class="flex items-center gap-3 text-xs sm:text-sm font-bold text-slate-200 hover:translate-x-1 transition-transform" @if(!$hasBenefit2) style="display: none;" @endif>
                                <div class="w-5 h-5 rounded-full bg-sky-500 text-white flex items-center justify-center text-xs flex-shrink-0">✓</div>
                                <span data-cms-field="benefit_2">{{ $benefit2 }}</span>
                            </div>
                        </div>

                        <!-- CTA Actions -->
                        <div class="flex items-center">
                            <a href="{{ $hero['button_primary_url'] ?? '#appointment-section' }}" class="bg-sky-500 hover:bg-sky-600 text-white px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-lg shadow-sky-500/30 flex items-center gap-1.5 hover:scale-105 active:scale-95 hover:shadow-sky-500/50">
                                <span data-cms-field="button_primary_text">{{ $hero['button_primary_text'] ?? __('Agendar Consulta Online »') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Floating Circular Badge on Seam: 24 Horas Soporte -->
                    <div class="absolute right-0 lg:-right-12 top-1/2 -translate-y-1/2 z-30 hidden sm:flex animate-float">
                        <div class="w-24 h-24 rounded-full bg-sky-500 text-white border-4 border-white shadow-xl flex flex-col items-center justify-center text-center p-2 hover:scale-110 transition-transform cursor-default">
                            <span data-cms-field="badge_title" class="text-sm font-black leading-none">{{ $hero['badge_title'] ?? __('24 Horas') }}</span>
                            <span data-cms-field="badge_subtitle" class="text-[10px] font-bold uppercase tracking-wider mt-1">{{ $hero['badge_subtitle'] ?? __('Urgencias') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Side High-Res Clinical Photography (LCP Optimizado con Fallback Seguro) -->
                <div class="lg:w-5/12 relative min-h-[380px] lg:min-h-full bg-slate-900 overflow-hidden">
                    <img 
                        data-cms-field="image_url" 
                        src="{{ !empty($hero['image_url']) ? $hero['image_url'] : 'https://masalaserclinic.com/wp-content/uploads/elementor/thumbs/dentist-making-professional-teeth-cleaning-withb-cotton-female-young-patient-dental-office-scaled-rmzddojmvr5b2ndthwo8x3mhck1ct16aflega90jqw.jpg' }}" 
                        alt="{{ __('images.home_hero', ['clinic' => $clinic->clinic_name ?? 'Aura Dental Studio']) }}" 
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" 
                        loading="eager" 
                        fetchpriority="high"
                        decoding="async"
                    />
                </div>
            </div>

            <!-- Overlapping Quick Contact Cards (-mt-10) -->
            <div data-cms-section="quick_cards" data-cms-name="Tarjetas de Contacto" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-30 animate-scale-in" @if(!$quickCardsActive && !$isEditor) style="display: none;" @endif>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Emergency Call Card (Número conectado a configuración clínica permanente) -->
                    <div class="bg-white p-7 rounded-3xl shadow-xl border border-slate-100 flex items-center gap-6 hover:-translate-y-1.5 transition-all duration-300 hover:shadow-2xl">
                        <div class="w-16 h-16 rounded-2xl bg-[#13223f] text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-sky-400">
                                <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 data-cms-field="emergency_title" class="text-xl font-black text-slate-900 mb-1">{{ $quickCards['emergency_title'] ?? __('Llamada de Urgencia') }}</h3>
                            <p data-cms-field="emergency_subtitle" class="text-slate-500 text-xs mb-2">{{ $quickCards['emergency_subtitle'] ?? __('Atención dental prioritaria el mismo día') }}</p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic->emergency_phone ?? $clinic->phone) }}" class="text-sky-500 font-extrabold text-sm hover:underline">
                                {{ $clinic->emergency_phone ?? $clinic->phone }}
                            </a>
                        </div>
                    </div>

                    <!-- Instant Message / Support Card -->
                    <div class="bg-white p-7 rounded-3xl shadow-xl border border-slate-100 flex items-center gap-6 hover:-translate-y-1.5 transition-all duration-300 hover:shadow-2xl">
                        <div class="w-16 h-16 rounded-2xl bg-[#13223f] text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-sky-400">
                                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            </svg>
                        </div>
                        <div>
                            <h3 data-cms-field="schedule_title" class="text-xl font-black text-slate-900 mb-1">{{ $quickCards['schedule_title'] ?? __('Agenda tu Cita en Línea') }}</h3>
                            <p data-cms-field="schedule_subtitle" class="text-slate-500 text-xs mb-2">{{ $quickCards['schedule_subtitle'] ?? __('Elige a tu especialista y horario disponible en segundos.') }}</p>
                            <a href="#appointment-section" class="text-sky-500 font-extrabold text-sm hover:underline">
                                <span data-cms-field="schedule_button_text">{{ $quickCards['schedule_button_text'] ?? __('Reservar Cita Ahora') }}</span> →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 4. ABOUT OUR CLINIC SECTION                 -->
        <!-- ========================================== -->
        <section id="about" data-cms-section="about_preview" data-cms-name="Sobre la Clínica" class="pt-8 pb-10 bg-white" @if(!$aboutActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-14">
                    
                    <!-- Left Photo Composition with Circular Badge & 2 Overlapping Images -->
                    <div class="lg:w-1/2 relative w-full flex justify-center">
                        <div class="relative w-full max-w-[460px]">
                            <!-- Animated Ring -->
                            <div class="absolute -top-6 -left-6 w-24 h-24 border-8 border-sky-400 rounded-full border-r-transparent border-b-transparent -z-10 animate-float"></div>
                            
                            <!-- Primary Clinical Photo (Grande Superior) -->
                            <div class="rounded-[36px] overflow-hidden shadow-2xl border-4 border-white bg-slate-100 transition-all duration-500 aspect-[4/3]">
                                <img 
                                    data-cms-field="image_1_url" 
                                    src="{{ !empty($aboutSec['image_1_url']) ? $aboutSec['image_1_url'] : ($aboutSec['image_url'] ?? 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80') }}" 
                                    alt="{{ __('images.home_about_1', ['clinic' => $clinic->clinic_name ?? 'Aura Dental Studio']) }}" 
                                    class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" 
                                    loading="lazy" 
                                />
                            </div>

                            <!-- Floating Circular Badge (ej. 20+ AÑOS EXP.) -->
                            <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-28 h-28 rounded-full bg-[#13223f] text-white border-4 border-white shadow-2xl flex flex-col items-center justify-center text-center p-2 z-20 hover:scale-110 transition-transform duration-300">
                                <span data-cms-field="badge_years" class="text-xl font-black leading-none">{{ $aboutSec['badge_years'] ?? '20+' }}</span>
                                <span data-cms-field="badge_text" class="text-[10px] font-bold uppercase tracking-widest mt-1">{{ $aboutSec['badge_text'] ?? __('Años Exp.') }}</span>
                            </div>

                            <!-- Secondary Overlapping Clinical Photo (Inferior Derecha) -->
                            <div class="w-64 sm:w-72 h-44 sm:h-48 rounded-3xl overflow-hidden shadow-2xl border-4 border-white -mt-20 ml-auto relative z-10 bg-slate-100 hover:scale-105 transition-transform duration-300">
                                <img 
                                    data-cms-field="image_2_url" 
                                    src="{{ !empty($aboutSec['image_2_url']) ? $aboutSec['image_2_url'] : 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=600&q=80' }}" 
                                    alt="{{ __('images.home_about_2') }}" 
                                    class="w-full h-full object-cover" 
                                    loading="lazy" 
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Right Narrative & Benefit Cards -->
                    <div class="lg:w-1/2 flex flex-col items-start">
                        <div class="flex items-center gap-2 text-sky-500 font-bold text-xs uppercase tracking-widest mb-3">
                            <span class="w-5 h-1 bg-sky-500 rounded-full"></span>
                            <span data-cms-field="eyebrow">{{ $aboutSec['eyebrow'] ?? __('CONOCE NUESTRA CLÍNICA') }}</span>
                        </div>

                        <h2 data-cms-field="title" class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-tight mb-5">
                            {{ $aboutSec['title'] ?? __('Más de 15 años transformando sonrisas con calidez, ciencia y precisión.') }}
                        </h2>

                        <p data-cms-field="description" class="text-slate-500 text-sm leading-relaxed mb-6">
                            {{ $aboutSec['description'] ?? __('En nuestra clínica creemos que una visita al dentista debe ser una experiencia reconfortante y libre de ansiedad. Contamos con tecnología de diagnóstico digital 3D y quirófanos esterilizados bajo estándares hospitalarios internacionales.') }}
                        </p>

                        <!-- Cards con soporte bilingüe EN / ES -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full mb-6">
                            <!-- Card 1: Atención Sin Temor -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-start gap-3.5 hover:border-sky-300 transition-colors">
                                <div class="p-2.5 rounded-xl bg-white text-sky-500 shadow-xs border border-slate-100 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm mb-0.5">
                                        {{ __('Atención Sin Temor') }}
                                    </h4>
                                    <p class="text-xs text-slate-400">
                                        {{ __('Técnicas suaves para pacientes con fobia y niños.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Card 2: Especialistas Top -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-start gap-3.5 hover:border-sky-300 transition-colors">
                                <div class="p-2.5 rounded-xl bg-white text-sky-500 shadow-xs border border-slate-100 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm mb-0.5">
                                        {{ __('Especialistas Top') }}
                                    </h4>
                                    <p class="text-xs text-slate-400">
                                        {{ __('Doctores acreditados en estética, implantes y ortodoncia.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @php
                            $checkText = $aboutSec['check_text'] ?? null;
                            if (!array_key_exists('check_text', $aboutSec)) {
                                $checkText = __('Horarios organizados por Turno Mañana y Turno Tarde para tu comodidad.');
                            }
                            $hasCheckText = filled(trim($checkText ?? ''));
                        @endphp
                        <!-- Checkmark de horarios -->
                        <div data-cms-row="check_text" class="flex items-center gap-3 text-xs text-slate-600 mb-6" @if(!$hasCheckText) style="display: none;" @endif>
                            <div class="w-5 h-5 rounded-full bg-[#13223f] text-white flex items-center justify-center text-[10px] font-bold shrink-0">✓</div>
                            <span data-cms-field="check_text">{{ $checkText }}</span>
                        </div>

                        <!-- Botón: Conocer a los Especialistas (Fin de la sección Sobre la Clínica) -->
                        <a href="{{ $aboutSec['button_url'] ?? '#dentists' }}" class="bg-sky-500 hover:bg-sky-600 text-white px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all flex items-center gap-1 hover:scale-105 hover:shadow-sky-500/40">
                            <span data-cms-field="button_text">{{ $aboutSec['button_text'] ?? __('CONOCER A LOS ESPECIALISTAS »') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 4.1 BARRA DE CONFIANZA Y ESTADÍSTICAS       -->
        <!-- ========================================== -->
        <section id="trustbar" data-cms-section="trust_bar" data-cms-name="Barra de Estadísticas" class="py-6 bg-white" @if(!$trustbarActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-[#0c1830] text-white rounded-3xl p-8 sm:p-10 shadow-[0_20px_50px_rgba(14,165,233,0.12)] border border-slate-800 relative overflow-hidden">
                    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-slate-800/80 relative z-10">
                        <div class="flex flex-col items-center p-3 group">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 shadow-lg shadow-sky-500/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_1_number" class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">{{ $trustbarSec['stat_1_number'] ?? '25,000+' }}</h3>
                            <p data-cms-field="stat_1_label" class="text-xs text-sky-400 font-bold uppercase tracking-wider">{{ $trustbarSec['stat_1_label'] ?? __('Pacientes Felices') }}</p>
                        </div>

                        <div class="flex flex-col items-center p-3 pt-6 md:pt-3 group">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 shadow-lg shadow-sky-500/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                                    <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_2_number" class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">{{ $trustbarSec['stat_2_number'] ?? '24/7' }}</h3>
                            <p data-cms-field="stat_2_label" class="text-xs text-sky-400 font-bold uppercase tracking-wider">{{ $trustbarSec['stat_2_label'] ?? __('Soporte Urgencias') }}</p>
                        </div>

                        <div class="flex flex-col items-center p-3 pt-6 md:pt-3 group">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 shadow-lg shadow-sky-500/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                                    <path d="M5 21v-6"></path>
                                    <path d="M12 21V3"></path>
                                    <path d="M19 21V9"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_3_number" class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">{{ $trustbarSec['stat_3_number'] ?? '18,500+' }}</h3>
                            <p data-cms-field="stat_3_label" class="text-xs text-sky-400 font-bold uppercase tracking-wider">{{ $trustbarSec['stat_3_label'] ?? __('Sonrisas Renovadas') }}</p>
                        </div>

                        <div class="flex flex-col items-center p-3 pt-6 md:pt-3 group">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 shadow-lg shadow-sky-500/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-amber-400">
                                    <path d="M10 14.66V17a1 1 0 0 1-1 1 2 2 0 0 0-2 2v2"></path>
                                    <path d="M14 14.66V17a1 1 0 0 0 1 1 2 2 0 0 1 2 2v2"></path>
                                    <path d="M17.916 10H19.5A2.5 2.5 0 0 0 22 7.5V5a1 1 0 0 0-1-1h-3"></path>
                                    <path d="M4 22h16"></path>
                                    <path d="M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z"></path>
                                    <path d="M6.084 10H4.5A2.5 2.5 0 0 1 2 7.5V5a1 1 0 0 1 1-1h3"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_4_number" class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">{{ $trustbarSec['stat_4_number'] ?? '99.4%' }}</h3>
                            <p data-cms-field="stat_4_label" class="text-xs text-sky-400 font-bold uppercase tracking-wider">{{ $trustbarSec['stat_4_label'] ?? __('Satisfacción Clínica') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 5. OUR DENTAL SOLUTIONS (How It Works)      -->
        <!-- ========================================== -->
        <section id="how-it-works" data-cms-section="how_it_works" data-cms-name="Cómo Trabajamos" class="py-12 bg-slate-50/90 border-t border-slate-200/80" @if(!$howItWorksActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-14">
                    
                    <!-- Left Solutions Features -->
                    <div class="lg:w-1/2 flex flex-col items-start">
                        <div class="flex items-center gap-2 text-sky-500 font-bold text-xs uppercase tracking-widest mb-3">
                            <span class="w-5 h-1 bg-sky-500 rounded-full"></span>
                            <span data-cms-field="eyebrow">{{ $howItWorksSec['eyebrow'] ?? __('NUESTRAS SOLUCIONES DENTALES') }}</span>
                        </div>

                        <h2 data-cms-field="title" class="text-4xl sm:text-5xl font-black text-slate-900 leading-tight mb-5">
                            {{ $howItWorksSec['title'] ?? __('Soluciones Dentales Completas con la Mayor Suavidad.') }}
                        </h2>

                        <p data-cms-field="subtitle" class="text-slate-500 text-sm leading-relaxed mb-6">
                            {{ $howItWorksSec['subtitle'] ?? __('Desde limpiezas preventivas profundas hasta carillas de porcelana ultra finas e implantes guiados en 3D, cuidamos tu salud bucal con precisión quirúrgica y confort total.') }}
                        </p>

                        <div class="space-y-3.5 w-full mb-4">
                            @php
                                $step1 = $howItWorksSec['step_1_title'] ?? null;
                                $step2 = $howItWorksSec['step_2_title'] ?? null;
                                $step3 = $howItWorksSec['step_3_title'] ?? null;
                                $step4 = $howItWorksSec['step_4_title'] ?? null;
                                if (!array_key_exists('step_1_title', $howItWorksSec)) {
                                    $step1 = __('Higiene y Prevención Dental Integral con Ultrasonido Tibio');
                                }
                                if (!array_key_exists('step_2_title', $howItWorksSec)) {
                                    $step2 = __('Especialistas Certificados en Estética y Cirugía Reconstructiva');
                                }
                                if (!array_key_exists('step_3_title', $howItWorksSec)) {
                                    $step3 = __('Protocolos Estrictos de Esterilización Quirúrgica Grado Hospital');
                                }
                                if (!array_key_exists('step_4_title', $howItWorksSec)) {
                                    $step4 = __('Odontología Láser Indolora y Sedación Consciente');
                                }
                                $hasStep1 = filled(trim($step1 ?? ''));
                                $hasStep2 = filled(trim($step2 ?? ''));
                                $hasStep3 = filled(trim($step3 ?? ''));
                                $hasStep4 = filled(trim($step4 ?? ''));
                            @endphp
                            <!-- Feature 1 -->
                            <div data-cms-row="step_1_title" class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1" @if(!$hasStep1) style="display: none;" @endif>
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                </div>
                                <span data-cms-field="step_1_title" class="font-bold text-slate-800 text-sm">{{ $step1 }}</span>
                            </div>

                            <!-- Feature 2 -->
                            <div data-cms-row="step_2_title" class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1" @if(!$hasStep2) style="display: none;" @endif>
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                                    </svg>
                                </div>
                                <span data-cms-field="step_2_title" class="font-bold text-slate-800 text-sm">{{ $step2 }}</span>
                            </div>

                            <!-- Feature 3 -->
                            <div data-cms-row="step_3_title" class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1" @if(!$hasStep3) style="display: none;" @endif>
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>
                                        <path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"></path>
                                    </svg>
                                </div>
                                <span data-cms-field="step_3_title" class="font-bold text-slate-800 text-sm">{{ $step3 }}</span>
                            </div>

                            <!-- Feature 4 -->
                            <div data-cms-row="step_4_title" class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1" @if(!$hasStep4) style="display: none;" @endif>
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path>
                                        <circle cx="12" cy="8" r="6"></circle>
                                    </svg>
                                </div>
                                <span data-cms-field="step_4_title" class="font-bold text-slate-800 text-sm">{{ $step4 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Double Image Composition with Sparkle -->
                    <div class="lg:w-1/2 relative w-full flex justify-center">
                        <div class="relative w-full max-w-[460px]">
                            <div class="absolute -top-6 -right-6 text-sky-400 text-5xl z-0 animate-pulse">✦</div>
                            
                            <div class="rounded-[40px] overflow-hidden shadow-2xl border-4 border-white relative z-10 bg-slate-100 aspect-[4/3]">
                                <img 
                                    data-cms-field="image_1_url" 
                                    src="{{ !empty($howItWorksSec['image_1_url']) ? $howItWorksSec['image_1_url'] : 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=800&q=80' }}" 
                                    alt="{{ __('images.home_how_it_works_1') }}" 
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                                    loading="lazy" 
                                />
                            </div>

                            <div class="w-52 h-52 rounded-[35px] overflow-hidden shadow-2xl border-4 border-white absolute -bottom-8 -right-4 z-20 bg-slate-100 hover:scale-105 transition-transform duration-300">
                                <img 
                                    data-cms-field="image_2_url" 
                                    src="{{ !empty($howItWorksSec['image_2_url']) ? $howItWorksSec['image_2_url'] : 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80' }}" 
                                    alt="{{ __('images.home_how_it_works_2') }}" 
                                    class="w-full h-full object-cover" 
                                    loading="lazy" 
                                />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 6. PATIENT TESTIMONIALS SPOTLIGHT CARD      -->
        <!-- ========================================== -->
        @php
            $testimonialItems = $testimonialsSec['items'] ?? [];
            if (empty($testimonialItems) && isset($testimonials) && $testimonials->isNotEmpty()) {
                $testimonialItems = $testimonials->map(function ($t) {
                    return [
                        'patient_name' => $t->patient_name,
                        'treatment' => $t->treatment,
                        'rating' => (float) $t->rating,
                        'comment' => $t->comment,
                        'avatar_url' => $t->patient_avatar ?? '',
                    ];
                })->values()->toArray();
            }
            if (empty($testimonialItems)) {
                $testimonialItems = [
                    [
                        'patient_name' => 'Claire Davenport',
                        'treatment' => __('Alineadores Invisibles y Blanqueamiento'),
                        'rating' => 5,
                        'comment' => __('La clínica más acogedora y avanzada tecnológicamente que he visitado. La Dra. Rostova me explicó cada detalle de la simulación digital. El equipo es súper profesional y cálido.'),
                        'avatar_url' => '',
                    ],
                    [
                        'patient_name' => 'David Morales',
                        'treatment' => __('Implante Dental y Corona Circonio'),
                        'rating' => 4.5,
                        'comment' => __('Tenía mucho miedo a la cirugía dental, pero el Dr. Vance y su equipo hicieron que el implante fuera 100% indoloro. La recuperación fue rapidísima. ¡Totalmente recomendados!'),
                        'avatar_url' => '',
                    ],
                    [
                        'patient_name' => 'Emma Watson-Lee',
                        'treatment' => __('Blanqueamiento Dental Láser'),
                        'rating' => 5,
                        'comment' => __('Llegué con manchas de café de años y salí una hora después con una sonrisa radiante. Cero sensibilidad en los días posteriores. ¡Atención impecable!'),
                        'avatar_url' => '',
                    ],
                    [
                        'patient_name' => 'Santiago Ramírez',
                        'treatment' => __('Limpieza y Revisión General'),
                        'rating' => 4.5,
                        'comment' => __('Instalaciones ultra modernas y precios transparentes. No intentan venderte cosas innecesarias. Mis dientes se sienten más limpios que nunca.'),
                        'avatar_url' => '',
                    ],
                ];
            }
            $firstTestimonial = $testimonialItems[0];
        @endphp

        @if(count($testimonialItems) > 0 || $isEditor)
            <section id="testimonials" data-cms-section="testimonials_cases" data-cms-name="Casos de Éxito y Testimonios" class="py-12 bg-white" @if(!$testimonialsActive && !$isEditor) style="display: none;" @endif>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="rounded-[40px] overflow-hidden shadow-2xl border border-slate-200/80 grid grid-cols-1 lg:grid-cols-2">
                        
                        <!-- Left Big Image (editable con biblioteca de medios, sin botón eliminar) -->
                        <div class="h-72 lg:h-full relative bg-slate-100 min-h-[340px]">
                            <img 
                                data-cms-field="image_url"
                                src="{{ $testimonialsSec['image_url'] ?? 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1000&q=80' }}" 
                                alt="{{ __('images.home_testimonials') }}" 
                                class="w-full h-full object-cover min-h-[340px]" 
                                loading="lazy" 
                            />
                        </div>

                        <!-- Right Testimonial Navy Content (Alpine.js Slider) -->
                        <div 
                            x-data="testimonialsSpotlight({ items: @js($testimonialItems) })"
                            class="bg-[#13223f] text-white p-8 md:p-12 relative flex flex-col justify-between overflow-hidden"
                            @mouseenter="pauseAutoPlay()"
                            @mouseleave="startAutoPlay()"
                        >
                            <div class="absolute inset-0 bg-[radial-gradient(#0ea5e9_1px,transparent_1px)] [background-size:16px_16px] opacity-10 pointer-events-none"></div>

                            <div class="relative z-10 flex-1 flex flex-col">
                                <!-- Eyebrow -->
                                <div class="flex items-center gap-2 text-sky-400 font-bold text-xs uppercase tracking-widest mb-3">
                                    <span class="w-5 h-1 bg-sky-400 rounded-full"></span>
                                    <span data-cms-field="eyebrow">{{ $testimonialsSec['eyebrow'] ?? __('TESTIMONIOS DE PACIENTES') }}</span>
                                </div>

                                <!-- Título de Sección -->
                                <h3 data-cms-field="title" class="text-2xl sm:text-3xl font-black text-white leading-tight mb-2">
                                    {{ $testimonialsSec['title'] ?? __('Lo que Dicen Nuestros Pacientes sobre su Experiencia Dental.') }}
                                </h3>

                                <!-- Subtítulo de Sección -->
                                <span data-cms-field="subtitle" class="text-xs font-bold text-sky-400 uppercase tracking-widest block mb-6">
                                    {{ $testimonialsSec['subtitle'] ?? __('Opinión Verificada de Google & WhatsApp') }}
                                </span>

                                <!-- 1. PACIENTE (USUARIO) ARRIBA DEL COMENTARIO -->
                                <div class="flex items-center gap-4 mb-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-500 to-sky-600 text-white font-black text-base flex items-center justify-center border-2 border-sky-400 shadow-md shrink-0 overflow-hidden">
                                        <template x-if="current.avatar_url">
                                            <img :src="current.avatar_url" :alt="current.patient_name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!current.avatar_url">
                                            <span x-text="(current.patient_name || 'P').charAt(0).toUpperCase()">{{ strtoupper(substr($firstTestimonial['patient_name'] ?? 'P', 0, 1)) }}</span>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-white text-base truncate" x-text="current.patient_name">{{ $firstTestimonial['patient_name'] }}</h4>
                                        <p class="text-xs text-sky-400 font-semibold truncate" x-text="current.treatment">{{ $firstTestimonial['treatment'] }}</p>
                                    </div>
                                </div>

                                <!-- 2. ESTRELLAS Y CALIFICACIÓN (Soporta 1, 1.5, 2, ..., 5) -->
                                <div class="flex items-center gap-2.5 mb-4">
                                    <div class="flex items-center text-amber-400 gap-1">
                                        <template x-for="starIndex in [1, 2, 3, 4, 5]" :key="starIndex">
                                            <div class="relative w-4 h-4 flex items-center justify-center">
                                                <!-- Estrella Llena -->
                                                <template x-if="(parseFloat(current.rating) || 5) >= starIndex">
                                                    <svg class="w-4 h-4 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                </template>
                                                <!-- Media Estrella -->
                                                <template x-if="(parseFloat(current.rating) || 5) < starIndex && (parseFloat(current.rating) || 5) >= (starIndex - 0.5)">
                                                    <div class="relative w-4 h-4">
                                                        <svg class="w-4 h-4 text-slate-600 fill-slate-700/60" viewBox="0 0 24 24">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                        <div class="absolute inset-0 overflow-hidden w-1/2">
                                                            <svg class="w-4 h-4 text-amber-400 fill-amber-400" viewBox="0 0 24 24">
                                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </template>
                                                <!-- Estrella Vacía -->
                                                <template x-if="(parseFloat(current.rating) || 5) < (starIndex - 0.5)">
                                                    <svg class="w-4 h-4 text-slate-600 fill-slate-700/60" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <!-- Número simple de color amarillo sin cápsula ni fondo -->
                                    <span class="text-sm font-black text-amber-400 select-none ml-1" x-text="Number(current.rating || 5)">
                                        {{ (float)($firstTestimonial['rating'] ?? 5) }}
                                    </span>
                                </div>

                                <!-- 3. COMENTARIO ABAJO DEL USUARIO -->
                                <div class="relative mb-6 min-h-[72px]">
                                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed italic font-light">
                                        &quot;<span x-text="current.comment">{{ $firstTestimonial['comment'] }}</span>&quot;
                                    </p>
                                </div>
                            </div>

                            <!-- Footer con Indicadores y Botones Anterior/Siguiente -->
                            <div class="flex items-center justify-between pt-4 border-t border-slate-800/80 mt-auto relative z-10">
                                <!-- Indicator Pills -->
                                <div class="flex items-center gap-2">
                                    <template x-for="(it, idx) in items" :key="idx">
                                        <button 
                                            type="button" 
                                            @click="goTo(idx)" 
                                            :class="currentIndex === idx ? 'w-8 bg-sky-400' : 'w-2.5 bg-slate-700 hover:bg-slate-500'" 
                                            class="h-2 rounded-full transition-all duration-300 focus:outline-none cursor-pointer"
                                            :title="'{{ __('Ver testimonio') }} ' + (idx + 1)"
                                        ></button>
                                    </template>
                                </div>

                                <!-- Botones Anterior / Siguiente -->
                                <div class="flex items-center gap-2" x-show="items.length > 1">
                                    <button 
                                        type="button" 
                                        @click="prev()" 
                                        class="w-8 h-8 rounded-full bg-slate-800 hover:bg-sky-500 text-slate-300 hover:text-white flex items-center justify-center transition-colors border border-slate-700 focus:outline-none cursor-pointer shadow-sm"
                                        title="{{ __('Testimonio anterior') }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="next()" 
                                        class="w-8 h-8 rounded-full bg-slate-800 hover:bg-sky-500 text-slate-300 hover:text-white flex items-center justify-center transition-colors border border-slate-700 focus:outline-none cursor-pointer shadow-sm"
                                        title="{{ __('Siguiente testimonio') }}"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </section>
        @endif

        <!-- ========================================== -->
        <!-- 7. COMPREHENSIVE TREATMENTS CATALOG        -->
        <!-- ========================================== -->
        <section id="services" data-cms-section="services_catalog" data-cms-name="Catálogo de Tratamientos" class="py-14 bg-slate-50/90 border-t border-slate-200/80" @if(!$servicesActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span data-cms-field="eyebrow" class="text-sky-500 font-bold text-xs tracking-widest uppercase mb-2 block">{{ $servicesSec['eyebrow'] ?? __('Especialidades Odontológicas') }}</span>
                    <h2 data-cms-field="title" class="text-4xl sm:text-5xl font-black text-slate-900 mb-4">{{ $servicesSec['title'] ?? __('Tratamientos Odontológicos Especializados') }}</h2>
                    <p data-cms-field="subtitle" class="text-slate-500 text-sm">{{ $servicesSec['subtitle'] ?? __('Cuidado dental de alta precisión con cero dolor y tecnología digital 3D.') }}</p>
                </div>

                <!-- Treatments Grid with Images (Max 6) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    @foreach($services->take(6) as $index => $service)
                        <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 relative flex flex-col justify-between group hover:-translate-y-1.5">
                            <!-- Clickable Card Body linking to details -->
                            <a href="{{ route('services.show', $service->slug) }}" class="block focus:outline-none flex-1">
                                <!-- Treatment Image Header -->
                                <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                    <img 
                                        src="{{ $service->image_url }}" 
                                        alt="{{ __('images.service_card', ['service' => $service->name, 'clinic' => $clinic->clinic_name ?? 'Aura Dental Studio']) }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy"
                                    />
                                </div>

                                <div class="p-6">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2.5 py-0.5 rounded-full inline-block mb-2.5">
                                        {{ __($service->category) }}
                                    </span>

                                    <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-sky-600 transition-colors">
                                        {{ $service->name }}
                                    </h3>

                                    <p class="text-slate-500 text-xs leading-relaxed mb-2">
                                        {{ $service->short_description ?? Str::limit($service->description, 110) }}
                                    </p>
                                </div>
                            </a>

                            <div class="px-6 pb-6 pt-0 flex items-center justify-between">
                                <a 
                                    href="{{ route('services.show', $service->slug) }}"
                                    class="text-xs font-bold text-slate-600 hover:text-sky-600 transition-colors"
                                >
                                    {{ __('Ver detalles') }}
                                </a>

                                <a 
                                    href="{{ route('schedule') }}?service_id={{ $service->id }}"
                                    class="text-xs font-extrabold text-sky-500 group-hover:text-sky-600 uppercase tracking-wider flex items-center gap-1.5 transition-all text-left"
                                >
                                    <span>{{ __('Agendar') }} »</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 group-hover:translate-x-1 transition-transform">
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($services->count() > 6)
                    <div class="text-center pt-2">
                        <a 
                            href="{{ route('services.index') }}" 
                            class="inline-flex items-center gap-2 bg-[#13223f] hover:bg-sky-600 text-white px-8 py-4 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-slate-900/10 transition-all hover:scale-105 hover:shadow-sky-500/25"
                        >
                            <span>{{ __('Ver todos los tratamientos') }} ({{ $services->count() }}) »</span>
                        </a>
                    </div>
                @endif

            </div>
        </section>

        <!-- ========================================== -->
        <!-- 8. PREGUNTAS FRECUENTES (FAQS)             -->
        <!-- ========================================== -->
        @php
            $faqItems = $faqsSec['items'] ?? [];
            if (empty($faqItems) && isset($faqs) && $faqs->isNotEmpty()) {
                $faqItems = $faqs->map(function ($f) {
                    return [
                        'question' => $f->question,
                        'answer' => $f->answer,
                    ];
                })->values()->toArray();
            }
            if (empty($faqItems)) {
                $faqItems = [
                    [
                        'question' => __('¿Cómo puedo agendar o reagendar una cita?'),
                        'answer' => __('Puedes agendar directamente a través de nuestro formulario web 24/7 o escribiendo a nuestro WhatsApp. Si necesitas reagendar, avísanos con al menos 24 horas de anticipación.'),
                    ],
                    [
                        'question' => __('¿Dolerán los tratamientos o la limpieza dental?'),
                        'answer' => __('No. Nos enfocamos en odontología indolora y confortable. Utilizamos anestesia local computarizada, instrumental ultrasónico tibio y técnicas suaves para garantizar cero dolor.'),
                    ],
                    [
                        'question' => __('¿Aceptan seguros médicos o planes de pago?'),
                        'answer' => __('¡Sí! Trabajamos con las principales aseguradoras y ofrecemos planes de financiamiento sin intereses de hasta 12 meses para tratamientos como ortodoncia e implantes.'),
                    ],
                    [
                        'question' => __('¿Qué debo esperar durante mi primera consulta dental?'),
                        'answer' => __('Tu primera cita dura aproximadamente 45 minutos e incluye radiografías digitales HD, escaneo 3D intraoral, revisión periodontal completa y tu plan de tratamiento personalizado.'),
                    ],
                ];
            }
        @endphp

        @if(count($faqItems) > 0 || $isEditor)
            <section id="faqs" data-cms-section="faqs" data-cms-name="Preguntas Frecuentes" class="py-16 bg-slate-50/70 border-t border-slate-200/80" @if(!$faqsActive && !$isEditor) style="display: none;" @endif>
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-xl mx-auto mb-12">
                        <span data-cms-field="eyebrow" class="text-sky-500 font-bold text-xs tracking-widest uppercase mb-2 block">{{ $faqsSec['eyebrow'] ?? __('Dudas Comunes') }}</span>
                        <h2 data-cms-field="title" class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">{{ $faqsSec['title'] ?? __('Preguntas Frecuentes') }}</h2>
                        <p data-cms-field="subtitle" class="text-slate-500 text-xs sm:text-sm">{{ $faqsSec['subtitle'] ?? __('Todo lo que necesitas saber antes de tu primera consulta odontológica.') }}</p>
                    </div>

                    <div 
                        class="space-y-3" 
                        x-data="faqsAccordion({ items: @js($faqItems) })"
                    >
                        <template x-for="(faq, fIndex) in items" :key="fIndex">
                            <div class="border border-slate-200/90 rounded-2xl overflow-hidden bg-white shadow-xs transition-all duration-200 hover:border-sky-300">
                                <button 
                                    type="button" 
                                    @click="toggle(fIndex)" 
                                    class="w-full p-5 text-left flex justify-between items-center font-bold text-slate-900 text-sm hover:text-sky-600 transition-colors focus:outline-none cursor-pointer"
                                >
                                    <span class="pr-4" x-text="faq.question"></span>
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-500 transition-all duration-200" :class="activeFaq === fIndex ? 'rotate-180 bg-sky-50 text-sky-600' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </div>
                                </button>
                                <div 
                                    x-show="activeFaq === fIndex" 
                                    x-cloak 
                                    class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3.5"
                                >
                                    <p class="whitespace-pre-line" x-text="faq.answer"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>
        @endif

        <!-- ========================================== -->
        <!-- 9. CONTACT & APPOINTMENT BOOKING           -->
        <!-- ========================================== -->
        <section id="appointment-section" data-cms-section="cta_booking" data-cms-name="Módulo de Reserva Online" class="py-16 bg-white border-t border-slate-200/80" @if(!$ctaBookingActive && !$isEditor) style="display: none;" @endif>
            <div id="booking" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-14">
                    
                    <!-- Left Column: Minimalist Dark Navy Card (#13223f) matching emergency right column -->
                    <div class="lg:w-1/2 w-full flex justify-center">
                        <div class="bg-[#13223f] text-white rounded-[40px] p-8 sm:p-10 w-full max-w-[480px] flex flex-col justify-between relative border border-slate-800 shadow-2xl overflow-hidden hover:shadow-sky-500/10 transition-all duration-300 min-h-[440px]">
                            <!-- Glow accent -->
                            <div class="absolute -top-16 -right-16 w-56 h-56 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="relative z-10 flex-1 flex flex-col">
                                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 font-bold text-xs uppercase tracking-widest mb-5 self-start">
                                    <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                                    <span data-cms-field="eyebrow">{{ $ctaBookingSec['eyebrow'] ?? __('Reserva Online 24/7') }}</span>
                                </div>

                                <h2 data-cms-field="title" class="text-3xl sm:text-4xl font-black text-white leading-tight mb-3 tracking-tight">
                                    {{ $ctaBookingSec['title'] ?? __('Reserva tu Cita Odontológica Online') }}
                                </h2>

                                <p data-cms-field="subtitle" class="text-slate-300 text-xs sm:text-sm leading-relaxed mb-6 font-light">
                                    {{ $ctaBookingSec['subtitle'] ?? __('Selecciona el tratamiento que necesitas y reserva tu turno en el día y horario que mejor se adapte a tu rutina.') }}
                                </p>

                                <!-- Benefits Checklist -->
                                @php
                                    $check1 = $ctaBookingSec['check_1'] ?? null;
                                    $check2 = $ctaBookingSec['check_2'] ?? null;
                                    $check3 = $ctaBookingSec['check_3'] ?? null;
                                    if (!array_key_exists('check_1', $ctaBookingSec)) {
                                        $check1 = __('Confirmación instantánea de cita y recordatorios automáticos por WhatsApp.');
                                    }
                                    if (!array_key_exists('check_2', $ctaBookingSec)) {
                                        $check2 = __('Especialistas certificados en estética y cirugía reconstructiva.');
                                    }
                                    if (!array_key_exists('check_3', $ctaBookingSec)) {
                                        $check3 = __('Horarios flexibles organizados por Turno Mañana y Turno Tarde.');
                                    }
                                    $hasCheck1 = filled(trim($check1 ?? ''));
                                    $hasCheck2 = filled(trim($check2 ?? ''));
                                    $hasCheck3 = filled(trim($check3 ?? ''));
                                @endphp
                                <ul data-cms-group="cta_booking_checks" class="space-y-3 mb-8 text-xs text-slate-300" @if(!$hasCheck1 && !$hasCheck2 && !$hasCheck3) style="display: none;" @endif>
                                    <li data-cms-row="check_1" class="flex items-center gap-2.5" @if(!$hasCheck1) style="display: none;" @endif>
                                        <div class="w-5 h-5 rounded-full bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/30">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span data-cms-field="check_1">{{ $check1 }}</span>
                                    </li>
                                    <li data-cms-row="check_2" class="flex items-center gap-2.5" @if(!$hasCheck2) style="display: none;" @endif>
                                        <div class="w-5 h-5 rounded-full bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/30">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span data-cms-field="check_2">{{ $check2 }}</span>
                                    </li>
                                    <li data-cms-row="check_3" class="flex items-center gap-2.5" @if(!$hasCheck3) style="display: none;" @endif>
                                        <div class="w-5 h-5 rounded-full bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/30">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span data-cms-field="check_3">{{ $check3 }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- CTA Action -->
                            <div class="relative z-10 w-full mt-auto">
                                <a 
                                    href="{{ $ctaBookingSec['button_url'] ?? route('schedule') }}"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-400 text-white font-bold py-4 px-8 rounded-2xl shadow-lg shadow-sky-500/25 hover:scale-[1.02] active:scale-95 transition-all text-xs uppercase tracking-wider text-center group cursor-pointer"
                                >
                                    <span data-cms-field="button_text">{{ $ctaBookingSec['button_text'] ?? __('Reservar Cita Online »') }}</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Female Dentist Cutout & Dark Navy Emergency Card -->
                    <div class="lg:w-1/2 w-full flex justify-center">
                        <div class="bg-sky-50/70 rounded-[40px] p-8 pb-10 w-full max-w-[440px] flex flex-col items-center text-center relative border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-300 min-h-[440px] justify-between">
                            
                            <!-- Dentist Image Cutout -->
                            <div class="w-56 h-56 rounded-full overflow-hidden border-4 border-white shadow-xl mb-6 bg-slate-200 hover:scale-105 transition-transform duration-300 relative group cursor-pointer">
                                <img
                                    data-cms-field="image_url"
                                    src="{{ $ctaBookingSec['image_url'] ?? 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=600&q=80' }}"
                                    alt="{{ __('images.home_booking_cta', ['clinic' => $clinic->clinic_name ?? 'Aura Dental Studio']) }}"
                                    class="w-full h-full object-cover"
                                    loading="eager"
                                />
                            </div>

                            <!-- Emergency Card (Dark Navy #13223f) -->
                            <div class="bg-[#13223f] text-white p-6 rounded-3xl shadow-2xl border border-slate-800 w-full flex items-center gap-4 hover:scale-[1.02] transition-transform">
                                <div class="w-12 h-12 rounded-xl bg-sky-500 text-white flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
                                    </svg>
                                </div>
                                <div class="text-left min-w-0 flex-1">
                                    <h4 data-cms-field="emergency_title" class="font-extrabold text-white text-base truncate">{{ $ctaBookingSec['emergency_title'] ?? __('Llamada de Urgencia 24/7') }}</h4>
                                    <p data-cms-field="emergency_subtitle" class="text-[11px] text-slate-300 truncate">{{ $ctaBookingSec['emergency_subtitle'] ?? __('Atención el mismo día para dolor agudo') }}</p>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic->emergency_phone ?? $clinic->phone) }}" class="text-sky-400 font-bold text-xs hover:underline block mt-0.5">
                                        {{ $clinic->emergency_phone ?? $clinic->phone }}
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

    <script>
        function testimonialsSpotlight(config) {
            return {
                currentIndex: 0,
                items: config.items || [],
                timer: null,

                init() {
                    window.addEventListener('cms-testimonials_cases-items-updated', (e) => {
                        if (Array.isArray(e.detail)) {
                            this.items = e.detail;
                            if (this.currentIndex >= this.items.length) {
                                this.currentIndex = Math.max(0, this.items.length - 1);
                            }
                        }
                    });

                    window.addEventListener('cms-testimonials_cases-updated', (e) => {
                        if (e.detail && Array.isArray(e.detail.items)) {
                            this.items = e.detail.items;
                            if (this.currentIndex >= this.items.length) {
                                this.currentIndex = Math.max(0, this.items.length - 1);
                            }
                        }
                    });

                    window.addEventListener('cms-preview-testimonial-index', (e) => {
                        if (typeof e.detail === 'number') {
                            this.goTo(e.detail);
                        }
                    });

                    this.startAutoPlay();
                },

                get current() {
                    if (!this.items || this.items.length === 0) {
                        return {
                            patient_name: @json(__('Paciente Verificado')),
                            treatment: @json(__('Tratamiento Dental')),
                            rating: 5,
                            comment: @json(__('Excelente experiencia clínica en :clinic.', ['clinic' => $clinic->clinic_name ?? 'Aura Dental Studio'])),
                            avatar_url: ''
                        };
                    }
                    return this.items[this.currentIndex] || this.items[0];
                },

                goTo(index) {
                    if (index >= 0 && index < this.items.length) {
                        this.currentIndex = index;
                    }
                },

                next() {
                    if (this.items.length <= 1) return;
                    this.currentIndex = (this.currentIndex + 1) % this.items.length;
                },

                prev() {
                    if (this.items.length <= 1) return;
                    this.currentIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
                },

                startAutoPlay() {
                    if (window.location.search.includes('editor_preview=1')) return;
                    this.pauseAutoPlay();
                    if (this.items.length > 1) {
                        this.timer = setInterval(() => {
                            this.next();
                        }, 8000);
                    }
                },

                pauseAutoPlay() {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                }
            };
        }

        function faqsAccordion(config) {
            return {
                activeFaq: 0,
                items: config.items || [],

                init() {
                    window.addEventListener('cms-faqs-items-updated', (e) => {
                        if (Array.isArray(e.detail)) {
                            this.items = e.detail;
                        }
                    });

                    window.addEventListener('cms-faqs-updated', (e) => {
                        if (e.detail && Array.isArray(e.detail.items)) {
                            this.items = e.detail.items;
                        }
                    });

                    window.addEventListener('cms-preview-faq-index', (e) => {
                        if (typeof e.detail === 'number') {
                            this.activeFaq = e.detail;
                        }
                    });
                },

                toggle(index) {
                    this.activeFaq = this.activeFaq === index ? null : index;
                }
            };
        }
    </script>

    @if(request()->has('editor_preview'))
        @include('partials.editor-bridge')
    @endif

</body>
</html>