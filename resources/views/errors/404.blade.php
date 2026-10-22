<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @php
        $errorClinic = $clinic ?? \App\Models\ClinicSetting::first() ?? new \App\Models\ClinicSetting([
            'clinic_name' => 'Aura Dental Studio',
            'phone' => '+1 (555) 234-5678',
            'emergency_phone' => '+1 (555) 911-DENT',
            'email' => 'hola@auradental.com',
        ]);
    @endphp

    @include('partials.seo-meta', [
        'clinic' => $errorClinic,
        'title' => __('Página No Encontrada (404)') . ' | ' . $errorClinic->clinic_name,
        'description' => __('La página que buscas no existe o ha sido movida en :clinic.', ['clinic' => $errorClinic->clinic_name]),
        'robots' => 'noindex, nofollow',
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
    @include('partials.navbar', ['clinic' => $errorClinic])

    <main class="min-h-screen bg-gradient-to-b from-[#f0f7ff] via-white to-slate-50 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- 404 HERO CONTENT                           -->
        <!-- ========================================== -->
        <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 relative flex-grow flex items-center">
            
            <!-- Soft Decorative Ambient Glows -->
            <div class="absolute top-10 left-1/2 -translate-x-1/2 w-96 h-96 bg-sky-200/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-blue-100/50 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-4xl mx-auto text-center relative z-10 w-full">

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-100/80 border border-sky-200 text-sky-800 text-xs font-black uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <span>{{ __('Error 404 • Página No Encontrada') }}</span>
                </div>

                <!-- Stylized 404 Numeric with Dental Icon -->
                <div class="flex items-center justify-center gap-3 sm:gap-6 my-4 select-none">
                    <span class="text-7xl sm:text-9xl font-black text-[#13223f] tracking-tighter drop-shadow-sm">4</span>
                    
                    <!-- Dental Stethoscope / Tooth Mirror Diagnostic Graphic -->
                    <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-3xl bg-gradient-to-br from-sky-500 to-[#13223f] text-white flex items-center justify-center shadow-xl shadow-sky-500/20 transform hover:scale-105 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12 sm:w-16 sm:h-16 text-sky-100">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            <path d="M11 8v6"></path>
                            <path d="M8 11h6"></path>
                        </svg>
                    </div>

                    <span class="text-7xl sm:text-9xl font-black text-[#13223f] tracking-tighter drop-shadow-sm">4</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight mt-6">
                    {{ __('Parece que esta página no existe o ha cambiado de lugar') }}
                </h1>

                <!-- Subtitle -->
                <p class="text-slate-600 text-xs sm:text-base max-w-xl mx-auto mt-4 font-normal leading-relaxed">
                    {{ __('La dirección web a la que intentas acceder no está disponible o el enlace ingresado es incorrecto. Te ayudamos a retomar tu camino hacia una sonrisa saludable.') }}
                </p>

                <!-- Primary Action Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 mt-8">
                    <a 
                        href="{{ route('home') }}" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-sky-500/25 hover:shadow-lg active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span>{{ __('Volver al Inicio') }}</span>
                    </a>

                    <a 
                        href="{{ route('schedule') }}" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-[#13223f] hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md hover:shadow-lg active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                            <path d="m9 16 2 2 4-4"></path>
                        </svg>
                        <span>{{ __('Agendar Cita Médica') }}</span>
                    </a>

                    <a 
                        href="{{ route('services.index') }}" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider transition-all border border-slate-200 shadow-sm hover:border-slate-300 active:scale-95"
                    >
                        <span>{{ __('Explorar Tratamientos »') }}</span>
                    </a>
                </div>

                <!-- Helpful Links Section -->
                <div class="mt-14 pt-10 border-t border-slate-200/80 text-left">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest text-center mb-6">
                        {{ __('Otras Secciones Útiles de la Clínica') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Card 1: Treatments -->
                        <a href="{{ route('services.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-sky-300 hover:shadow-md transition-all group flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-500 group-hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-xs font-black text-slate-900 group-hover:text-sky-600 transition-colors">{{ __('Tratamientos') }}</h2>
                                <p class="text-[11px] text-slate-500 leading-tight">{{ __('Implantes, carillas, ortodoncia y diseño de sonrisa.') }}</p>
                            </div>
                        </a>

                        <!-- Card 2: Doctors & About -->
                        <a href="{{ route('about') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-sky-300 hover:shadow-md transition-all group flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-500 group-hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-xs font-black text-slate-900 group-hover:text-sky-600 transition-colors">{{ __('Especialistas') }}</h2>
                                <p class="text-[11px] text-slate-500 leading-tight">{{ __('Conoce a nuestro equipo médico odontológico colegiado.') }}</p>
                            </div>
                        </a>

                        <!-- Card 3: Blog -->
                        <a href="{{ route('blog.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-sky-300 hover:shadow-md transition-all group flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-500 group-hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                                    <path d="M6 6h10"></path>
                                    <path d="M6 10h10"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-xs font-black text-slate-900 group-hover:text-sky-600 transition-colors">{{ __('Blog Dental') }}</h2>
                                <p class="text-[11px] text-slate-500 leading-tight">{{ __('Consejos de salud bucodental y guías de cuidado oral.') }}</p>
                            </div>
                        </a>

                        <!-- Card 4: Contact -->
                        <a href="{{ route('contact') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-sky-300 hover:shadow-md transition-all group flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-500 group-hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-xs font-black text-slate-900 group-hover:text-sky-600 transition-colors">{{ __('Contacto') }}</h2>
                                <p class="text-[11px] text-slate-500 leading-tight">{{ __('Ubicación, horarios de atención y consultas generales.') }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Emergency Banner Callout -->
                <div class="mt-8 p-5 sm:p-6 rounded-2xl bg-rose-50 border border-rose-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-left">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-rose-950 uppercase tracking-wider">{{ __('¿Tienes una Urgencia Odontológica?') }}</p>
                            <p class="text-xs text-rose-900/80">{{ __('Atendemos dolor agudo o traumatismos con prioridad clínica inmediata.') }}</p>
                        </div>
                    </div>

                    <a 
                        href="tel:{{ preg_replace('/[^0-9+]/', '', $errorClinic->emergency_phone ?? $errorClinic->phone) }}" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-wider transition-colors shadow-sm flex-shrink-0"
                    >
                        <span>{{ __('Llamar a Urgencias: :phone', ['phone' => $errorClinic->emergency_phone ?? $errorClinic->phone]) }}</span>
                    </a>
                </div>

            </div>
        </section>

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer', ['clinic' => $errorClinic])

</body>
</html>
