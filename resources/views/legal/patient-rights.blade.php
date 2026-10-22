<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => __('Derechos del Paciente') . ' | ' . $clinic->clinic_name,
        'description' => __('Conoce la carta de derechos y deberes del paciente odontológico en :clinic. Calidad asistencial, consentimiento informado y ética médica.', ['clinic' => $clinic->clinic_name]),
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

    <main class="min-h-screen bg-slate-50 text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section class="bg-[#13223f] text-white py-14 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="max-w-5xl mx-auto text-center relative z-10">
                <!-- Breadcrumb -->
                <nav class="flex items-center justify-center gap-2 text-xs text-slate-400 mb-4 font-medium" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-sky-400 transition-colors">{{ __('Inicio') }}</a>
                    <span class="text-slate-600">/</span>
                    <span class="text-slate-400">{{ __('Marco Legal') }}</span>
                    <span class="text-slate-600">/</span>
                    <span class="text-sky-400 font-bold">{{ __('Derechos del Paciente') }}</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 text-xs font-bold uppercase tracking-wider mb-4 border border-sky-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                    </svg>
                    <span>{{ __('Calidad Asistencial y Ética Médica') }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                    {{ __('Carta de Derechos y Deberes del Paciente') }}
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-2xl mx-auto mt-4 font-light leading-relaxed">
                    {{ __('Nuestro compromiso fundamental en :clinic es garantizar una atención odontológica ética, transparente, humanizada y de excelencia clínica.', ['clinic' => $clinic->clinic_name]) }}
                </p>

                <p class="text-xs text-sky-300/80 mt-4 font-medium">
                    {{ __('Estándar de Calidad y Bioética Odontológica') }}
                </p>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- LEGAL NAVIGATION TABS                      -->
        <!-- ========================================== -->
        <section class="bg-white border-b border-slate-200 py-4 px-4 sticky top-16 z-30 shadow-sm backdrop-blur-md bg-white/95">
            <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                <a href="{{ route('privacy') }}" class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all {{ request()->routeIs('privacy') ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    {{ __('Política de Privacidad') }}
                </a>
                <a href="{{ route('terms') }}" class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all {{ request()->routeIs('terms') ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    {{ __('Términos del Servicio') }}
                </a>
                <a href="{{ route('patient-rights') }}" class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all {{ request()->routeIs('patient-rights') ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    {{ __('Derechos del Paciente') }}
                </a>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- CONTENT BODY                               -->
        <!-- ========================================== -->
        <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto space-y-10">

                <!-- INTRO BOX -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                        {{ __('La relación entre el paciente y el equipo de profesionales de :clinic se basa en la confianza mutua, el respeto y la colaboración activa. Para velar por tu bienestar, ponemos a tu disposición los derechos que te asisten como paciente y los deberes necesarios para garantizar el éxito de tus tratamientos odontológicos.', ['clinic' => $clinic->clinic_name]) }}
                    </p>
                </div>

                <!-- PART 1: PATIENT RIGHTS -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-500 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0">I</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900">{{ __('Tus Derechos como Paciente') }}</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 pt-2">
                        <!-- Right 1 -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">{{ __('1. Trato Digno y Respetuoso') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Recibir una atención cordial, empática, considerada y totalmente libre de discriminación por motivos de género, raza, creencias o condición socioeconómica.') }}
                            </p>
                        </div>

                        <!-- Right 2 -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">{{ __('2. Información Clara y Veraz') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Conocer en lenguaje claro y comprensible el diagnóstico de tu salud dental, alternativas de tratamiento, riesgos potenciales, tiempos estimados y presupuesto por escrito antes de comenzar.') }}
                            </p>
                        </div>

                        <!-- Right 3 -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <path d="m9 15 2 2 4-4"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">{{ __('3. Consentimiento Informado Libre') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Otorgar o revocar libremente tu consentimiento previo a cualquier procedimiento clínico invasivo, quirúrgico o protésico tras recibir explicaciones completas de tu odontólogo.') }}
                            </p>
                        </div>

                        <!-- Right 4 -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">{{ __('4. Confidencialidad y Privacidad') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Garantía de estricta reserva sobre tu historial clínico, diagnósticos, imágenes intraorales y cualquier dato confidencial compartido con el equipo odontológico.') }}
                            </p>
                        </div>

                        <!-- Right 5 -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">{{ __('5. Alivio Oportuno del Dolor') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Recibir atención prioritaria en caso de dolor agudo, infección o urgencia dental, empleando técnicas de anestesia moderna y protocolos mínimamente invasivos.') }}
                            </p>
                        </div>

                        <!-- Right 6 -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900">{{ __('6. Acceso al Historial e Informes') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Solicitar copia de tus radiografías, informes médicos y plan de tratamiento si decides consultar una segunda opinión médica o trasladar tu atención.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- PART 2: PATIENT RESPONSIBILITIES -->
                <div class="space-y-4 pt-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-800 text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0">II</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900">{{ __('Tus Deberes y Responsabilidades') }}</h2>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                        <ul class="text-xs sm:text-sm text-slate-700 space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <strong class="font-bold text-slate-900 block text-xs sm:text-sm mb-0.5">{{ __('Veracidad en la Información Médica:') }}</strong>
                                    <span class="text-slate-600">{{ __('Informar con total honestidad sobre alergias a medicamentos o anestésicos, enfermedades preexistentes (diabetes, hipertensión, cardiopatías), tratamientos médicos en curso o estado de embarazo.') }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <strong class="font-bold text-slate-900 block text-xs sm:text-sm mb-0.5">{{ __('Cumplimiento de Indicaciones Postoperatorias:') }}</strong>
                                    <span class="text-slate-600">{{ __('Seguir estrictamente las pautas de medicación, higiene y cuidados orales indicados por el especialista tras cirugías, endodoncias o colocación de implantes.') }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <strong class="font-bold text-slate-900 block text-xs sm:text-sm mb-0.5">{{ __('Puntualidad y Aviso Oportuno de Cancelación:') }}</strong>
                                    <span class="text-slate-600">{{ __('Acudir puntualmente a las citas agendadas y avisar con al menos 24 horas de anticipación en caso de requerir cancelación o cambio de horario.') }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <strong class="font-bold text-slate-900 block text-xs sm:text-sm mb-0.5">{{ __('Trato Respetuoso y Convivencia:') }}</strong>
                                    <span class="text-slate-600">{{ __('Mantener un trato cortés y respetuoso hacia los odontólogos, higienistas, personal de recepción y demás pacientes de la clínica.') }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <strong class="font-bold text-slate-900 block text-xs sm:text-sm mb-0.5">{{ __('Compromiso Financiero:') }}</strong>
                                    <span class="text-slate-600">{{ __('Cumplir con los pagos y modalidades convenidas para los tratamientos ejecutados según el presupuesto previamente aceptado.') }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Callout Box -->
                <article class="bg-gradient-to-br from-slate-900 to-[#13223f] text-white rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-lg sm:text-xl font-black text-white">{{ __('¿Deseas Plantear una Sugerencia o Felicitación?') }}</h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        {{ __('En :clinic nos esforzamos por la excelencia continua. La opinión de nuestros pacientes es vital para mantener nuestros altos estándares clínicos y humanos.', ['clinic' => $clinic->clinic_name]) }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm">
                            <span>{{ __('Contactar a Dirección Clínica »') }}</span>
                        </a>
                        <a href="{{ route('schedule') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition-colors border border-white/20">
                            <span>{{ __('Agendar Cita Médica') }}</span>
                        </a>
                    </div>
                </article>

            </div>
        </section>

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

</body>
</html>
