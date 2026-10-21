<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    <title>Tratamientos y Procedimientos Odontológicos | {{ $clinic->clinic_name }}</title>
    <meta name="description" content="Conoce nuestro catálogo completo de tratamientos dentales: limpieza profunda, blanqueamiento láser, implantes guiados 3D, carillas y ortodoncia invisible.">

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f0f7ff] text-slate-900 selection:bg-sky-500 selection:text-white" x-data="{ mobileMenuOpen: false, searchOpen: false, searchQuery: '' }">

    <!-- GLOBAL NAVBAR -->
    @include('partials.navbar')

    @php
        $sectionsMap = isset($sections) ? $sections : \App\Models\SiteSection::forPage('services')->get()->keyBy('section_key');
        $servicesHero = $sectionsMap->get('services_hero')?->content ?? [];
        $servicesHeroActive = $sectionsMap->get('services_hero')?->is_active ?? true;
        $isEditor = request()->has('editor_preview');

        $heroEyebrow = $servicesHero['eyebrow'] ?? null;
        if (!array_key_exists('eyebrow', $servicesHero)) {
            $heroEyebrow = 'EXCELENCIA CLÍNICA';
        }

        $heroTitle = $servicesHero['title'] ?? null;
        if (!array_key_exists('title', $servicesHero)) {
            $heroTitle = 'Nuestros Tratamientos y Procedimientos';
        }

        $heroSubtitle = $servicesHero['subtitle'] ?? null;
        if (!array_key_exists('subtitle', $servicesHero)) {
            $heroSubtitle = 'Atención odontológica integral, moderna y sin dolor respaldada por tecnología intraoral 3D y materiales de estándar suizo.';
        }

        $hasHeroEyebrow = filled(trim($heroEyebrow ?? ''));
        $hasHeroTitle = filled(trim($heroTitle ?? ''));
        $hasHeroSubtitle = filled(trim($heroSubtitle ?? ''));
    @endphp

<main class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section data-cms-section="services_hero" 
                 data-cms-name="Portada de Servicios (Hero)" 
                 class="bg-[#13223f] text-white py-14 px-4 sm:px-6 lg:px-8 relative overflow-hidden" 
                 @if(!$servicesHeroActive && !$isEditor) style="display: none;" @endif>
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="max-w-7xl mx-auto text-center relative z-10">
                <span data-cms-field="eyebrow" data-cms-row="eyebrow" class="text-sky-400 font-bold text-xs uppercase tracking-widest block mb-2" @if(!$hasHeroEyebrow) style="display: none;" @endif>
                    {{ $heroEyebrow }}
                </span>
                <h1 data-cms-field="title" data-cms-row="title" class="text-4xl sm:text-5xl font-black text-white" @if(!$hasHeroTitle) style="display: none;" @endif>
                    {{ $heroTitle }}
                </h1>
                <p data-cms-field="subtitle" data-cms-row="subtitle" class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto mt-3 font-light leading-relaxed" @if(!$hasHeroSubtitle) style="display: none;" @endif>
                    {{ $heroSubtitle }}
                </p>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- CATALOG SECTION                            -->
        <!-- ========================================== -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    @foreach($services as $index => $service)
                        <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 relative flex flex-col justify-between group hover:-translate-y-1.5">
                            <!-- Clickable Card Body linking to details -->
                            <a href="{{ route('services.show', $service->slug) }}" class="block focus:outline-none flex-1">
                                <!-- Treatment Image Header -->
                                <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                    <img 
                                        src="{{ $service->image_url }}" 
                                        alt="{{ $service->name }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy"
                                    />
                                </div>

                                <div class="p-6">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2.5 py-0.5 rounded-full inline-block mb-2.5">
                                        {{ $service->category }}
                                    </span>

                                    <h3 class="text-lg font-bold text-slate-900 mb-2.5 group-hover:text-sky-600 transition-colors">
                                        {{ $service->name }}
                                    </h3>

                                    <p class="text-slate-500 text-xs leading-relaxed mb-6 font-normal">
                                        {{ $service->short_description ?? Str::limit($service->description, 130) }}
                                    </p>
                                </div>
                            </a>

                            <div class="p-6 pt-0 flex items-center justify-between border-t border-slate-100/80 mt-2">
                                <a 
                                    href="{{ route('services.show', $service->slug) }}"
                                    class="text-xs font-extrabold text-slate-700 hover:text-sky-600 transition-colors flex items-center gap-1"
                                >
                                    <span>Ver detalles »</span>
                                </a>

                                <a 
                                    href="{{ route('schedule', ['service_id' => $service->id]) }}"
                                    class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider shadow-sm hover:scale-105 transition-all"
                                >
                                    Agendar Cita
                                </a>
                            </div>
                        </div>
                    @endforeach
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
