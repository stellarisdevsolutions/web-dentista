<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    <title>Nosotros y Especialistas | {{ $clinic->clinic_name }}</title>
    <meta name="description" content="Conoce a nuestro equipo médico odontológico de especialistas y nuestra trayectoria de excelencia clínica en {{ $clinic->clinic_name }}.">

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f0f7ff] text-slate-900 selection:bg-sky-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- GLOBAL NAVBAR -->
    @include('partials.navbar')

    @php
        $sectionsMap = isset($sections) ? $sections : \App\Models\SiteSection::forPage('about')->get()->keyBy('section_key');
        $aboutHero = $sectionsMap->get('about_hero')?->content ?? [];
        $aboutHeroActive = $sectionsMap->get('about_hero')?->is_active ?? true;
        $isEditor = request()->has('editor_preview');

        $heroEyebrow = $aboutHero['eyebrow'] ?? ('CONOCE ' . strtoupper($clinic->clinic_name));
        $heroTitle = $aboutHero['title'] ?? 'Más de 20+ Años Brindando Excelencia Odontológica';
        $heroSubtitle = $aboutHero['subtitle'] ?? 'Un equipo multidisciplinario de odontólogos especialistas apasionados por tu bienestar, tecnología mínimamente invasiva y una experiencia clínica libre de dolor.';

        $primaryText = $aboutHero['button_primary_text'] ?? null;
        $primaryUrl = $aboutHero['button_primary_url'] ?? '#dentists';
        if (!array_key_exists('button_primary_text', $aboutHero)) {
            $primaryText = 'Conocer Especialistas ↓';
        }

        $secondaryText = $aboutHero['button_secondary_text'] ?? null;
        $secondaryUrl = $aboutHero['button_secondary_url'] ?? route('schedule');
        if (!array_key_exists('button_secondary_text', $aboutHero)) {
            $secondaryText = 'Agendar Consulta Online »';
        }

        $hasPrimaryBtn = filled(trim($primaryText ?? ''));
        $hasSecondaryBtn = filled(trim($secondaryText ?? ''));

        $aboutStory = $sectionsMap->get('about_story')?->content ?? [];
        $aboutStoryActive = $sectionsMap->get('about_story')?->is_active ?? true;

        $storyEyebrow = $aboutStory['eyebrow'] ?? 'NUESTRA FILOSOFÍA MÉDICA';
        $storyTitle = $aboutStory['title'] ?? 'Cuidado Odontológico Integral, Cálido y Sin Dolor.';
        $storyDescription = $aboutStory['description'] ?? ('En ' . $clinic->clinic_name . ', combinamos ciencia médica de vanguardia con un trato profundamente humano. Nuestro compromiso es que cada visita sea una experiencia tranquila y gratificante, cuidando tanto de la salud funcional de tus dientes como de la estética natural de tu sonrisa.');

        $storyImage1 = !empty($aboutStory['image_1_url']) ? $aboutStory['image_1_url'] : 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=800&q=80';
        $storyImage2 = !empty($aboutStory['image_2_url']) ? $aboutStory['image_2_url'] : 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=600&q=80';
        $badgeYears = $aboutStory['badge_years'] ?? '20+';
        $badgeText = $aboutStory['badge_text'] ?? 'Años de Trayectoria';

        $feature1Title = $aboutStory['feature_1_title'] ?? null;
        $feature1Desc = $aboutStory['feature_1_desc'] ?? null;
        if (!array_key_exists('feature_1_title', $aboutStory)) {
            $feature1Title = 'Acompañamiento Cercano';
            $feature1Desc = 'Orientación clara y soporte antes, durante y después de cada tratamiento.';
        }

        $feature2Title = $aboutStory['feature_2_title'] ?? null;
        $feature2Desc = $aboutStory['feature_2_desc'] ?? null;
        if (!array_key_exists('feature_2_title', $aboutStory)) {
            $feature2Title = 'Especialistas Certificados';
            $feature2Desc = 'Postgrados universitarios y formación continua en cada área odontológica.';
        }

        $checkText = $aboutStory['check_text'] ?? null;
        if (!array_key_exists('check_text', $aboutStory)) {
            $checkText = 'Contamos con quirófanos digitales, escáner intraoral 3D y sedación consciente.';
        }

        $storyButtonText = $aboutStory['button_text'] ?? null;
        $storyButtonUrl = $aboutStory['button_url'] ?? route('schedule');
        if (!array_key_exists('button_text', $aboutStory)) {
            $storyButtonText = 'Agendar Cita con un Especialista »';
        }

        $hasFeature1 = filled(trim($feature1Title ?? ''));
        $hasFeature2 = filled(trim($feature2Title ?? ''));
        $hasCheckText = filled(trim($checkText ?? ''));
        $hasStoryButton = filled(trim($storyButtonText ?? ''));

        $aboutTrustbar = $sectionsMap->get('about_trustbar')?->content ?? [];
        $aboutTrustbarActive = $sectionsMap->get('about_trustbar')?->is_active ?? true;

        $stat1Number = $aboutTrustbar['stat_1_number'] ?? null;
        $stat1Label = $aboutTrustbar['stat_1_label'] ?? null;
        if (!array_key_exists('stat_1_number', $aboutTrustbar)) {
            $stat1Number = '567+';
            $stat1Label = 'Pacientes Felices';
        }

        $stat2Number = $aboutTrustbar['stat_2_number'] ?? null;
        $stat2Label = $aboutTrustbar['stat_2_label'] ?? null;
        if (!array_key_exists('stat_2_number', $aboutTrustbar)) {
            $stat2Number = '23K+';
            $stat2Label = 'Atenciones Realizadas';
        }

        $stat3Number = $aboutTrustbar['stat_3_number'] ?? null;
        $stat3Label = $aboutTrustbar['stat_3_label'] ?? null;
        if (!array_key_exists('stat_3_number', $aboutTrustbar)) {
            $stat3Number = '241+';
            $stat3Label = 'Casos de Sonrisa';
        }

        $stat4Number = $aboutTrustbar['stat_4_number'] ?? null;
        $stat4Label = $aboutTrustbar['stat_4_label'] ?? null;
        if (!array_key_exists('stat_4_number', $aboutTrustbar)) {
            $stat4Number = '16+';
            $stat4Label = 'Certificaciones de Calidad';
        }

        $hasStat1 = filled(trim($stat1Number ?? ''));
        $hasStat2 = filled(trim($stat2Number ?? ''));
        $hasStat3 = filled(trim($stat3Number ?? ''));
        $hasStat4 = filled(trim($stat4Number ?? ''));

        $aboutSpecialists = $sectionsMap->get('about_specialists')?->content ?? [];
        $aboutSpecialistsActive = $sectionsMap->get('about_specialists')?->is_active ?? true;

        $specialistsEyebrow = $aboutSpecialists['eyebrow'] ?? null;
        if (!array_key_exists('eyebrow', $aboutSpecialists)) {
            $specialistsEyebrow = 'CUERPO MÉDICO DE ÉLITE';
        }

        $specialistsTitle = $aboutSpecialists['title'] ?? null;
        if (!array_key_exists('title', $aboutSpecialists)) {
            $specialistsTitle = 'Nuestros Especialistas Odontológicos';
        }

        $specialistsSubtitle = $aboutSpecialists['subtitle'] ?? null;
        if (!array_key_exists('subtitle', $aboutSpecialists)) {
            $specialistsSubtitle = 'Doctores colegiados con postgrados y amplia experiencia en cirugía, implantes, ortodoncia, odontopediatría y diseño de sonrisa.';
        }

        $hasSpecialistsEyebrow = filled(trim($specialistsEyebrow ?? ''));
        $hasSpecialistsTitle = filled(trim($specialistsTitle ?? ''));
        $hasSpecialistsSubtitle = filled(trim($specialistsSubtitle ?? ''));
    @endphp

