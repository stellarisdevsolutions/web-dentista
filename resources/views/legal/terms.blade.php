<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => __('Términos del Servicio') . ' | ' . $clinic->clinic_name,
        'description' => __('Términos y condiciones de uso del portal web, sistema de citas y servicios odontológicos en :clinic.', ['clinic' => $clinic->clinic_name]),
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
                    <span class="text-sky-400 font-bold">{{ __('Términos del Servicio') }}</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 text-xs font-bold uppercase tracking-wider mb-4 border border-sky-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>{{ __('Condiciones de Uso y Reserva Clínica') }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                    {{ __('Términos del Servicio') }}
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-2xl mx-auto mt-4 font-light leading-relaxed">
                    {{ __('Por favor lee detenidamente las condiciones que regulan el uso de nuestro portal web, el sistema de reserva de citas y las pautas asistenciales en :clinic.', ['clinic' => $clinic->clinic_name]) }}
                </p>

                <p class="text-xs text-sky-300/80 mt-4 font-medium">
                    {{ __('Última actualización:') }} {{ date('F Y') }}
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
            <div class="max-w-4xl mx-auto space-y-8">

                <!-- Alert Box: Medical Disclaimer -->
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 sm:p-7 flex flex-col sm:flex-row items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="space-y-1.5">
                        <h2 class="text-sm sm:text-base font-extrabold text-amber-950">
                            {{ __('Aviso Médico Importante: Finalidad Informativa') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-amber-900/80 leading-relaxed">
                            {{ __('La información publicada en este sitio web (descripción de procedimientos, tarifas aproximadas, artículos del blog y preguntas frecuentes) tiene un propósito exclusivamente educativo e informativo. No constituye un diagnóstico médico vinculante ni sustituye la exploración presencial de un odontólogo calificado.') }}
                        </p>
                    </div>
                </div>

                <!-- Section 1: Acceptance -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">01</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Aceptación y Ámbito de Aplicación') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Al acceder y utilizar este sitio web, así como al solicitar turnos a través de nuestra plataforma de reservas en línea, el usuario acepta de manera expresa e irrevocable cumplir con los presentes Términos del Servicio y con la Política de Privacidad de :clinic.', ['clinic' => $clinic->clinic_name]) }}
                    </p>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Si no estás de acuerdo con cualquiera de estas disposiciones, te recomendamos abstenerte de utilizar las funciones de agendamiento digital y contactarnos por vía telefónica o presencial.') }}
                    </p>
                </article>

                <!-- Section 2: No Doctor-Patient Relationship -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">02</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Relación Asistencial y Consulta Profesional') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('El envío de un formulario de contacto o la solicitud provisional de una cita médica a través del sitio web no establece de forma automática una relación odontólogo-paciente vinculante.') }}
                    </p>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Dicha relación profesional sanitaria se perfecciona únicamente en el momento en que el paciente acude a nuestras instalaciones, es recibido por el equipo clínico, se abre formalmente su expediente y se realiza la valoración diagnóstica personalizada.') }}
                    </p>
                </article>

                <!-- Section 3: Scheduling & 24h Notice -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">03</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Políticas de Reserva, Cancelación y Puntualidad') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Para asegurar una atención de máxima calidad y respetar el tiempo de nuestros profesionales y pacientes, se aplican las siguientes pautas:') }}
                    </p>
                    <div class="space-y-3 pt-1">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Aviso Previo de Cancelación (24 Horas):') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Si necesitas cancelar o reprogramar una cita programada, te solicitamos avisar con al menos 24 horas de anticipación. Esto permite que ese turno pueda ser otorgado a un paciente con dolor agudo o tratamiento prioritario.') }}
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Tolerancia de Puntualidad (15 Minutos):') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Recomendamos llegar 10 minutos antes de tu turno asignado. Pasados 15 minutos de demora no justificada, la clínica podrá reagendar la cita para evitar retrasos en las atenciones de los pacientes subsecuentes.') }}
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Confirmación de Turno:') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Las citas agendadas por la web reciben un contacto de confirmación por WhatsApp o llamada telefónica por parte de nuestro equipo de recepción.') }}
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Section 4: Dental Emergencies Protocol -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">04</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Protocolo de Emergencias Odontológicas') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('El sistema de agendamiento online no está diseñado para emergencias de riesgo vital o dolor agudo insoportable que requieran intervención inmediata.') }}
                    </p>
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 space-y-2">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-rose-600">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider text-rose-900">{{ __('Atención de Urgencias 24/7:') }}</span>
                        </div>
                        <p class="text-xs text-rose-900/90 leading-relaxed">
                            {{ __('En caso de dolor agudo, hemorragia tras una extracción o traumatismo dental con fractura, comunícate directamente con nuestra línea de urgencias al :phone o acude a nuestro centro de atención.', ['phone' => $clinic->emergency_phone ?? $clinic->phone]) }}
                        </p>
                    </div>
                </article>

                <!-- Section 5: Treatment Plans & Pricing -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">05</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Presupuestos, Precios y Planes de Tratamiento') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Los valores, promociones o costos base mostrados en la web o comunicados telefónicamente son estimaciones orientativas. Cada boca y estructura ósea es única.') }}
                    </p>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('El presupuesto médico definitivo, el número de sesiones requeridas y los materiales específicos se determinarán tras una evaluación clínica presencial y los estudios radiográficos necesarios. Todo tratamiento iniciará únicamente tras la entrega y aprobación de dicho presupuesto.') }}
                    </p>
                </article>

                <!-- Section 6: Intellectual Property -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">06</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Propiedad Intelectual y Uso Permitido') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Todos los contenidos del portal (textos clínicos, fotografías de casos de sonrisa, logotipos, elementos gráficos y software) son propiedad exclusiva de :clinic o cuentan con licencia de uso. Queda prohibida su reproducción, copia o distribución con fines comerciales sin autorización expresa por escrito.', ['clinic' => $clinic->clinic_name]) }}
                    </p>
                </article>

                <!-- Section 7: Jurisdiction -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">07</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Legislación Aplicable y Jurisdicción') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Estos Términos del Servicio se rigen por la legislación civil, sanitaria y de protección al consumidor aplicable. Cualquier controversia será sometida a los tribunales competentes de la jurisdicción donde se ubica la sede principal de :clinic.', ['clinic' => $clinic->clinic_name]) }}
                    </p>
                </article>

                <!-- Section 8: Call to Action -->
                <article class="bg-gradient-to-br from-slate-900 to-[#13223f] text-white rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-lg sm:text-xl font-black text-white">{{ __('¿Tienes Preguntas sobre Nuestras Condiciones?') }}</h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        {{ __('Estamos a tu disposición para aclarar cualquier inquietud sobre nuestras políticas asistenciales, citas y formas de pago.') }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('schedule') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm">
                            <span>{{ __('Reservar Consulta Dental »') }}</span>
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition-colors border border-white/20">
                            <span>{{ __('Contactar con la Clínica') }}</span>
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
