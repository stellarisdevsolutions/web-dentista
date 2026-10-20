<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    <title>Blog y Consejos Clínicos | {{ $clinic->clinic_name }}</title>
    <meta name="description" content="Aprende sobre higiene oral, ortodoncia invisible, implantes y estética dental con los especialistas de {{ $clinic->clinic_name }}.">

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

    <main class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section class="bg-[#13223f] text-white py-14 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 left-0 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto text-center relative z-10">
                <span class="text-sky-400 font-bold text-xs uppercase tracking-widest block mb-2">DIVULGACIÓN CIENTÍFICA & SALUD BUCAL</span>
                <h1 class="text-4xl sm:text-5xl font-black text-white">Blog y Consejos Clínicos</h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto mt-3 font-light leading-relaxed">
                    Artículos con base científica y recomendaciones prácticas de nuestros odontólogos para el cuidado diario de tu salud bucal.
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
                            Todos los Artículos ({{ $posts->count() }})
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
                                                    alt="{{ $post->title }}" 
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                    loading="lazy"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-4xl\'>🦷</div>';"
                                                />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-4xl">
                                                    🦷
                                                </div>
                                            @endif
                                        </a>
                                    </div>

                                    <!-- Category & Metadata Row (no capsule, update date with icon) -->
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <span class="text-sky-600 font-bold uppercase tracking-wider text-[11px]">
                                            {{ $post->category ?? 'Salud Dental' }}
                                        </span>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium">
                                            <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ ($post->updated_at ?? $post->published_at)?->translatedFormat('d M, Y') ?? 'Reciente' }}</span>
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
                                        <span>Leer artículo</span>
                                        <span class="group-hover/btn:translate-x-1 transition-transform">»</span>
                                    </a>

                                    <a 
                                        href="{{ route('schedule') }}"
                                        class="px-3.5 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-500 text-sky-700 hover:text-white font-bold text-xs uppercase tracking-wider transition-all"
                                    >
                                        Agendar Cita
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-200/80 p-8 max-w-xl mx-auto mb-16 shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center mx-auto mb-4 text-2xl font-black">
                            📝
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Próximamente más artículos</h3>
                        <p class="text-slate-500 text-xs">Estamos preparando contenido educativo y guías clínicas para compartir contigo en breve.</p>
                    </div>
                @endif



                <!-- Bottom CTA Card -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 text-white rounded-3xl p-8 sm:p-12 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="max-w-xl text-center md:text-left">
                        <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-widest mb-3">
                            CONSULTA PERSONALIZADA
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-black leading-tight">
                            ¿Tienes dudas sobre algún síntoma o tratamiento dental?
                        </h3>
                        <p class="text-sky-100 text-xs sm:text-sm mt-2 font-light">
                            Nuestros especialistas certificados están disponibles para una evaluación presencial o responder a tus inquietudes de inmediato.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <a 
                            href="{{ route('schedule') }}"
                            class="px-8 py-4 rounded-full bg-white text-sky-600 hover:bg-sky-50 font-black text-xs uppercase tracking-wider shadow-lg hover:scale-105 active:scale-95 transition-all text-center flex-shrink-0"
                        >
                            Reservar Turno Online »
                        </a>
                        <a 
                            href="{{ route('contact') }}"
                            class="px-6 py-4 rounded-full bg-sky-700/60 hover:bg-sky-700/80 text-white font-bold text-xs uppercase tracking-wider border border-white/20 transition-all text-center flex-shrink-0"
                        >
                            Contactar »
                        </a>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

</body>
</html>
