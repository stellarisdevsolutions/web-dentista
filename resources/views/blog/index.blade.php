<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => __('Blog y Consejos Clínicos') . ' | ' . $clinic->clinic_name,
        'description' => __('Aprende sobre higiene oral, ortodoncia invisible, implantes y estética dental con los especialistas de :clinic.', ['clinic' => $clinic->clinic_name]),
    ])

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f0f7ff] text-slate-900 selection:bg-sky-500 selection:text-white" x-data="{ mobileMenuOpen: false, searchOpen: false, searchQuery: '', activeFilter: 'all' }">

    <!-- GLOBAL NAVBAR -->
    @include('partials.navbar')

    @php
        $sectionsMap = isset($sections) ? $sections : \App\Models\SiteSection::forPage('blog')->get()->keyBy('section_key');
        $blogHero = $sectionsMap->get('blog_hero')?->content ?? [];
        $blogHeroActive = $sectionsMap->get('blog_hero')?->is_active ?? true;
        $blogCta = $sectionsMap->get('blog_cta')?->content ?? [];
        $blogCtaActive = $sectionsMap->get('blog_cta')?->is_active ?? true;
        $isEditor = request()->has('editor_preview');

        // Hero Fields
        $heroEyebrow = $blogHero['eyebrow'] ?? null;
        if (!array_key_exists('eyebrow', $blogHero)) {
            $heroEyebrow = __('DIVULGACIÓN CIENTÍFICA & SALUD BUCAL');
        }

        $heroTitle = $blogHero['title'] ?? null;
        if (!array_key_exists('title', $blogHero)) {
            $heroTitle = __('Blog y Consejos Clínicos');
        }

        $heroSubtitle = $blogHero['subtitle'] ?? null;
        if (!array_key_exists('subtitle', $blogHero)) {
            $heroSubtitle = __('Artículos con base científica y recomendaciones prácticas de nuestros odontólogos para el cuidado diario de tu salud bucal.');
        }

        $hasHeroEyebrow = filled(trim($heroEyebrow ?? ''));
        $hasHeroTitle = filled(trim($heroTitle ?? ''));
        $hasHeroSubtitle = filled(trim($heroSubtitle ?? ''));

        // CTA Fields
        $ctaEyebrow = $blogCta['eyebrow'] ?? null;
        if (!array_key_exists('eyebrow', $blogCta)) {
            $ctaEyebrow = __('CONSULTA PERSONALIZADA');
        }

        $ctaTitle = $blogCta['title'] ?? null;
        if (!array_key_exists('title', $blogCta)) {
            $ctaTitle = __('¿Tienes dudas sobre algún síntoma o tratamiento dental?');
        }

        $ctaSubtitle = $blogCta['subtitle'] ?? null;
        if (!array_key_exists('subtitle', $blogCta)) {
            $ctaSubtitle = __('Nuestros especialistas certificados están disponibles para una evaluación presencial o responder a tus inquietudes de inmediato.');
        }

        $ctaPrimaryText = $blogCta['button_primary_text'] ?? null;
        $ctaPrimaryUrl = $blogCta['button_primary_url'] ?? route('schedule');
        if (!array_key_exists('button_primary_text', $blogCta)) {
            $ctaPrimaryText = __('Reservar Turno Online »');
        }

        $ctaSecondaryText = $blogCta['button_secondary_text'] ?? null;
        $ctaSecondaryUrl = $blogCta['button_secondary_url'] ?? route('contact');
        if (!array_key_exists('button_secondary_text', $blogCta)) {
            $ctaSecondaryText = __('Contactar »');
        }

        $hasCtaEyebrow = filled(trim($ctaEyebrow ?? ''));
        $hasCtaTitle = filled(trim($ctaTitle ?? ''));
        $hasCtaSubtitle = filled(trim($ctaSubtitle ?? ''));
        $hasCtaPrimaryBtn = filled(trim($ctaPrimaryText ?? ''));
        $hasCtaSecondaryBtn = filled(trim($ctaSecondaryText ?? ''));
    @endphp

    <main class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section data-cms-section="blog_hero" 
                 data-cms-name="Portada del Blog (Hero)" 
                 class="bg-[#13223f] text-white py-14 px-4 sm:px-6 lg:px-8 relative overflow-hidden" 
                 @if(!$blogHeroActive && !$isEditor) style="display: none;" @endif>
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 left-0 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
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
        <!-- CATALOG / ARTICLES SECTION                 -->
        <!-- ========================================== -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Category Filter Pills -->
                @if($categories->count() > 0)
                    <div class="flex flex-wrap justify-center items-center gap-2.5 mb-12">
                        <button
                            type="button"
                            @click="activeFilter = 'all'"
                            :class="activeFilter === 'all' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/30' : 'bg-white text-slate-700 hover:border-sky-300 border border-slate-200'"
                            class="px-5 py-2.5 rounded-full text-xs font-bold transition-all"
                        >
                            {{ __('Todos los Artículos') }} ({{ $posts->count() }})
                        </button>
                        @foreach($categories as $cat)
                            <button
                                type="button"
                                @click="activeFilter = '{{ addslashes($cat) }}'"
                                :class="activeFilter === '{{ addslashes($cat) }}' ? 'bg-sky-500 text-white shadow-md shadow-sky-500/30' : 'bg-white text-slate-700 hover:border-sky-300 border border-slate-200'"
                                class="px-5 py-2.5 rounded-full text-xs font-bold transition-all"
                            >
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Articles Grid -->
                @if($posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                        @foreach($posts as $index => $post)
                            <article 
                                x-show="activeFilter === 'all' || activeFilter === '{{ addslashes($post->category) }}'"
                                x-transition
                                class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs hover:shadow-xl transition-all duration-300 relative flex flex-col justify-between group hover:-translate-y-1.5"
                            >
                                <div>
                                    <!-- Image (clean, without overlay badges or numbers) -->
                                    <div class="h-52 w-full rounded-2xl overflow-hidden mb-4 bg-slate-100">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="block w-full h-full">
                                            @if($post->cover_image)
                                                <img 
                                                    src="{{ Str::startsWith($post->cover_image, ['http://', 'https://']) ? $post->cover_image : asset('storage/' . $post->cover_image) }}" 
                                                    alt="{{ __('images.blog_cover', ['title' => $post->title]) }}" 
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                    loading="lazy"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-xs uppercase\'>{{ strtoupper(substr($clinic->clinic_name, 0, 4)) }}</div>';"
                                                />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-xs tracking-widest uppercase">
                                                    {{ $clinic->clinic_name }}
                                                </div>
                                            @endif
                                        </a>
                                    </div>

                                    <!-- Category & Metadata Row (no capsule, update date with icon) -->
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <span class="text-sky-600 font-bold uppercase tracking-wider text-[11px]">
                                            {{ $post->category ?? __('Salud Dental') }}
                                        </span>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium">
                                            <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ ($post->updated_at ?? $post->published_at)?->translatedFormat('d M, Y') ?? __('Reciente') }}</span>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-lg font-black text-slate-900 group-hover:text-sky-600 transition-colors leading-snug mb-2.5 line-clamp-2">
                                        <a href="{{ route('blog.show', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>

                                    <!-- Excerpt -->
                                    <p class="text-slate-500 text-xs leading-relaxed mb-6 font-normal line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                </div>

                                <!-- Card Bottom -->
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <a 
                                        href="{{ route('blog.show', $post->slug) }}" 
                                        class="text-xs font-extrabold text-sky-600 hover:text-sky-700 flex items-center gap-1 group/btn transition-colors"
                                    >
                                        <span>{{ __('Leer artículo') }}</span>
                                        <span class="group-hover/btn:translate-x-1 transition-transform">»</span>
                                    </a>

                                    <a 
                                        href="{{ route('schedule') }}"
                                        class="px-3.5 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-500 text-sky-700 hover:text-white font-bold text-xs uppercase tracking-wider transition-all"
                                    >
                                        {{ __('Agendar Cita') }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-200/80 p-8 max-w-xl mx-auto mb-16 shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">{{ __('Próximamente más artículos') }}</h3>
                        <p class="text-slate-500 text-xs">{{ __('Estamos preparando contenido educativo y guías clínicas para compartir contigo en breve.') }}</p>
                    </div>
                @endif

                <!-- Bottom CTA Card -->
                <div data-cms-section="blog_cta" 
                     data-cms-name="Banner Consulta Personalizada" 
                     class="bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-3xl p-8 sm:p-12 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8"
                     @if(!$blogCtaActive && !$isEditor) style="display: none;" @endif>
                    <div class="max-w-xl text-center md:text-left">
                        <span data-cms-field="eyebrow" data-cms-row="eyebrow" class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-widest mb-3" @if(!$hasCtaEyebrow) style="display: none;" @endif>
                            {{ $ctaEyebrow }}
                        </span>
                        <h3 data-cms-field="title" data-cms-row="title" class="text-2xl sm:text-3xl font-black leading-tight" @if(!$hasCtaTitle) style="display: none;" @endif>
                            {{ $ctaTitle }}
                        </h3>
                        <p data-cms-field="subtitle" data-cms-row="subtitle" class="text-sky-100 text-xs sm:text-sm mt-2 font-light leading-relaxed" @if(!$hasCtaSubtitle) style="display: none;" @endif>
                            {{ $ctaSubtitle }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <a 
                            data-cms-field="button_primary_text"
                            data-cms-row="button_primary_text"
                            href="{{ $ctaPrimaryUrl }}"
                            class="px-8 py-4 rounded-full bg-white text-sky-600 hover:bg-sky-50 font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 active:scale-95 transition-all text-center flex-shrink-0"
                            @if(!$hasCtaPrimaryBtn) style="display: none;" @endif
                        >
                            {{ $ctaPrimaryText }}
                        </a>
                        <a 
                            data-cms-field="button_secondary_text"
                            data-cms-row="button_secondary_text"
                            href="{{ $ctaSecondaryUrl }}"
                            class="px-6 py-4 rounded-full bg-sky-700/60 hover:bg-sky-700/80 text-white font-bold text-xs uppercase tracking-wider border border-white/20 transition-all text-center flex-shrink-0"
                            @if(!$hasCtaSecondaryBtn) style="display: none;" @endif
                        >
                            {{ $ctaSecondaryText }}
                        </a>
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
