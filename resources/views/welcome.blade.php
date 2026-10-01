<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $clinic->clinic_name }} | Odontología Avanzada y Cuidado Dental</title>
    <meta name="description" content="{{ $clinic->tagline ?? 'Cuidado dental suave, integral e indoloro para toda la familia.' }}">

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

    <main class="min-h-screen bg-white text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- 3. HERO SECTION ASYMMETRIC (Vercel Style)   -->
        <!-- ========================================== -->
        <section id="inicio" class="relative bg-white pt-2 pb-10 overflow-hidden">
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
                            <span>Tu Sonrisa Merece la Mejor Atención</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[1.1] mb-5 tracking-tight">
                            Odontología Suave, Sin Dolor para Toda tu Familia.
                        </h1>

                        <!-- Description -->
                        <p class="text-slate-300 text-sm leading-relaxed mb-8 font-light max-w-lg">
                            {{ $clinic->tagline ?? 'Combinamos escáner digital 3D y tecnología láser avanzada con un trato cálido y humano para devolverte una sonrisa radiante y saludable.' }}
                        </p>

                        <!-- Key Benefits / Bullet Checkmarks -->
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center gap-3 text-xs sm:text-sm font-bold text-slate-200 hover:translate-x-1 transition-transform">
                                <div class="w-5 h-5 rounded-full bg-sky-500 text-white flex items-center justify-center text-xs flex-shrink-0">✓</div>
                                <span>Odontólogos y Cirujanos Especialistas Certificados</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs sm:text-sm font-bold text-slate-200 hover:translate-x-1 transition-transform">
                                <div class="w-5 h-5 rounded-full bg-sky-500 text-white flex items-center justify-center text-xs flex-shrink-0">✓</div>
                                <span>Tratamientos Láser 100% Indoloros y Sedación Consciente</span>
                            </div>
                        </div>

                        <!-- CTA Actions -->
                        <div class="flex flex-wrap items-center gap-5">
                            <a href="#appointment-section" class="bg-sky-500 hover:bg-sky-600 text-white px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-lg shadow-sky-500/30 flex items-center gap-1 hover:scale-105 active:scale-95 hover:shadow-sky-500/50">
                                <span>Agendar Consulta Online »</span>
                            </a>
                            <a href="#how-it-works" class="flex items-center gap-3 text-white font-bold text-xs uppercase tracking-wider group hover:text-sky-300 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 fill-current ml-0.5">
                                        <path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path>
                                    </svg>
                                </div>
                                <span class="underline underline-offset-4">Cómo Trabajamos</span>
                            </a>
                        </div>
                    </div>

                    <!-- Floating Circular Badge on Seam: 24 Horas Soporte -->
                    <div class="absolute right-0 lg:-right-12 top-1/2 -translate-y-1/2 z-30 hidden sm:flex animate-float">
                        <div class="w-24 h-24 rounded-full bg-sky-500 text-white border-4 border-white shadow-xl flex flex-col items-center justify-center text-center p-2 hover:scale-110 transition-transform cursor-default">
                            <span class="text-sm font-black leading-none">24 Horas</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-1">Urgencias</span>
                        </div>
                    </div>
                </div>

                <!-- Right Side High-Res Clinical Photography -->
                <div class="lg:w-5/12 relative min-h-[380px] lg:min-h-full bg-slate-100 overflow-hidden">
                    <img src="https://masalaserclinic.com/wp-content/uploads/elementor/thumbs/dentist-making-professional-teeth-cleaning-withb-cotton-female-young-patient-dental-office-scaled-rmzddojmvr5b2ndthwo8x3mhck1ct16aflega90jqw.jpg" alt="Odontología moderna en Aura Dental Studio" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" loading="eager" />
                </div>
            </div>

            <!-- Overlapping Quick Contact Cards (-mt-10) -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-30 animate-scale-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Emergency Call Card -->
                    <div class="bg-white p-7 rounded-3xl shadow-xl border border-slate-100 flex items-center gap-6 hover:-translate-y-1.5 transition-all duration-300 hover:shadow-2xl">
                        <div class="w-16 h-16 rounded-2xl bg-[#13223f] text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-sky-400">
                                <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 mb-1">Llamada de Urgencia</h3>
                            <p class="text-slate-500 text-xs mb-2">Atención dental prioritaria el mismo día</p>
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
                            <h3 class="text-xl font-black text-slate-900 mb-1">Contacto Directo</h3>
                            <p class="text-slate-500 text-xs mb-2">Escríbenos para presupuestos o dudas clínicas</p>
                            <a href="mailto:{{ $clinic->email }}" class="text-sky-500 font-extrabold text-sm hover:underline">
                                {{ $clinic->email }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 4. ABOUT OUR CLINIC SECTION                 -->
        <!-- ========================================== -->
        <section id="about" class="pt-6 pb-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-14 mb-12">
                    
                    <!-- Left Photo Composition with Circular Badges -->
                    <div class="lg:w-1/2 relative w-full flex justify-center">
                        <div class="relative w-full max-w-[440px]">
                            <!-- Animated Ring -->
                            <div class="absolute -top-6 -left-6 w-24 h-24 border-8 border-sky-400 rounded-full border-r-transparent border-b-transparent -z-10 animate-float"></div>
                            
                            <!-- Primary Doctor Photo -->
                            <div class="rounded-[40px] overflow-hidden shadow-2xl border-4 border-white bg-slate-100 hover:shadow-sky-500/20 transition-all duration-500">
                                <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=800&q=80" alt="Especialista Odontóloga en Consulta" class="w-full h-[350px] object-cover transition-transform duration-700 hover:scale-105" loading="eager" />
                            </div>

                            <!-- Floating Circular 20+ Years Exp Badge -->
                            <div class="absolute top-1/2 left-1/3 -translate-x-1/2 -translate-y-1/2 w-28 h-28 rounded-full bg-[#13223f] text-white border-4 border-white shadow-2xl flex flex-col items-center justify-center text-center p-2 z-20 hover:scale-110 transition-transform duration-300">
                                <span class="text-xl font-black leading-none">20+</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest mt-1">Años Exp.</span>
                            </div>

                            <!-- Secondary Overlapping Clinical Photo -->
                            <div class="w-64 h-44 rounded-3xl overflow-hidden shadow-2xl border-4 border-white -mt-16 ml-auto relative z-10 bg-slate-100 hover:scale-105 transition-transform duration-300">
                                <img src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=600&q=80" alt="Procedimiento Odontológico Seguro" class="w-full h-full object-cover" loading="lazy" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Narrative & Benefit Cards -->
                    <div class="lg:w-1/2 flex flex-col items-start">
                        <div class="flex items-center gap-2 text-sky-500 font-bold text-xs uppercase tracking-widest mb-3">
                            <span class="w-5 h-1 bg-sky-500 rounded-full"></span>
                            <span>SOBRE NUESTRA CLÍNICA</span>
                        </div>

                        <h2 class="text-4xl sm:text-5xl font-black text-slate-900 leading-tight mb-5">
                            Más de 20 Años Creando Sonrisas Saludables y Confiadas.
                        </h2>

                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            {{ $clinic->clinic_name }} fue fundada con una visión clara: brindar odontología integral de máxima precisión combinada con un entorno cálido, humano y libre de ansiedad. Tratamos a cada paciente como a nuestra propia familia.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full mb-6">
                            <!-- Card 1 -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-start gap-4 hover:border-sky-300 transition-colors hover:shadow-md">
                                <div class="p-3 rounded-xl bg-white text-sky-500 shadow-xs border border-slate-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm mb-0.5">Atención Sin Temor</h4>
                                    <p class="text-xs text-slate-400">Técnicas suaves para pacientes con fobia y niños.</p>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-start gap-4 hover:border-sky-300 transition-colors hover:shadow-md">
                                <div class="p-3 rounded-xl bg-white text-sky-500 shadow-xs border border-slate-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm mb-0.5">Especialistas Top</h4>
                                    <p class="text-xs text-slate-400">Doctores acreditados en estética, implantes y ortodoncia.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-slate-600 mb-6">
                            <div class="w-5 h-5 rounded-full bg-[#13223f] text-white flex items-center justify-center text-[10px] font-bold flex-shrink-0">✓</div>
                            <span>Horarios organizados por <strong>Turno Mañana</strong> y <strong>Turno Tarde</strong> para tu comodidad.</span>
                        </div>

                        <a href="#dentists" class="bg-sky-500 hover:bg-sky-600 text-white px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all flex items-center gap-1 hover:scale-105 hover:shadow-sky-500/40">
                            <span>Conocer a los Especialistas »</span>
                        </a>
                    </div>
                </div>

                <!-- Dark Metrics & Stats Bar (#0c1830) -->
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
                            <h3 class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">25,000+</h3>
                            <p class="text-xs text-sky-400 font-bold uppercase tracking-wider">Pacientes Felices</p>
                        </div>

                        <div class="flex flex-col items-center p-3 pt-6 md:pt-3 group">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 shadow-lg shadow-sky-500/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                                    <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">24/7</h3>
                            <p class="text-xs text-sky-400 font-bold uppercase tracking-wider">Soporte Urgencias</p>
                        </div>

                        <div class="flex flex-col items-center p-3 pt-6 md:pt-3 group">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-400 border border-sky-500/20 shadow-lg shadow-sky-500/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                                    <path d="M5 21v-6"></path>
                                    <path d="M12 21V3"></path>
                                    <path d="M19 21V9"></path>
                                </svg>
                            </div>
                            <h3 class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">18,500+</h3>
                            <p class="text-xs text-sky-400 font-bold uppercase tracking-wider">Sonrisas Renovadas</p>
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
                            <h3 class="text-3xl sm:text-4xl font-black text-white mb-1 tracking-tight">15+</h3>
                            <p class="text-xs text-sky-400 font-bold uppercase tracking-wider">Premios y Avales</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ========================================== -->
        <!-- 5. OUR DENTAL SOLUTIONS (How It Works)      -->
        <!-- ========================================== -->
        <section id="how-it-works" class="py-12 bg-slate-50/90 border-t border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-14">
                    
                    <!-- Left Solutions Features -->
                    <div class="lg:w-1/2 flex flex-col items-start">
                        <div class="flex items-center gap-2 text-sky-500 font-bold text-xs uppercase tracking-widest mb-3">
                            <span class="w-5 h-1 bg-sky-500 rounded-full"></span>
                            <span>NUESTRAS SOLUCIONES DENTALES</span>
                        </div>

                        <h2 class="text-4xl sm:text-5xl font-black text-slate-900 leading-tight mb-5">
                            Soluciones Dentales Completas con la Mayor Suavidad.
                        </h2>

                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Desde limpiezas preventivas profundas hasta carillas de porcelana ultra finas e implantes guiados en 3D, cuidamos tu salud bucal con precisión quirúrgica y confort total.
                        </p>

                        <div class="space-y-3.5 w-full mb-4">
                            <!-- Feature 1 -->
                            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1">
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                </div>
                                <span class="font-bold text-slate-800 text-sm">Higiene y Prevención Dental Integral con Ultrasonido Tibio</span>
                            </div>

                            <!-- Feature 2 -->
                            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1">
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                                    </svg>
                                </div>
                                <span class="font-bold text-slate-800 text-sm">Especialistas Certificados en Estética y Cirugía Reconstructiva</span>
                            </div>

                            <!-- Feature 3 -->
                            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1">
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>
                                        <path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"></path>
                                    </svg>
                                </div>
                                <span class="font-bold text-slate-800 text-sm">Protocolos Estrictos de Esterilización Quirúrgica Grado Hospital</span>
                            </div>

                            <!-- Feature 4 -->
                            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all hover:translate-x-1">
                                <div class="w-9 h-9 rounded-xl bg-[#13223f] text-white flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-sky-400">
                                        <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path>
                                        <circle cx="12" cy="8" r="6"></circle>
                                    </svg>
                                </div>
                                <span class="font-bold text-slate-800 text-sm">Odontología Láser Indolora y Sedación Consciente</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Double Image Composition with Sparkle -->
                    <div class="lg:w-1/2 relative w-full flex justify-center">
                        <div class="relative w-full max-w-[460px]">
                            <div class="absolute -top-6 -right-6 text-sky-400 text-5xl z-0 animate-pulse">✦</div>
                            
                            <div class="rounded-[40px] overflow-hidden shadow-2xl border-4 border-white relative z-10 bg-slate-100">
                                <img src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=800&q=80" alt="Especialista revisando caso clínico" class="w-full h-[370px] object-cover hover:scale-105 transition-transform duration-500" loading="lazy" />
                            </div>

                            <div class="w-52 h-52 rounded-[35px] overflow-hidden shadow-2xl border-4 border-white absolute -bottom-8 -right-4 z-20 bg-slate-100 hover:scale-105 transition-transform duration-300">
                                <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80" alt="Examen dental preventivo" class="w-full h-full object-cover" loading="lazy" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 6. PATIENT TESTIMONIALS SPOTLIGHT CARD      -->
        <!-- ========================================== -->
        @if($testimonials->count() > 0)
            <section id="testimonials" class="py-12 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="rounded-[40px] overflow-hidden shadow-2xl border border-slate-200/80 grid grid-cols-1 lg:grid-cols-2">
                        
                        <!-- Left Big Image -->
                        <div class="h-72 lg:h-full relative bg-slate-100">
                            <img src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1000&q=80" alt="Doctor explicando escaneo 3D" class="w-full h-full object-cover min-h-[320px]" loading="lazy" />
                        </div>

                        <!-- Right Testimonial Navy Content -->
                        @php $featuredTestimonial = $testimonials->first(); @endphp
                        <div class="bg-[#13223f] text-white p-8 md:p-12 relative flex flex-col justify-between">
                            <div class="absolute inset-0 bg-[radial-gradient(#0ea5e9_1px,transparent_1px)] [background-size:16px_16px] opacity-10 pointer-events-none"></div>

                            <div class="relative z-10">
                                <div class="flex items-center gap-2 text-sky-400 font-bold text-xs uppercase tracking-widest mb-3">
                                    <span class="w-5 h-1 bg-sky-400 rounded-full"></span>
                                    <span>TESTIMONIOS DE PACIENTES</span>
                                </div>

                                <h3 class="text-3xl font-black text-white leading-tight mb-4">
                                    Lo que Dicen Nuestros Pacientes sobre su Experiencia Dental.
                                </h3>

                                <span class="text-xs font-bold text-sky-400 uppercase tracking-widest block mb-4">
                                    Opinión Verificada de Google & WhatsApp
                                </span>

                                <p class="text-slate-300 text-sm leading-relaxed italic mb-8 font-light">
                                    &quot;{{ $featuredTestimonial->comment ?? 'Tenía mucho miedo a los procedimientos dentales, pero en la clínica hicieron que todo fuera 100% indoloro. ¡Mi nueva sonrisa luce radiante y natural!' }}&quot;
                                </p>

                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-sky-500 text-white font-black text-base flex items-center justify-center border-2 border-sky-400 shadow-md">
                                        {{ substr($featuredTestimonial->patient_name ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-white text-base">{{ $featuredTestimonial->patient_name ?? 'Paciente Verificado' }}</h4>
                                        <p class="text-xs text-sky-400 font-semibold">{{ $featuredTestimonial->treatment ?? 'Tratamiento Integral' }}</p>
                                    </div>
                                </div>

                                <!-- 5 Stars -->
                                <div class="flex text-amber-400 mt-4 gap-1">
                                    @for($i = 0; $i < ($featuredTestimonial->rating ?? 5); $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" class="w-4 h-4 fill-current">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            <!-- Indicator Pills -->
                            <div class="flex items-center gap-2 mt-8 relative z-10">
                                <span class="w-8 h-2 rounded-full bg-sky-500"></span>
                                <span class="w-2.5 h-2 rounded-full bg-slate-700"></span>
                                <span class="w-2.5 h-2 rounded-full bg-slate-700"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        @endif

        <!-- ========================================== -->
        <!-- 7. COMPREHENSIVE TREATMENTS CATALOG        -->
        <!-- ========================================== -->
        <section id="services" class="py-14 bg-slate-50/90 border-t border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-sky-500 font-bold text-xs tracking-widest uppercase mb-2 block">Nuestra Oferta Clínica</span>
                    <h2 class="text-4xl sm:text-5xl font-black text-slate-900 mb-4">Tratamientos Odontológicos Especializados</h2>
                    <p class="text-slate-500 text-sm">Cuidado dental de alta precisión con cero dolor y tecnología digital 3D.</p>
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
                                        alt="{{ $service->name }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy"
                                    />
                                </div>

                                <div class="p-6">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-2.5 py-0.5 rounded-full inline-block mb-2.5">
                                        {{ $service->category }}
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
                                    Ver detalles
                                </a>

                                <a 
                                    href="{{ route('schedule') }}?service_id={{ $service->id }}"
                                    class="text-xs font-extrabold text-sky-500 group-hover:text-sky-600 uppercase tracking-wider flex items-center gap-1.5 transition-all text-left"
                                >
                                    <span>Agendar »</span>
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
                            <span>Ver todos los tratamientos ({{ $services->count() }}) »</span>
                        </a>
                    </div>
                @endif

            </div>
        </section>

        <!-- ========================================== -->
        <!-- 8. PREGUNTAS FRECUENTES (FAQS)             -->
        <!-- ========================================== -->
        @if($faqs->count() > 0)
            <section id="faqs" class="py-16 bg-slate-50/70 border-t border-slate-200/80">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-xl mx-auto mb-12">
                        <span class="text-sky-500 font-bold text-xs tracking-widest uppercase mb-2 block">Dudas Comunes</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">Preguntas Frecuentes</h2>
                        <p class="text-slate-500 text-xs">Todo lo que necesitas saber antes de tu primera consulta odontológica.</p>
                    </div>

                    <div class="space-y-3" x-data="{ activeFaq: null }">
                        @foreach($faqs as $faq)
                            <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs transition-colors">
                                <button 
                                    type="button" 
                                    @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})" 
                                    class="w-full p-5 text-left flex justify-between items-center font-bold text-slate-900 text-sm hover:text-sky-600 transition-colors"
                                >
                                    <span>{{ $faq->question }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-sky-500 transform transition-transform duration-200" :class="activeFaq === {{ $faq->id }} ? 'rotate-180' : ''">
                                        <path d="m6 9 6 6 6-6"></path>
                                    </svg>
                                </button>
                                <div x-show="activeFaq === {{ $faq->id }}" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- ========================================== -->
        <!-- 9. CONTACT & APPOINTMENT BOOKING           -->
        <!-- ========================================== -->
        <section id="appointment-section" class="py-16 bg-white border-t border-slate-200/80">
            <div id="booking" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-14">
                    
                    <!-- Left Column: Minimalist Dark Navy Card (#13223f) matching emergency right column -->
                    <div class="lg:w-1/2 w-full flex justify-center">
                        <div class="bg-[#13223f] text-white rounded-[40px] p-8 sm:p-10 w-full max-w-[480px] flex flex-col justify-between relative border border-slate-800 shadow-2xl overflow-hidden hover:shadow-sky-500/10 transition-all duration-300">
                            <!-- Glow accent -->
                            <div class="absolute -top-16 -right-16 w-56 h-56 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="relative z-10">
                                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-300 font-bold text-xs uppercase tracking-widest mb-5">
                                    <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                                    <span>Reserva Online 24/7</span>
                                </div>

                                <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-3 tracking-tight">
                                    Reserva tu Cita <span class="text-sky-400">Odontológica</span> Online.
                                </h2>

                                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed mb-8 font-light">
                                    Selecciona el tratamiento que necesitas y reserva tu turno en el día y horario que mejor se adapte a tu rutina.
                                </p>

                            </div>

                            <!-- CTA Action -->
                            <div class="relative z-10 w-full">
                                <a 
                                    href="{{ route('schedule') }}"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white font-bold py-4 px-8 rounded-2xl shadow-lg shadow-sky-500/25 hover:scale-[1.02] active:scale-95 transition-all text-xs uppercase tracking-wider text-center"
                                >
                                    <span>Reservar Cita Online »</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Female Dentist Cutout & Dark Navy Emergency Card (Matching Mockup) -->
                    <div class="lg:w-1/2 w-full flex justify-center">
                        <div class="bg-sky-50/70 rounded-[40px] p-8 pb-10 w-full max-w-[440px] flex flex-col items-center text-center relative border border-sky-100 hover:shadow-xl transition-all duration-300">
                            
                            <!-- Dentist Image Cutout -->
                            <div class="w-56 h-56 rounded-full overflow-hidden border-4 border-white shadow-xl mb-6 bg-slate-200 hover:scale-105 transition-transform duration-300">
                                <img
                                    src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=600&q=80"
                                    alt="Doctora Especialista Odontológica"
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
                                <div class="text-left">
                                    <h4 class="font-extrabold text-white text-base">Llamada de Urgencia 24/7</h4>
                                    <p class="text-[11px] text-slate-300">Atención el mismo día para dolor agudo</p>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic->emergency_phone ?? $clinic->phone) }}" class="text-sky-400 font-bold text-xs hover:underline">
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

</body>
</html>