<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => $post->title . ' | Blog ' . $clinic->clinic_name,
        'description' => Str::limit(strip_tags($post->excerpt ?? $post->content), 150),
        'ogType' => 'article',
        'ogImage' => $post->image_asset_url ?? $clinic->logo_asset_url,
        'post' => $post,
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

    <!-- GLOBAL NAVBAR -->
    @include('partials.navbar')

    <main class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section class="bg-[#13223f] text-white py-12 sm:py-16 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 left-0 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-4xl mx-auto relative z-10">
                <!-- Breadcrumbs -->
                <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6 font-medium">
                    <a href="{{ route('home') }}" class="hover:text-sky-400 transition-colors">{{ __('Inicio') }}</a>
                    <span>/</span>
                    <a href="{{ route('blog.index') }}" class="hover:text-sky-400 transition-colors">{{ __('Blog') }}</a>
                    <span>/</span>
                    <span class="text-slate-300 font-bold truncate max-w-xs">{{ $post->title }}</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    <span>{{ $post->category ?? __('Salud Dental') }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight mb-6">
                    {{ $post->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300 pt-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="font-bold text-white">{{ __('Equipo Odontológico de :clinic', ['clinic' => $clinic->clinic_name]) }}</span>
                    </div>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ ($post->updated_at ?? $post->published_at)?->translatedFormat('d \d\e F, Y') ?? __('Reciente') }}
                    </span>
                    <span>•</span>
                    <span class="text-teal-400 font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        {{ __('Revisión médica certificada') }}
                    </span>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- ARTICLE CONTENT & SIDEBAR SECTION          -->
        <!-- ========================================== -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    
                    <!-- Main Article Body Column -->
                    <article class="lg:col-span-8 space-y-8">
                        <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm md:p-8 lg:p-10">
                            <!-- Featured Cover Image (flush on mobile, framed with margins and rounded on desktop like treatments) -->
                            @if($post->cover_image)
                                <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100 md:rounded-2xl md:shadow-xs">
                                    <img 
                                        src="{{ Str::startsWith($post->cover_image, ['http://', 'https://']) ? $post->cover_image : asset('storage/' . $post->cover_image) }}" 
                                        alt="{{ __('images.blog_detail', ['title' => $post->title]) }}" 
                                        class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-xs uppercase tracking-widest\'>{{ strtoupper(substr($clinic->clinic_name, 0, 4)) }}</div>';"
                                    />
                                </div>
                            @endif

                            <div class="p-6 sm:p-8 md:p-0 md:pt-8 space-y-8">
                                <!-- Lead Excerpt Callout -->
                                @if($post->excerpt)
                                    <div class="p-6 rounded-2xl bg-sky-50 border-l-4 border-sky-500 text-slate-700 font-medium text-sm sm:text-base leading-relaxed">
                                        {{ $post->excerpt }}
                                    </div>
                                @endif

                                <!-- Body Content (Rendered Quill RichText HTML) -->
                                <div class="blog-content leading-relaxed text-sm sm:text-base font-normal">
                                    {!! $post->content !!}
                                </div>

                                <!-- Author & Clinical Disclaimer Box -->
                                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-sky-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-sky-500/20">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs text-slate-600 leading-relaxed text-center sm:text-left">
                                        <p class="font-bold text-slate-900 text-sm mb-1">{{ __('Nota médica informativa') }}</p>
                                        <p>{{ __('Este contenido tiene fines puramente educativos e informativos con base odontológica. No reemplaza el diagnóstico presencial o tratamiento médico indicado por un odontólogo colegiado.') }}</p>
                                    </div>
                                </div>

                                <!-- Navigation Controls -->
                                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <a 
                                        href="{{ route('blog.index') }}" 
                                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700 hover:text-sky-600 transition-colors"
                                    >
                                        <span>{{ __('« Volver al Blog') }}</span>
                                    </a>

                                    <a 
                                        href="{{ route('schedule') }}" 
                                        class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm hover:scale-105 transition-all"
                                    >
                                        <span>{{ __('Agendar Consulta »') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Sidebar Column -->
                    <aside class="lg:col-span-4 space-y-8">
                        
                        <!-- Quick Booking Card -->
                        <div class="p-7 rounded-3xl bg-[#13223f] text-white shadow-xl space-y-5 border border-slate-800 relative overflow-hidden">
                            <div class="absolute -top-12 -right-12 w-40 h-40 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>

                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 text-[10px] font-black uppercase tracking-wider">
                                <span>{{ __('Reserva Inmediata') }}</span>
                            </div>

                            <h3 class="text-xl font-black">{{ __('Agenda tu Chequeo Dental') }}</h3>

                            <p class="text-slate-300 text-xs leading-relaxed font-light">
                                {{ __('Elige tu especialista y confirma tu cita en 3 sencillos pasos con recordatorio automático a tu WhatsApp.') }}
                            </p>

                            <a 
                                href="{{ route('schedule') }}" 
                                class="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-sky-500/25 hover:scale-102"
                            >
                                {{ __('Reservar Cita Online »') }}
                            </a>
                        </div>

                        <!-- Related Articles -->
                        @if($relatedPosts->count() > 0)
                            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs space-y-4">
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">
                                    {{ __('Artículos Relacionados') }}
                                </h4>
                                <div class="space-y-4">
                                    @foreach($relatedPosts as $rel)
                                        <a href="{{ route('blog.show', $rel->slug) }}" class="block group">
                                            <p class="text-xs font-bold text-slate-800 group-hover:text-sky-600 transition-colors leading-snug">
                                                {{ $rel->title }}
                                            </p>
                                            <span class="text-[10px] text-slate-400 font-medium inline-flex items-center gap-1 mt-1">
                                                <svg class="w-3 h-3 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span>{{ ($rel->updated_at ?? $rel->published_at)?->format('d/m/Y') ?? __('Reciente') }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Clinic Contact Details Card -->
                        <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs space-y-3 text-xs">
                            <h4 class="font-bold text-slate-900 uppercase tracking-wider text-xs">{{ __('Atención al Paciente') }}</h4>
                            <p class="text-slate-500 leading-relaxed">{{ __('¿Deseas consultar con un asesor o agendar por vía directa?') }}</p>
                            <div class="space-y-2.5 pt-3 border-t border-slate-100">
                                <p class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-sky-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    <span class="font-bold text-slate-700">{{ __('Teléfono') }}:</span> {{ $clinic->phone }}
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <span class="font-bold text-slate-700">{{ __('Urgencias') }}:</span> {{ $clinic->emergency_phone ?? $clinic->phone }}
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="font-bold text-slate-700">{{ __('Ubicación') }}:</span> {{ $clinic->address }}
                                </p>
                            </div>
                        </div>

                    </aside>

                </div>



                <!-- Bottom CTA Card -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-3xl p-8 sm:p-12 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="max-w-xl text-center md:text-left">
                        <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-widest mb-3">
                            {{ __('VALORACIÓN CLÍNICA INICIAL') }}
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-black leading-tight">
                            {{ __('¿Necesitas una opinión profesional sobre tu salud dental?') }}
                        </h3>
                        <p class="text-sky-100 text-xs sm:text-sm mt-2 font-light">
                            {{ __('Agenda una consulta diagnóstica presencial con escaneo digital en 3D para evaluar la salud de tu sonrisa.') }}
                        </p>
                    </div>

                    <a 
                        href="{{ route('schedule') }}"
                        class="px-8 py-4 rounded-full bg-white text-sky-600 hover:bg-sky-50 font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 active:scale-95 transition-all text-center flex-shrink-0"
                    >
                        {{ __('Agendar Valoración Ahora »') }}
                    </a>
                </div>

            </div>
        </section>

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

</body>
</html>
