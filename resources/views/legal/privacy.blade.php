<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    @include('partials.seo-meta', [
        'title' => __('Política de Privacidad') . ' | ' . $clinic->clinic_name,
        'description' => __('Conoce cómo protegemos tus datos personales, historial clínico y privacidad en :clinic.', ['clinic' => $clinic->clinic_name]),
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
                    <span class="text-sky-400 font-bold">{{ __('Política de Privacidad') }}</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 text-xs font-bold uppercase tracking-wider mb-4 border border-sky-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>{{ __('Protección de Datos y Confidencialidad') }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                    {{ __('Política de Privacidad') }}
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-2xl mx-auto mt-4 font-light leading-relaxed">
                    {{ __('En :clinic asumimos con el más alto rigor ético y profesional el resguardo de tus datos personales, historial clínico y confidencialidad médica conforme a las normativas de salud vigentes.', ['clinic' => $clinic->clinic_name]) }}
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

                <!-- Alert Box: Medical Secrecy -->
                <div class="bg-sky-50 border border-sky-200 rounded-2xl p-6 sm:p-7 flex flex-col sm:flex-row items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div class="space-y-1.5">
                        <h2 class="text-sm sm:text-base font-extrabold text-sky-950">
                            {{ __('Compromiso de Secreto Profesional y Confidencialidad Odontológica') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-sky-900/80 leading-relaxed">
                            {{ __('Toda la información médica bucodental, diagnósticos, radiografías y anotaciones clínicas que proporciones a :clinic están amparadas por el secreto médico profesional. Ningún dato de salud será divulgado ni compartido con fines distintos a tu adecuada atención sanitaria.', ['clinic' => $clinic->clinic_name]) }}
                        </p>
                    </div>
                </div>

                <!-- Section 1: Data Controller -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">01</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Responsable del Tratamiento de Datos') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('El responsable legal y técnico del tratamiento de los datos recabados a través de este portal web es:') }}
                    </p>
                    <ul class="text-xs sm:text-sm text-slate-700 space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200/60">
                        <li><strong class="font-bold text-slate-900">{{ __('Institución Odontológica:') }}</strong> {{ $clinic->clinic_name }}</li>
                        <li><strong class="font-bold text-slate-900">{{ __('Dirección Asistencial:') }}</strong> {{ $clinic->address }}</li>
                        <li><strong class="font-bold text-slate-900">{{ __('Teléfono Principal:') }}</strong> {{ $clinic->phone }}</li>
                        <li><strong class="font-bold text-slate-900">{{ __('Correo Electrónico Oficial:') }}</strong> {{ $clinic->email }}</li>
                        <li><strong class="font-bold text-slate-900">{{ __('Atención de Privacidad:') }}</strong> {{ $clinic->email }}</li>
                    </ul>
                </article>

                <!-- Section 2: Data Collected -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">02</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Datos Personales y de Salud Recopilados') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('A través de nuestros formularios de reserva online, formularios de contacto y durante la interacción asistencial en la clínica, podemos recabar las siguientes categorías de datos:') }}
                    </p>
                    <div class="grid sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Datos Identificativos y de Contacto') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Nombre y apellido, número telefónico, correo electrónico, fecha de nacimiento y documento de identidad.') }}
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Datos de Salud y Antecedentes Odontológicos') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Motivo de consulta dental, síntomas descritos, antecedentes médicos relevantes (alergias o patologías sistémicas) e historial de citas.') }}
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Datos de Agendamiento') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Tratamiento de interés, odontólogo o especialista seleccionado, fecha, hora preferente y notas de la reserva.') }}
                            </p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ __('Datos Técnicos y de Navegación') }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ __('Dirección IP anonimizada, navegador web utilizado y cookies técnicas estrictamente necesarias para la navegación.') }}
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Section 3: Purpose -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">03</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Finalidad y Base Jurídica del Tratamiento') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Tratamos la información que nos suministras exclusivamente para los siguientes fines asistenciales y administrativos:') }}
                    </p>
                    <ul class="text-xs sm:text-sm text-slate-700 space-y-3 pl-2">
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Gestión y Confirmación de Citas:') }}</strong> {{ __('Procesar reservas, asignar turnos con especialistas y enviar notificaciones de confirmación.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Recordatorios Clínicos y Seguimiento:') }}</strong> {{ __('Enviar avisos de proximidad de cita, instrucciones previas y posteriores al tratamiento mediante WhatsApp, llamadas o correo electrónico.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Confección de la Historia Clínica Odontológica:') }}</strong> {{ __('Cumplimiento estricto de las obligaciones sanitarias legales sobre expediente clínico odontológico.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Facturación y Gestión Administrativa:') }}</strong> {{ __('Emisión de comprobantes de pago, coordinación con aseguradoras dentales y auditorías reglamentarias.') }}</span>
                        </li>
                    </ul>
                </article>

                <!-- Section 4: Storage & Security -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">04</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Seguridad y Almacenamiento Seguro') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('Implementamos salvaguardas técnicas, administrativas y físicas avanzadas para salvaguardar tu información frente a accesos no autorizados, pérdidas o manipulaciones indebidas:') }}
                    </p>
                    <div class="grid sm:grid-cols-3 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center space-y-2">
                            <div class="w-8 h-8 mx-auto rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xs font-black text-slate-900">{{ __('Cifrado SSL / TLS') }}</h3>
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ __('Comunicaciones cifradas de punto a punto para todo el tráfico web y formularios.') }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center space-y-2">
                            <div class="w-8 h-8 mx-auto rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3 class="text-xs font-black text-slate-900">{{ __('Acceso Restringido') }}</h3>
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ __('Solo el personal sanitario debidamente autenticado accede a expedientes clínicos.') }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center space-y-2">
                            <div class="w-8 h-8 mx-auto rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                </svg>
                            </div>
                            <h3 class="text-xs font-black text-slate-900">{{ __('Custodia Legal Sanitaria') }}</h3>
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ __('Resguardo de historias clínicas por el tiempo exigido por la normativa sanitaria vigente.') }}</p>
                        </div>
                    </div>
                </article>

                <!-- Section 5: Third-Party Disclosures -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">05</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Cesión y Destinatarios de la Información') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('No vendemos, comercializamos ni alquilamos tus datos personales a empresas de publicidad ni a intermediarios ajenos. Solo se podrán comunicar tus datos a terceros en los siguientes supuestos estrictamente necesarios:') }}
                    </p>
                    <ul class="text-xs sm:text-sm text-slate-700 space-y-2.5 pl-2">
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Laboratorios Dentales y Protésicos Autorizados:') }}</strong> {{ __('Para la fabricación de coronas, carillas, alineadores o prótesis prescritas en tu tratamiento.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Aseguradoras Médicas y Dentales:') }}</strong> {{ __('Cuando el paciente solicite cobertura o reembolso directo con su compañía de seguros.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-sky-500 font-bold mt-0.5">•</span>
                            <span><strong class="font-bold text-slate-900">{{ __('Autoridades Sanitarias y Judiciales:') }}</strong> {{ __('En aquellos casos en que exista un mandato legal o requerimiento sanitario expreso.') }}</span>
                        </li>
                    </ul>
                </article>

                <!-- Section 6: Patient Rights -->
                <article class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 font-extrabold text-xs flex items-center justify-center flex-shrink-0">06</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">{{ __('Tus Derechos de Protección de Datos (ARCO)') }}</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ __('En cualquier momento puedes ejercer los siguientes derechos sobre tus datos personales:') }}
                    </p>
                    <div class="grid sm:grid-cols-2 gap-3 pt-1">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <h3 class="text-xs font-black text-slate-900 mb-1">{{ __('Acceso:') }}</h3>
                            <p class="text-xs text-slate-600">{{ __('Conocer qué datos personales e informes disponemos en tu expediente.') }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <h3 class="text-xs font-black text-slate-900 mb-1">{{ __('Rectificación:') }}</h3>
                            <p class="text-xs text-slate-600">{{ __('Actualizar o corregir información errónea o desactualizada.') }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <h3 class="text-xs font-black text-slate-900 mb-1">{{ __('Supresión / Cancelación:') }}</h3>
                            <p class="text-xs text-slate-600">{{ __('Solicitar la eliminación de tus datos, siempre que la ley sanitaria no exija su conservación.') }}</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <h3 class="text-xs font-black text-slate-900 mb-1">{{ __('Oposición y Portabilidad:') }}</h3>
                            <p class="text-xs text-slate-600">{{ __('Solicitar copia de radiografías e informes clínicos para una segunda opinión médica.') }}</p>
                        </div>
                    </div>
                </article>

                <!-- Section 7: Contact for Privacy -->
                <article class="bg-gradient-to-br from-slate-900 to-[#13223f] text-white rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-lg sm:text-xl font-black text-white">{{ __('Canal de Contacto y Dudas sobre Privacidad') }}</h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        {{ __('Si deseas ejercer cualquiera de tus derechos o tienes inquietudes sobre el tratamiento de tus datos en :clinic, contáctanos directamente a través de nuestro correo asistencial o en la recepción de la clínica.', ['clinic' => $clinic->clinic_name]) }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="mailto:{{ $clinic->email }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                            <span>{{ __('Escribir a :email', ['email' => $clinic->email]) }}</span>
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider transition-colors border border-white/20">
                            <span>{{ __('Formulario de Contacto »') }}</span>
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