<main class="min-h-screen bg-white text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section data-cms-section="about_hero" data-cms-name="Portada Nosotros (Hero)" class="bg-[#13223f] text-white py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden" @if(!$aboutHeroActive && !$isEditor) style="display: none;" @endif>
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 left-0 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto text-center relative z-10 animate-fade-in-up">
                <span data-cms-field="eyebrow" class="text-sky-400 font-bold text-xs uppercase tracking-widest block mb-2">{{ $heroEyebrow }}</span>
                <h1 data-cms-field="title" class="text-4xl sm:text-5xl font-black text-white leading-tight mb-4">{{ $heroTitle }}</h1>
                <p data-cms-field="subtitle" class="text-slate-300 text-sm max-w-2xl mx-auto font-light leading-relaxed mb-8">{{ $heroSubtitle }}</p>
                <div data-cms-group="hero_buttons" class="flex flex-wrap justify-center items-center gap-4" @if(!$hasPrimaryBtn && !$hasSecondaryBtn) style="display: none;" @endif>
                    <a data-cms-row="button_primary_text" href="{{ $primaryUrl }}" class="bg-sky-500 hover:bg-sky-600 text-white px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-sky-500/25 hover:scale-105" @if(!$hasPrimaryBtn) style="display: none;" @endif>
                        <span data-cms-field="button_primary_text">{{ $primaryText }}</span>
                    </a>
                    <a data-cms-row="button_secondary_text" href="{{ $secondaryUrl }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition-all hover:scale-105" @if(!$hasSecondaryBtn) style="display: none;" @endif>
                        <span data-cms-field="button_secondary_text">{{ $secondaryText }}</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- SECTION 1: ABOUT US & CLINIC STORY         -->
        <!-- ========================================== -->
        <section data-cms-section="about_story" data-cms-name="Filosofía Médica e Historia" class="py-20 bg-white relative" @if(!$aboutStoryActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    
                    <!-- Left: Graphic Doctor Photos -->
                    <div class="lg:w-1/2 relative w-full flex justify-center">
                        <div class="relative w-full max-w-[440px]">
                            <!-- Blue Arc Decoration -->
                            <div class="absolute -top-6 -left-6 w-24 h-24 border-8 border-sky-400 rounded-full border-r-transparent border-b-transparent -z-10 animate-float"></div>

                            <!-- Main Group Doctor Image -->
                            <div class="rounded-[40px] overflow-hidden shadow-2xl border-4 border-white bg-slate-100 hover:shadow-sky-500/20 transition-all duration-500">
                                <img
                                    data-cms-field="image_1_url"
                                    src="{{ $storyImage1 }}"
                                    alt="{{ $storyTitle }}"
                                    class="w-full h-[360px] object-cover hover:scale-105 transition-transform duration-700"
                                    loading="eager"
                                />
                            </div>

                            <!-- Experience Circular Badge -->
                            <div class="absolute top-1/2 left-1/3 -translate-x-1/2 -translate-y-1/2 w-28 h-28 rounded-full bg-[#13223f] text-white border-4 border-white shadow-2xl flex flex-col items-center justify-center text-center p-2 z-20 hover:scale-110 transition-transform duration-300">
                                <span data-cms-field="badge_years" class="text-2xl font-black leading-none text-sky-400">{{ $badgeYears }}</span>
                                <span data-cms-field="badge_text" class="text-[9px] font-bold uppercase tracking-widest mt-1 text-slate-200">{{ $badgeText }}</span>
                            </div>

                            <!-- Clinic Photo Bottom Right -->
                            <div class="w-64 h-44 rounded-3xl overflow-hidden shadow-2xl border-4 border-white -mt-16 ml-auto relative z-10 bg-slate-100 hover:scale-105 transition-transform duration-300">
                                <img
                                    data-cms-field="image_2_url"
                                    src="{{ $storyImage2 }}"
                                    alt="{{ $storyTitle }}"
                                    class="w-full h-full object-cover"
                                    loading="eager"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Right: Story, Values and Support Cards -->
                    <div class="lg:w-1/2 flex flex-col items-start animate-fade-in-up">
                        <div class="flex items-center gap-2 text-sky-500 font-bold text-xs uppercase tracking-widest mb-3">
                            <span class="w-5 h-1 bg-sky-500 rounded-full"></span>
                            <span data-cms-field="eyebrow">{{ $storyEyebrow }}</span>
                        </div>

                        <h2 data-cms-field="title" class="text-4xl sm:text-5xl font-black text-slate-900 leading-tight mb-5">
                            {{ $storyTitle }}
                        </h2>

                        <p data-cms-field="description" class="text-slate-500 text-sm leading-relaxed mb-8">
                            {{ $storyDescription }}
                        </p>

                        <!-- Support Cards Grid -->
                        <div data-cms-group="story_features" class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full mb-8" @if(!$hasFeature1 && !$hasFeature2) style="display: none;" @endif>
                            <div data-cms-row="feature_1_title" class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-start gap-4 hover:border-sky-300 hover:shadow-md transition-all hover:-translate-y-1" @if(!$hasFeature1) style="display: none;" @endif>
                                <div class="p-3 rounded-xl bg-white text-sky-500 shadow-xs border border-slate-100 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 data-cms-field="feature_1_title" class="font-extrabold text-slate-900 text-sm mb-1">{{ $feature1Title }}</h4>
                                    <p data-cms-field="feature_1_desc" class="text-xs text-slate-500 leading-relaxed">{{ $feature1Desc }}</p>
                                </div>
                            </div>

                            <div data-cms-row="feature_2_title" class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-start gap-4 hover:border-sky-300 hover:shadow-md transition-all hover:-translate-y-1" @if(!$hasFeature2) style="display: none;" @endif>
                                <div class="p-3 rounded-xl bg-white text-sky-500 shadow-xs border border-slate-100 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 data-cms-field="feature_2_title" class="font-extrabold text-slate-900 text-sm mb-1">{{ $feature2Title }}</h4>
                                    <p data-cms-field="feature_2_desc" class="text-xs text-slate-500 leading-relaxed">{{ $feature2Desc }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Bullet Feature Checkmark (Ocultar si está vacío) -->
                        <div data-cms-row="check_text" class="flex items-center gap-3 text-xs text-slate-600 mb-8" @if(!$hasCheckText) style="display: none;" @endif>
                            <div class="w-5 h-5 rounded-full bg-[#13223f] text-white flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                ✓
                            </div>
                            <span data-cms-field="check_text">{{ $checkText }}</span>
                        </div>

                        <!-- CTA Button (Ocultar si está vacío) -->
                        <a data-cms-row="button_text" href="{{ $storyButtonUrl }}" class="bg-sky-500 hover:bg-sky-600 text-white px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all flex items-center gap-1.5 hover:scale-105" @if(!$hasStoryButton) style="display: none;" @endif>
                            <span data-cms-field="button_text">{{ $storyButtonText }}</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- SECTION 2: TRUSTBAR BOX                    -->
        <!-- ========================================== -->
        <section data-cms-section="about_trustbar" data-cms-name="Barra de Estadísticas y Confianza" class="bg-white pb-16" @if(!$aboutTrustbarActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Blue 4-Column Trustbar Box -->
                <div class="bg-[#13223f] text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-800 animate-scale-in">
                    <div data-cms-group="trustbar_stats" class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-slate-800" @if(!$hasStat1 && !$hasStat2 && !$hasStat3 && !$hasStat4) style="display: none;" @endif>
                        <div data-cms-row="stat_1_number" class="flex flex-col items-center p-3 group" @if(!$hasStat1) style="display: none;" @endif>
                            <div class="w-14 h-14 rounded-full bg-white/10 border border-white/10 text-white flex items-center justify-center mb-4 shadow-md group-hover:scale-110 group-hover:bg-sky-500 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-sky-400 group-hover:text-white transition-colors">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_1_number" class="text-3xl font-black text-white mb-1">{{ $stat1Number }}</h3>
                            <p data-cms-field="stat_1_label" class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ $stat1Label }}</p>
                        </div>

                        <div data-cms-row="stat_2_number" class="flex flex-col items-center p-3 pt-6 md:pt-3 group" @if(!$hasStat2) style="display: none;" @endif>
                            <div class="w-14 h-14 rounded-full bg-white/10 border border-white/10 text-white flex items-center justify-center mb-4 shadow-md group-hover:scale-110 group-hover:bg-sky-500 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-sky-400 group-hover:text-white transition-colors">
                                    <path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_2_number" class="text-3xl font-black text-white mb-1">{{ $stat2Number }}</h3>
                            <p data-cms-field="stat_2_label" class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ $stat2Label }}</p>
                        </div>

                        <div data-cms-row="stat_3_number" class="flex flex-col items-center p-3 pt-6 md:pt-3 group" @if(!$hasStat3) style="display: none;" @endif>
                            <div class="w-14 h-14 rounded-full bg-white/10 border border-white/10 text-white flex items-center justify-center mb-4 shadow-md group-hover:scale-110 group-hover:bg-sky-500 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-sky-400 group-hover:text-white transition-colors">
                                    <path d="M12 2v20"></path>
                                    <path d="m17 5-5-3-5 3"></path>
                                    <path d="m17 19-5 3-5-3"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_3_number" class="text-3xl font-black text-white mb-1">{{ $stat3Number }}</h3>
                            <p data-cms-field="stat_3_label" class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ $stat3Label }}</p>
                        </div>

                        <div data-cms-row="stat_4_number" class="flex flex-col items-center p-3 pt-6 md:pt-3 group" @if(!$hasStat4) style="display: none;" @endif>
                            <div class="w-14 h-14 rounded-full bg-white/10 border border-white/10 text-white flex items-center justify-center mb-4 shadow-md group-hover:scale-110 group-hover:bg-sky-500 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-sky-400 group-hover:text-white transition-colors">
                                    <circle cx="12" cy="8" r="6"></circle>
                                    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                                </svg>
                            </div>
                            <h3 data-cms-field="stat_4_number" class="text-3xl font-black text-white mb-1">{{ $stat4Number }}</h3>
                            <p data-cms-field="stat_4_label" class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ $stat4Label }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ========================================== -->
        <!-- SECTION 3: SPECIALISTS TEAM (FULL ROSTER)  -->
        <!-- ========================================== -->
        <section id="dentists" 
                 data-cms-section="about_specialists" 
                 data-cms-name="Equipo de Especialistas" 
                 class="py-20 bg-slate-50/80 border-t border-slate-200/80" 
                 x-data="{ isDentistModalOpen: false, activeDentist: null }"
                 @if(!$aboutSpecialistsActive && !$isEditor) style="display: none;" @endif>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-14 animate-fade-in-up">
                    <div data-cms-row="eyebrow" class="flex items-center justify-center gap-2 text-sky-600 font-bold text-xs uppercase tracking-widest mb-2.5" @if(!$hasSpecialistsEyebrow) style="display: none;" @endif>
                        <span class="w-5 h-0.5 bg-sky-500 rounded-full"></span>
                        <span data-cms-field="eyebrow">{{ $specialistsEyebrow }}</span>
                        <span class="w-5 h-0.5 bg-sky-500 rounded-full"></span>
                    </div>
                    <h2 data-cms-field="title" data-cms-row="title" class="text-3xl sm:text-4xl lg:text-[2.6rem] font-extrabold text-slate-900 tracking-tight leading-tight mb-3.5" @if(!$hasSpecialistsTitle) style="display: none;" @endif>
                        {{ $specialistsTitle }}
                    </h2>
                    <p data-cms-field="subtitle" data-cms-row="subtitle" class="text-slate-600 text-sm sm:text-base leading-relaxed" @if(!$hasSpecialistsSubtitle) style="display: none;" @endif>
                        {{ $specialistsSubtitle }}
                    </p>
                </div>

                @if($dentists->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                        @foreach($dentists as $dentist)
                            @php
                                $dentistData = [
                                    'id' => $dentist->id,
                                    'name' => $dentist->name,
                                    'initials' => $dentist->initials,
                                    'specialty' => $dentist->specialty,
                                    'photo_url' => $dentist->photo_url,
                                    'bio' => $dentist->bio,
                                    'services' => $dentist->services->map(fn($s) => ['name' => $s->name, 'slug' => $s->slug]),
                                ];
                            @endphp
                            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                                <div class="flex flex-col h-full">
                                    <!-- Doctor Image (1:1) -->
                                    <div 
                                        class="aspect-square w-full overflow-hidden relative bg-slate-100 cursor-pointer flex items-center justify-center select-none"
                                        @click="activeDentist = {{ Js::from($dentistData) }}; isDentistModalOpen = true"
                                    >
                                        @if($dentist->photo_url)
                                            <img src="{{ $dentist->photo_url }}" alt="{{ $dentist->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                                        @else
                                            <div class="w-full h-full bg-[#0EA5E9] text-[#0F172A] font-black text-6xl sm:text-7xl flex items-center justify-center tracking-tight group-hover:scale-105 transition-transform duration-500">
                                                {{ $dentist->initials }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-5 flex flex-col flex-1">
                                        <h3 
                                            class="text-lg font-bold text-slate-900 group-hover:text-sky-600 transition-colors cursor-pointer"
                                            @click="activeDentist = {{ Js::from($dentistData) }}; isDentistModalOpen = true"
                                        >
                                            {{ $dentist->name }}
                                        </h3>

                                        <!-- Specialty Text (sin cápsula) -->
                                        <p class="text-xs font-semibold text-sky-600 tracking-wide mt-0.5 mb-2.5">
                                            {{ $dentist->specialty }}
                                        </p>

                                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                            {{ Str::limit($dentist->bio, 110) }}
                                        </p>

                                        <!-- Saber Más Link -->
                                        <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <button 
                                                type="button" 
                                                @click="activeDentist = {{ Js::from($dentistData) }}; isDentistModalOpen = true"
                                                class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1 group/btn transition-colors"
                                            >
                                                <span>Saber más</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-3xl border border-slate-200 p-8">
                        <p class="text-slate-500 text-sm">Nuestro equipo de especialistas estará disponible muy pronto para agendar en línea.</p>
                    </div>
                @endif

            </div>

            <!-- SPECIALIST DETAILS MODAL -->
            <div 
                x-show="isDentistModalOpen" 
                x-cloak 
                class="fixed inset-0 z-50 overflow-y-auto"
                role="dialog" 
                aria-modal="true"
                @keydown.escape.window="isDentistModalOpen = false"
            >
                <!-- Backdrop -->
                <div 
                    x-show="isDentistModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                    @click="isDentistModalOpen = false"
                ></div>

                <!-- Modal Dialog -->
                <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
                    <div 
                        x-show="isDentistModalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        @click.stop
                        class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden z-10"
                    >
                        <!-- Profile Header Banner (#13223f) -->
                        <div class="h-28 sm:h-32 bg-gradient-to-r from-[#13223f] via-[#1a335e] to-[#13223f] relative overflow-hidden">
                            <!-- Subtle decorative glowing circles -->
                            <div class="absolute -top-10 -right-10 w-44 h-44 bg-sky-500/20 rounded-full blur-2xl pointer-events-none"></div>
                            <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-teal-500/20 rounded-full blur-2xl pointer-events-none"></div>

                            <!-- Close Button (inside banner) -->
                            <button 
                                type="button" 
                                @click="isDentistModalOpen = false" 
                                class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                                aria-label="Cerrar modal"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Profile Body -->
                        <div class="px-6 sm:px-8 pb-6" x-show="activeDentist">
                            <!-- Avatar + Identity Section (overlapping banner) -->
                            <div class="flex flex-col sm:flex-row items-center sm:items-end gap-5 -mt-14 sm:-mt-16 pb-5 border-b border-slate-100">
                                <!-- Doctor Photo (1:1) -->
                                <div class="w-28 h-28 sm:w-32 sm:h-32 aspect-square rounded-2xl overflow-hidden bg-white p-1 border-4 border-white shadow-xl flex-shrink-0 relative z-10 flex items-center justify-center">
                                    <template x-if="activeDentist?.photo_url">
                                        <img :src="activeDentist?.photo_url" :alt="activeDentist?.name" class="w-full h-full object-cover rounded-xl" />
                                    </template>
                                    <template x-if="!activeDentist?.photo_url">
                                        <div class="w-full h-full rounded-xl bg-[#0EA5E9] text-[#0F172A] font-black text-3xl sm:text-4xl flex items-center justify-center select-none shadow-inner">
                                            <span x-text="activeDentist?.initials"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Doctor Name & Specialty -->
                                <div class="text-center sm:text-left flex-1 sm:pb-1">
                                    <span class="text-xs font-bold text-sky-600 uppercase tracking-widest block" x-text="activeDentist?.specialty"></span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5 tracking-tight" x-text="activeDentist?.name"></h3>
                                </div>
                            </div>

                            <!-- Profile Sections -->
                            <div class="py-5 space-y-6">
                                <!-- Section: Descripción -->
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Descripción</h4>
                                    <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-100">
                                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line" x-text="activeDentist?.bio"></p>
                                    </div>
                                </div>

                                <!-- Section: Tratamientos que realiza -->
                                <template x-if="activeDentist?.services && activeDentist?.services.length > 0">
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">Tratamientos que realiza</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="service in activeDentist.services" :key="service.slug">
                                                <a :href="'/servicios/' + service.slug" class="group/item inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-sky-50 hover:text-sky-600 hover:border-sky-300 px-3.5 py-2 rounded-xl border border-slate-200/80 shadow-xs transition-all">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 group-hover/item:scale-125 transition-transform"></span>
                                                    <span x-text="service.name"></span>
                                                    <span class="text-slate-400 group-hover/item:text-sky-600 group-hover/item:translate-x-0.5 transition-all">»</span>
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer: Solo botón Cerrar -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                                <button 
                                    type="button" 
                                    @click="isDentistModalOpen = false" 
                                    class="px-6 py-2.5 rounded-full text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer"
                                >
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

    @if(request()->has('editor_preview'))
        @include('partials.editor-bridge')
    @endif

</body>
</html>
