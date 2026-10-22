<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => $service->name . ' | ' . $clinic->clinic_name,
        'description' => $service->short_description ?? Str::limit(strip_tags($service->description), 150),
        'ogImage' => $service->image_asset_url ?? $clinic->logo_asset_url,
        'service' => $service,
    ])

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

<main class="min-h-screen bg-white text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- BREADCRUMBS & BANNER                       -->
        <!-- ========================================== -->
        <section class="bg-[#13223f] text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10">
                <!-- Breadcrumbs -->
                <nav class="flex items-center space-x-2 text-xs text-sky-400 font-semibold mb-4">
                    <a href="{{ route('home') }}" class="hover:underline">{{ __('Inicio') }}</a>
                    <span>/</span>
                    <a href="{{ route('services.index') }}" class="hover:underline">{{ __('Tratamientos') }}</a>
                    <span>/</span>
                    <span class="text-slate-300">{{ $service->name }}</span>
                </nav>

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 font-bold text-xs uppercase tracking-widest mb-3 border border-sky-400/30">
                            {{ $service->category }}
                        </span>
                        <h1 class="text-3xl sm:text-5xl font-black text-white leading-tight">
                            {{ $service->name }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-xs p-4 rounded-2xl border border-white/15 self-start lg:self-auto">
                        <div>
                            <span class="block text-[11px] text-slate-300 uppercase tracking-wider font-bold">{{ __('Inversión Estimada') }}</span>
                            <span class="text-2xl font-black text-white">
                                {{ $service->price ? '$'.number_format($service->price, 0) : __('Previa Valoración') }}
                            </span>
                        </div>
                        <div class="h-10 w-px bg-white/20"></div>
                        <div>
                            <span class="block text-[11px] text-slate-300 uppercase tracking-wider font-bold">{{ __('Duración') }}</span>
                            <span class="text-sm font-black text-sky-400">
                                {{ $service->duration_minutes ?? 45 }} {{ __('Minutos') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- DETAIL CONTENT & SIDEBAR GRID              -->
        <!-- ========================================== -->
        <section class="py-16 bg-slate-50/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    
                    <!-- Left Column (8 cols): Clinical Details -->
                    <div class="lg:col-span-8 space-y-10">
                        
                        <!-- Description Card -->
                        <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm md:p-8 lg:p-10">
                            <!-- Treatment Featured Image (flush on mobile, framed with margins and rounded on desktop) -->
                            <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100 md:rounded-2xl md:shadow-xs">
                                <img 
                                    src="{{ $service->image_url }}" 
                                    alt="{{ __('images.service_detail', ['service' => $service->name]) }}" 
                                    class="w-full h-full object-cover"
                                />
                            </div>

                            <div class="p-6 sm:p-8 md:p-0 md:pt-8 space-y-6">
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1">{{ __('DESCRIPCIÓN CLÍNICA') }}</span>
                                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900">{{ __('En qué consiste este tratamiento') }}</h2>
                                    </div>

                                    <div class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal whitespace-pre-line">{{ trim($service->description ?? $service->short_description) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Specialist Dentists -->
                        @if($service->dentists->count() > 0)
                            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-sm space-y-6">
                                <div>
                                    <span class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1">{{ __('EQUIPO MÉDICO') }}</span>
                                    <h2 class="text-2xl font-black text-slate-900">{{ __('Especialistas para este Tratamiento') }}</h2>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    @foreach($service->dentists as $doc)
                                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center gap-4">
                                            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-[#0EA5E9] text-[#0F172A] border-2 border-white shadow-md flex-shrink-0 flex items-center justify-center select-none">
                                                @if($doc->photo_url)
                                                    <img 
                                                        src="{{ $doc->photo_url }}" 
                                                        alt="{{ __('images.service_dentist', ['name' => $doc->name, 'service' => $service->name]) }}" 
                                                        class="w-full h-full object-cover"
                                                    />
                                                @else
                                                    <span class="font-extrabold text-xl">{{ $doc->initials }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-extrabold text-slate-900 text-sm">{{ $doc->name }}</h4>
                                                <span class="text-sky-600 font-bold text-[11px] block mt-0.5">{{ $doc->specialty }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Right Column (4 cols): Sticky Booking Sidebar -->
                    <div class="lg:col-span-4 space-y-6 sticky top-28">
                        
                        <!-- Main Booking Action Box -->
                        <div class="bg-[#13223f] text-white p-8 rounded-3xl shadow-2xl border border-slate-800 space-y-6">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-sky-500/20 text-sky-400 text-[10px] font-extrabold uppercase tracking-wider mb-2">
                                    {{ __('ONLINE 24/7') }}
                                </span>
                                <h3 class="text-2xl font-black text-white">{{ __('¿Deseas agendar este tratamiento?') }}</h3>
                                <p class="text-xs text-slate-300 mt-2 font-light leading-relaxed">
                                    {{ __('Reserva tu turno de forma inmediata en 3 pasos con confirmación directa por WhatsApp.') }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 text-xs">
                                <div class="flex justify-between items-center text-slate-300">
                                    <span>{{ __('Tiempo estimado') }}:</span>
                                    <span class="font-bold text-white">{{ $service->duration_minutes ?? 45 }} {{ __('min') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-300">
                                    <span>{{ __('Costo estimado') }}:</span>
                                    <span class="font-bold text-sky-400">{{ $service->price ? '$'.number_format($service->price, 0) : __('Previa Valoración') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-300">
                                    <span>{{ __('Modalidad') }}:</span>
                                    <span class="font-bold text-emerald-400">{{ __('Atención Presencial') }}</span>
                                </div>
                            </div>

                            <a 
                                href="{{ route('schedule', ['service_id' => $service->id]) }}"
                                class="w-full bg-sky-500 hover:bg-sky-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-wider shadow-xl shadow-sky-500/30 transition-all flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 text-center"
                            >
                                <span>{{ __('Agendar Cita para este Servicio »') }}</span>
                            </a>
                        </div>

                        <!-- Emergency Call Box -->
                        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-6 text-rose-900 space-y-2">
                            <div class="flex items-center gap-2 font-black text-xs uppercase tracking-wider text-rose-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-rose-600 shrink-0">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                                <span>{{ __('¿Dolor agudo o urgencia?') }}</span>
                            </div>
                            <p class="text-xs text-rose-800">
                                {{ __('Contamos con espacios de atención inmediata el mismo día. Llama a nuestra línea prioritaria:') }}
                            </p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic->emergency_phone ?? $clinic->phone) }}" class="font-black text-sm text-rose-600 hover:underline block pt-1">
                                {{ $clinic->emergency_phone ?? $clinic->phone }}
                            </a>
                        </div>

                        <!-- Other Treatments Navigation -->
                        @if(isset($relatedServices) && $relatedServices->count() > 0)
                            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">
                                    {{ __('Otros Tratamientos') }}
                                </h4>
                                <div class="space-y-2">
                                    @foreach($relatedServices as $rel)
                                        <a 
                                            href="{{ route('services.show', $rel->slug) }}" 
                                            class="block p-3 rounded-xl hover:bg-slate-50 transition-colors border border-slate-100 group"
                                        >
                                            <p class="text-xs font-bold text-slate-800 group-hover:text-sky-600 transition-colors">{{ $rel->name }}</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $rel->category }} • {{ $rel->price ? '$'.number_format($rel->price, 0) : __('Cotizar') }}</p>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                </div>

            </div>
        </section>

        

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

</body>
</html>
