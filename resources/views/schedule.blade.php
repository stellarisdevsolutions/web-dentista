<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    <title>Reserva tu Cita Dental Online | {{ $clinic->clinic_name }}</title>
    <meta name="description" content="Agenda tu consulta odontológica online 24/7. Elige tratamiento, turno mañana o tarde y recibe confirmación inmediata vía WhatsApp.">

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f0f7ff] text-slate-900 selection:bg-sky-500 selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- MODERN FLOATING TOAST NOTIFICATION -->
    <div id="bookingToast"
         role="alert"
         aria-live="polite"
         class="fixed top-5 right-4 sm:right-6 z-[100] max-w-sm w-[calc(100%-2rem)] sm:w-auto min-w-[300px] sm:min-w-[340px] pointer-events-none transition-all duration-300 ease-out transform -translate-y-6 opacity-0 scale-95"
         style="display: none;">
        <div id="bookingToastCard" class="pointer-events-auto bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl p-4 shadow-2xl flex items-start gap-3.5 transition-all">
            <div id="bookingToastIconWrap" class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 bg-amber-100 text-amber-600">
                <!-- Icon via JS -->
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <h5 id="bookingToastTitle" class="text-xs font-black text-slate-900 tracking-tight">Selección requerida</h5>
                <p id="bookingToastMessage" class="text-xs text-slate-600 mt-0.5 leading-snug"></p>
            </div>
            <button type="button" onclick="hideToast()" class="text-slate-400 hover:text-slate-600 p-1 -mr-1 rounded-lg transition-colors" title="Cerrar notificación">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- GLOBAL NAVBAR -->
    @include('partials.navbar')

<main class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section class="bg-[#13223f] text-white py-14 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 left-0 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-4xl mx-auto text-center relative z-10 animate-fade-in-up">
                <span class="text-sky-400 font-bold text-xs uppercase tracking-widest block mb-2">RESERVA ONLINE 24/7</span>
                <h1 class="text-3xl sm:text-5xl font-black text-white">Agenda tu Cita Dental en 3 Pasos</h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto mt-3 font-light leading-relaxed">
                    Selecciona tu tratamiento, el turno que mejor te acomode (Mañana o Tarde) y recibe confirmación instantánea vía WhatsApp con recepción.
                </p>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- MAIN BOOKING WIZARD CONTAINER              -->
        <!-- ========================================== -->
        <section class="py-12 sm:py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Wizard Step Progress Indicator -->
                <div class="flex items-center justify-between max-w-lg mx-auto mb-10 relative">
                    <div class="absolute top-1/2 left-0 right-0 h-1 bg-slate-200 -translate-y-1/2 -z-0"></div>
                    <div id="progressBarFill" class="absolute top-1/2 left-0 h-1 bg-sky-500 -translate-y-1/2 -z-0 transition-all duration-500" style="width: 0%;"></div>

                    <div id="stepBubble1" class="w-10 h-10 rounded-full font-black text-xs flex items-center justify-center relative z-10 transition-all duration-300 bg-sky-500 text-white shadow-lg shadow-sky-500/30 scale-110">
                        1
                    </div>
                    <div id="stepBubble2" class="w-10 h-10 rounded-full font-black text-xs flex items-center justify-center relative z-10 transition-all duration-300 bg-slate-200 text-slate-500">
                        2
                    </div>
                    <div id="stepBubble3" class="w-10 h-10 rounded-full font-black text-xs flex items-center justify-center relative z-10 transition-all duration-300 bg-slate-200 text-slate-500">
                        3
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-slate-200/80 relative">
                    
                    <!-- Form Element -->
                    <form id="appointmentScheduleForm" onsubmit="handleScheduleSubmit(event)">
                        @csrf
                        <input type="hidden" name="service_id" id="formServiceId" value="{{ $selectedServiceId ?? '' }}">
                        <input type="hidden" name="dentist_id" id="formDentistId" value="{{ $selectedDentistId ?? '' }}">
                        <input type="hidden" name="appointment_date" id="formAppointmentDate" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <input type="hidden" name="appointment_time" id="formAppointmentTime" value="">
                        <input type="hidden" name="is_first_visit" id="formIsFirstVisit" value="1">

                        <!-- ============================================== -->
                        <!-- STEP 1: PATIENT TYPE & SERVICE / TREATMENT     -->
                        <!-- ============================================== -->
                        <div id="stepView1" class="space-y-8">
                            <div class="border-b border-slate-100 pb-4">
                                <span class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1">Paso 1 de 3</span>
                                <h2 class="text-2xl font-black text-slate-900">Tipo de Paciente y Tratamiento</h2>
                                <p class="text-xs text-slate-500">Indícanos si es tu primera consulta y el procedimiento que necesitas.</p>
                            </div>

                            <!-- 1. Patient Type Selector (Nuevo vs Ya soy paciente) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                                    1. ¿Nos visitas por primera vez?
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                    <!-- Card 1: Soy Paciente Nuevo -->
                                    <div
                                        onclick="selectPatientType(1)"
                                        id="patient-type-new"
                                        class="patient-type-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-sky-500 ring-2 ring-sky-500/20 shadow-xs bg-white"
                                    >
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                            </div>
                                            <span class="font-bold text-slate-900 text-sm sm:text-base">Soy Paciente Nuevo</span>
                                        </div>
                                        <div class="check-circle-patient w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors bg-sky-500 border-sky-500 text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Card 2: Ya soy Paciente -->
                                    <div
                                        onclick="selectPatientType(0)"
                                        id="patient-type-returning"
                                        class="patient-type-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-slate-200 hover:border-slate-300 bg-white"
                                    >
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <polyline points="16 11 18 13 22 9"></polyline>
                                                </svg>
                                            </div>
                                            <span class="font-bold text-slate-900 text-sm sm:text-base">Ya soy Paciente</span>
                                        </div>
                                        <div class="check-circle-patient w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors border-slate-200 text-transparent">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Treatment Selector -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                                    2. Selecciona el Tratamiento Dental
                                </label>
                                @if($services->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach($services as $srv)
                                            <div
                                                onclick="selectWizardService({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ $srv->duration_minutes ?? 45 }})"
                                                id="service-card-{{ $srv->id }}"
                                                class="service-select-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between {{ ($selectedServiceId == $srv->id) ? 'border-sky-500 ring-2 ring-sky-500/20 shadow-xs bg-white' : 'border-slate-200 hover:border-slate-300 bg-white' }}"
                                            >
                                                <span class="font-bold text-slate-900 text-sm sm:text-base pr-3">{{ $srv->name }}</span>
                                                <div class="check-circle w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors {{ ($selectedServiceId == $srv->id) ? 'bg-sky-500 border-sky-500 text-white' : 'border-slate-200 text-transparent' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-slate-400 text-xs">
                                        No hay tratamientos cargados actualmente.
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-end pt-6 border-t border-slate-100">
                                <button
                                    type="button"
                                    id="btnNextStep1"
                                    onclick="goToWizardStep(2)"
                                    class="bg-sky-500 hover:bg-sky-600 disabled:opacity-50 disabled:cursor-not-allowed text-white px-8 py-3.5 rounded-full font-black text-xs uppercase tracking-wider shadow-lg shadow-sky-500/30 transition-all flex items-center gap-2 hover:scale-105"
                                >
                                    <span>Continuar a Fecha y Hora</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="m12 5 7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 2: DENTIST, DATE & SHIFT SLOTS            -->
                        <!-- ============================================== -->
                        <div id="stepView2" class="space-y-6 hidden">
                            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                                <div>
                                    <span class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1">Paso 2 de 3</span>
                                    <h2 class="text-2xl font-black text-slate-900">Fecha, Horario y Especialista</h2>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-500">Tratamiento: <strong id="summarySelectedServiceName" class="text-sky-600">Por seleccionar</strong></span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-xs text-slate-500">Tipo: <strong id="summarySelectedPatientType" class="text-slate-700">Paciente Nuevo</strong></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-400">Duración estimada:</span>
                                    <span id="summarySelectedDuration" class="text-xs font-bold text-slate-800 ml-1">45 min</span>
                                </div>
                            </div>

                            <!-- SPECIALIST SELECTOR FOR RETURNING PATIENT -->
                            <div id="returningPatientDentistSelector" class="hidden">
                                <div class="bg-slate-50/80 border border-slate-200/90 rounded-2xl p-4 sm:p-5 transition-all">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-700 flex items-center justify-center flex-shrink-0 shadow-2xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-xs sm:text-sm font-bold text-slate-900">¿Deseas elegir a tu dentista de preferencia?</h4>
                                                <p class="text-[11px] text-slate-500 mt-0.5" id="returningDentistSelectedLabel">
                                                    Actualmente mostrando: <strong class="text-sky-600">Primer especialista disponible</strong> (horarios combinados de especialistas calificados).
                                                </p>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            id="btnToggleDentistList"
                                            onclick="toggleDentistSelectionList()"
                                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-sky-400 bg-white hover:bg-sky-50 text-sky-600 font-bold text-xs uppercase tracking-wider transition-all shadow-2xs flex-shrink-0"
                                        >
                                            <span id="btnToggleDentistText">Elegir Odontólogo</span>
                                            <svg id="btnToggleDentistChevron" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Collapsible cards container (hidden by default) -->
                                    <div id="dentistCardsCollapsible" class="hidden mt-4 pt-4 border-t border-slate-200/80">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Selecciona tu especialista habitual:</span>
                                            <button
                                                type="button"
                                                onclick="resetToFirstAvailableDentist()"
                                                class="text-[11px] font-bold text-sky-600 hover:text-sky-700 underline"
                                            >
                                                Restablecer a primer disponible
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div
                                                onclick="selectWizardDentist(null, 'Primer Especialista Disponible')"
                                                id="dentist-card-null"
                                                class="dentist-select-card p-3 rounded-xl border cursor-pointer transition-all flex items-center gap-3 border-sky-500 bg-sky-50/70"
                                            >
                                                <div class="w-9 h-9 rounded-full bg-[#13223f] text-sky-400 flex items-center justify-center flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-900 truncate">Primer disponible</p>
                                                    <p class="text-[10px] text-slate-500">Especialistas calificados</p>
                                                </div>
                                            </div>

                                            @foreach($dentists as $doc)
                                                <div
                                                    onclick="selectWizardDentist({{ $doc->id }}, '{{ addslashes($doc->name) }}')"
                                                    id="dentist-card-{{ $doc->id }}"
                                                    data-dentist-id="{{ $doc->id }}"
                                                    class="dentist-select-card p-3 rounded-xl border border-slate-200 hover:border-sky-300 bg-white cursor-pointer transition-all flex items-center gap-3"
                                                >
                                                    <div class="w-10 h-10 rounded-xl bg-[#0EA5E9] text-[#0F172A] border border-sky-400/30 flex items-center justify-center font-extrabold text-xs flex-shrink-0 overflow-hidden shadow-2xs select-none">
                                                        @if($doc->photo_url)
                                                            <img src="{{ $doc->photo_url }}" alt="{{ $doc->name }}" class="w-full h-full object-cover aspect-square">
                                                        @else
                                                            <span>{{ $doc->initials }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $doc->name }}</p>
                                                        <p class="text-[10px] text-slate-500 truncate">{{ $doc->specialty }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date and Time Slots Section (Restructured according to uploaded design) -->
                            <div class="pt-2">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Selecciona tu horario</h3>
                                    <span id="slotsLoadingSpinner" class="text-xs text-sky-600 font-bold hidden flex items-center gap-1.5">
                                        <svg class="animate-spin h-3.5 w-3.5 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        Consultando disponibilidad...
                                    </span>
                                </div>

                                <!-- Horizontal Date Carousel Header -->
                                <div class="flex items-center gap-1 sm:gap-3 border-b border-slate-200/90 pb-1">
                                    <!-- Prev Button -->
                                    <button
                                        type="button"
                                        id="btnPrevDateCarousel"
                                        onclick="navigateDateCarousel(-1)"
                                        title="Días anteriores"
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors flex-shrink-0 disabled:opacity-20 disabled:cursor-not-allowed"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    <!-- Date Tabs Grid (3 on mobile, 4 on desktop) -->
                                    <div id="dateCarouselTabs" class="flex-1 grid grid-cols-3 sm:grid-cols-4 gap-1 sm:gap-2">
                                        <!-- Rendered dynamically by JavaScript -->
                                    </div>

                                    <!-- Next Button -->
                                    <button
                                        type="button"
                                        id="btnNextDateCarousel"
                                        onclick="navigateDateCarousel(1)"
                                        title="Días siguientes"
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors flex-shrink-0"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <!-- Calendar Direct Picker Icon Button -->
                                    <div class="relative flex-shrink-0 pl-1">
                                        <button
                                            type="button"
                                            onclick="openDatePickerModal()"
                                            title="Elegir otra fecha específica en el calendario"
                                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-300 hover:border-sky-500 hover:bg-sky-50/50 text-slate-600 hover:text-sky-600 flex items-center justify-center transition-all bg-white shadow-2xs group"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-sky-600 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                                <line x1="16" x2="16" y1="2" y2="6"/>
                                                <line x1="8" x2="8" y1="2" y2="6"/>
                                                <line x1="3" x2="21" y1="10" y2="10"/>
                                                <path d="m9 16 2 2 4-4"/>
                                            </svg>
                                        </button>
                                        <input
                                            type="date"
                                            id="wizardDatePickerHidden"
                                            min="{{ date('Y-m-d') }}"
                                            value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                            onchange="onDateChanged(this.value)"
                                            class="sr-only"
                                        />
                                    </div>
                                </div>

                                <!-- Slots Vertical Stack (Morning -> Divider -> Evening) -->
                                <div class="py-6 space-y-7">
                                    <!-- 1. Morning Section -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-3.5">
                                            <svg class="w-6 h-6 text-sky-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 2v3"/>
                                                <path d="M4.93 4.93l2.12 2.12"/>
                                                <path d="M2 12h3"/>
                                                <path d="M19.07 4.93l-2.12 2.12"/>
                                                <path d="M22 12h-3"/>
                                                <path d="M5 16a7 7 0 0 1 14 0"/>
                                                <path d="M2 20h20"/>
                                            </svg>
                                            <h4 class="text-base sm:text-lg font-bold text-slate-900">Mañana</h4>
                                        </div>
                                        <div id="scheduleMorningSlots" class="flex flex-wrap gap-2.5 sm:gap-3">
                                            <div class="py-3 text-xs text-slate-400 italic">Cargando turnos...</div>
                                        </div>
                                    </div>

                                    <!-- Divider -->
                                    <div class="border-b border-slate-100"></div>

                                    <!-- 2. Evening / Afternoon Section -->
                                    <div>
                                        <div class="flex items-center gap-2 mb-3.5">
                                            <svg class="w-6 h-6 text-sky-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 13a7 7 0 0 1 14 0"/>
                                                <path d="M2 17h20"/>
                                                <path d="M4 21h16"/>
                                                <path d="M12 2v3"/>
                                                <path d="m4.93 4.93 2.12 2.12"/>
                                                <path d="m19.07 4.93-2.12 2.12"/>
                                            </svg>
                                            <h4 class="text-base sm:text-lg font-bold text-slate-900">Tarde</h4>
                                        </div>
                                        <div id="scheduleAfternoonSlots" class="flex flex-wrap gap-2.5 sm:gap-3">
                                            <div class="py-3 text-xs text-slate-400 italic">Cargando turnos...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between pt-4 border-t border-slate-100">
                                <button
                                    type="button"
                                    onclick="goToWizardStep(1)"
                                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider transition-colors"
                                >
                                    « Volver a Tratamientos
                                </button>
                                <button
                                    type="button"
                                    id="btnNextStep2"
                                    onclick="validateAndGoToStep3()"
                                    class="bg-sky-500 hover:bg-sky-600 disabled:opacity-50 disabled:cursor-not-allowed text-white px-8 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all flex items-center gap-2 hover:scale-105"
                                >
                                    <span>Continuar a tus Datos</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="m12 5 7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 3: PATIENT INFORMATION & CONFIRMATION      -->
                        <!-- ============================================== -->
                        <div id="stepView3" class="space-y-6 hidden">
                            <div class="border-b border-slate-100 pb-4">
                                <span class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1">Paso 3 de 3</span>
                                <h2 class="text-2xl font-black text-slate-900">Tus Datos de Contacto</h2>
                                <p class="text-xs text-slate-500">Ingresa tus datos para registrar la reserva y enviarte la confirmación por WhatsApp.</p>
                            </div>

                            <!-- Summary Card Preview -->
                            <div class="bg-sky-50/70 rounded-2xl p-4 border border-sky-100 flex flex-wrap justify-between items-center gap-3">
                                <div>
                                    <span class="text-[10px] text-sky-800 font-bold uppercase tracking-wider block">Resumen de tu Cita</span>
                                    <p class="text-xs font-black text-slate-900 mt-0.5">
                                        <span id="finalSummaryService">-</span> con <span id="finalSummaryDentist" class="text-sky-700">-</span>
                                    </p>
                                    <span class="text-[11px] text-slate-500 mt-0.5 block" id="finalSummaryPatientType">Paciente Nuevo</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Fecha y Turno</span>
                                    <p class="text-xs font-black text-sky-600 mt-0.5" id="finalSummaryDateTime">-</p>
                                </div>
                            </div>

                            <!-- Error Box -->
                            <div id="bookingScheduleError" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold hidden"></div>

                            <!-- Patient Inputs -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre Completo *</label>
                                    <input
                                        type="text"
                                        name="patient_name"
                                        required
                                        placeholder="Ej: Sofia Martínez"
                                        class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-sky-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico *</label>
                                    <input
                                        type="email"
                                        name="patient_email"
                                        required
                                        placeholder="sofia@ejemplo.com"
                                        class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-sky-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Teléfono Móvil / WhatsApp *</label>
                                <input
                                    type="tel"
                                    name="patient_phone"
                                    required
                                    placeholder="+1 555 123 4567"
                                    class="w-full sm:w-1/2 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-sky-500"
                                />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Motivo de Consulta o Síntomas (Opcional)</label>
                                <textarea
                                    name="symptoms"
                                    rows="2"
                                    placeholder="Describe brevemente alguna molestia, preferencia o dolor específico..."
                                    class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-sky-500"
                                ></textarea>
                            </div>

                            <div class="flex justify-between pt-4 border-t border-slate-100">
                                <button
                                    type="button"
                                    onclick="goToWizardStep(2)"
                                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider transition-colors"
                                >
                                    « Modificar Horario
                                </button>
                                <button
                                    type="submit"
                                    id="btnConfirmSchedule"
                                    class="bg-sky-500 hover:bg-sky-600 text-white px-9 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all flex items-center gap-2 hover:scale-105"
                                >
                                    <span id="btnConfirmText">Confirmar y Agendar Cita »</span>
                                    <svg id="btnConfirmSpinner" class="animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </form>

                    <!-- ============================================== -->
                    <!-- CONFIRMATION / SUCCESS STATE                   -->
                    <!-- ============================================== -->
                    <div id="scheduleSuccessCard" class="text-center py-10 space-y-6 hidden animate-scale-in">
                        <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner animate-pulse-glow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>

                        <div>
                            <span class="inline-block px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-[11px] font-black uppercase tracking-wider mb-2">
                                Código de Cita: <span id="successCodeDisplay">CIT-PENDIENTE</span>
                            </span>
                            <h2 class="text-3xl font-black text-slate-900">¡Tu Cita Dental Ha Sido Registrada!</h2>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-2 leading-relaxed">
                                Te esperamos en nuestras instalaciones. Tu turno de atención médica ha quedado registrado con éxito. Si tienes dudas, puedes llamar a nuestra recepción.
                            </p>
                        </div>

                        <!-- Ticket Receipt Details -->
                        <div class="max-w-md mx-auto bg-slate-50 rounded-2xl p-5 border border-slate-200/80 text-left space-y-2.5 text-xs">
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400 font-medium">Tratamiento:</span>
                                <span class="font-bold text-slate-900" id="successCardService">-</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400 font-medium">Especialista:</span>
                                <span class="font-bold text-slate-900" id="successCardDentist">-</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400 font-medium">Fecha y Horario:</span>
                                <span class="font-bold text-sky-600" id="successCardDateTime">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400 font-medium">Dirección Clínica:</span>
                                <span class="text-slate-700">{{ $clinic->address }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                            <a
                                href="tel:{{ $clinic->phone }}"
                                id="btnPhoneConfirmation"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all hover:scale-105"
                            >
                                <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                <span>Llamar a Recepción ({{ $clinic->phone }})</span>
                            </a>
                            <button
                                type="button"
                                onclick="resetScheduleWizard()"
                                class="w-full sm:w-auto px-6 py-3.5 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-bold uppercase tracking-wider transition-colors"
                            >
                                Agendar Otra Cita
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </section>

    </main>

    <!-- GLOBAL FOOTER -->
    @include('partials.footer')

    <!-- WIZARD INTERACTIVE JAVASCRIPT -->
    <script>
        const serviceDentistsMap = @json($services->mapWithKeys(fn($s) => [$s->id => $s->dentists->pluck('id')->all()]));

        let toastTimeout = null;

        function showToast(message, type = 'warning', title = null) {
            const toast = document.getElementById('bookingToast');
            const card = document.getElementById('bookingToastCard');
            const iconWrap = document.getElementById('bookingToastIconWrap');
            const titleEl = document.getElementById('bookingToastTitle');
            const msgEl = document.getElementById('bookingToastMessage');

            if (!toast || !card) return;

            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }

            const icons = {
                warning: `
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>`,
                error: `
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>`,
                info: `
                    <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>`,
                success: `
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>`
            };

            const styles = {
                warning: {
                    border: 'border-amber-300 shadow-amber-500/10 ring-1 ring-amber-400/20',
                    bgIcon: 'bg-amber-100 text-amber-600',
                    defaultTitle: 'Selección requerida'
                },
                error: {
                    border: 'border-rose-300 shadow-rose-500/10 ring-1 ring-rose-400/20',
                    bgIcon: 'bg-rose-100 text-rose-600',
                    defaultTitle: 'Atención'
                },
                info: {
                    border: 'border-sky-300 shadow-sky-500/10 ring-1 ring-sky-400/20',
                    bgIcon: 'bg-sky-100 text-sky-600',
                    defaultTitle: 'Información'
                },
                success: {
                    border: 'border-emerald-300 shadow-emerald-500/10 ring-1 ring-emerald-400/20',
                    bgIcon: 'bg-emerald-100 text-emerald-600',
                    defaultTitle: 'Completado'
                }
            };

            const cfg = styles[type] || styles.warning;

            card.className = `pointer-events-auto bg-white/95 backdrop-blur-md border ${cfg.border} rounded-2xl p-4 shadow-2xl flex items-start gap-3.5 transition-all`;
            iconWrap.className = `w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 ${cfg.bgIcon}`;
            iconWrap.innerHTML = icons[type] || icons.warning;

            titleEl.textContent = title || cfg.defaultTitle;
            msgEl.textContent = message;

            toast.style.display = 'block';
            requestAnimationFrame(() => {
                toast.classList.remove('-translate-y-6', 'opacity-0', 'scale-95');
                toast.classList.add('translate-y-0', 'opacity-100', 'scale-100');
            });

            toastTimeout = setTimeout(() => {
                hideToast();
            }, 4200);
        }

        function hideToast() {
            const toast = document.getElementById('bookingToast');
            if (!toast) return;

            toast.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
            toast.classList.add('-translate-y-6', 'opacity-0', 'scale-95');

            setTimeout(() => {
                toast.style.display = 'none';
            }, 300);
        }

        let currentWizardStep = 1;
        let isFirstVisit = 1;
        let selectedService = {
            id: '{{ $selectedServiceId ?? '' }}',
            name: '',
            duration: 45
        };
        let selectedDentist = {
            id: '{{ $selectedDentistId ?? '' }}',
            name: 'Primer Especialista Disponible'
        };
        let selectedDate = '{{ date('Y-m-d', strtotime('+1 day')) }}';
        let selectedTime = '';

        const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        function formatYMD(dateObj) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function parseYMD(ymdStr) {
            if (!ymdStr) return new Date();
            const parts = ymdStr.split('-');
            return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        }

        let carouselBaseDate = parseYMD(selectedDate);
        let currentDateSlotCount = null;

        const appTimeFormat = '{{ config('app.time_format', '12h') }}';

        function formatTime(time24) {
            if (!time24) return '';
            const parts = time24.split(':');
            let h = parseInt(parts[0], 10);
            const m = parts[1] || '00';
            if (appTimeFormat === '24h') {
                const hFormatted = h < 10 ? `0${h}` : `${h}`;
                return `${hFormatted}:${m}`;
            }
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            if (h === 0) h = 12;
            const hFormatted = h < 10 ? `0${h}` : `${h}`;
            return `${hFormatted}:${m} ${ampm}`;
        }

        function formatTime12h(time24) {
            return formatTime(time24);
        }

        function openDatePickerModal() {
            const hiddenPicker = document.getElementById('wizardDatePickerHidden');
            if (hiddenPicker) {
                if (typeof hiddenPicker.showPicker === 'function') {
                    hiddenPicker.showPicker();
                } else {
                    hiddenPicker.focus();
                    hiddenPicker.click();
                }
            }
        }

        function navigateDateCarousel(direction) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const newBase = new Date(carouselBaseDate);
            newBase.setDate(newBase.getDate() + (direction * 3));

            if (newBase.getTime() < today.getTime()) {
                carouselBaseDate = new Date(today);
            } else {
                carouselBaseDate = newBase;
            }

            renderDateCarouselTabs();
        }

        function renderDateCarouselTabs() {
            const container = document.getElementById('dateCarouselTabs');
            if (!container) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const prevBtn = document.getElementById('btnPrevDateCarousel');
            if (prevBtn) {
                const isAtStart = carouselBaseDate.getTime() <= today.getTime();
                prevBtn.disabled = isAtStart;
            }

            container.innerHTML = '';

            for (let i = 0; i < 4; i++) {
                const d = new Date(carouselBaseDate);
                d.setDate(d.getDate() + i);

                const dateStr = formatYMD(d);
                const isSelected = dateStr === selectedDate;
                const isSunday = d.getDay() === 0;

                const dayLabel = `${dayNames[d.getDay()]}, ${d.getDate()} ${monthNames[d.getMonth()]}`;

                const tab = document.createElement('div');
                tab.className = `date-carousel-tab relative py-2 px-1 text-center cursor-pointer transition-all select-none ${i === 3 ? 'hidden sm:block' : ''}`;

                if (isSelected) {
                    tab.innerHTML = `
                        <div class="font-bold text-xs sm:text-sm text-slate-900">${dayLabel}</div>
                        <div id="activeDateSlotCountLabel" class="text-[10px] sm:text-[11px] font-semibold text-emerald-600 mt-0.5">
                            ${currentDateSlotCount !== null ? (currentDateSlotCount > 0 ? `${currentDateSlotCount} Turnos Disponibles` : 'Sin turnos') : 'Consultando...'}
                        </div>
                        <div class="absolute bottom-0 left-2 right-2 h-0.5 bg-sky-500 rounded-full"></div>
                    `;
                } else {
                    const subtext = isSunday ? 'Cerrado' : 'Disponible';
                    const subtextClass = isSunday ? 'text-slate-400' : 'text-slate-500';
                    tab.innerHTML = `
                        <div class="font-semibold text-xs sm:text-sm text-slate-700 hover:text-slate-900">${dayLabel}</div>
                        <div class="text-[10px] sm:text-[11px] ${subtextClass} mt-0.5">${subtext}</div>
                    `;
                    tab.onclick = () => onDateChanged(dateStr);
                }

                container.appendChild(tab);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderDateCarouselTabs();
            const initialServiceId = document.getElementById('formServiceId').value;
            if (initialServiceId) {
                const initialCard = document.getElementById(`service-card-${initialServiceId}`);
                if (initialCard) {
                    initialCard.click();
                }
            } else {
                const nextBtn = document.getElementById('btnNextStep1');
                if (nextBtn) {
                    nextBtn.disabled = true;
                }
            }
        });

        function selectWizardService(id, name, duration) {
            selectedService.id = id;
            selectedService.name = name;
            selectedService.duration = parseInt(duration, 10) || 45;

            const formServiceId = document.getElementById('formServiceId');
            if (formServiceId) {
                formServiceId.value = id;
            }

            // Enable Next button in Step 1
            const nextBtn = document.getElementById('btnNextStep1');
            if (nextBtn) {
                nextBtn.disabled = false;
            }

            // Update card styling
            document.querySelectorAll('.service-select-card').forEach(card => {
                card.className = 'service-select-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-slate-200 hover:border-slate-300 bg-white';
                const circle = card.querySelector('.check-circle');
                if (circle) {
                    circle.className = 'check-circle w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors border-slate-200 text-transparent';
                }
            });

            const selectedCard = document.getElementById(`service-card-${id}`);
            if (selectedCard) {
                selectedCard.className = 'service-select-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-sky-500 ring-2 ring-sky-500/20 shadow-xs bg-white';
                const circle = selectedCard.querySelector('.check-circle');
                if (circle) {
                    circle.className = 'check-circle w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors bg-sky-500 border-sky-500 text-white';
                }
            }

            // Update step 2 summaries
            const summaryName = document.getElementById('summarySelectedServiceName');
            const summaryDuration = document.getElementById('summarySelectedDuration');
            if (summaryName) summaryName.textContent = name;
            if (summaryDuration) summaryDuration.textContent = `${selectedService.duration} min`;

            // Filter returning patient dentist cards for this specific service
            const qualifiedDentistIds = (serviceDentistsMap && serviceDentistsMap[id]) || [];
            document.querySelectorAll('.dentist-select-card[data-dentist-id]').forEach(card => {
                const docId = parseInt(card.getAttribute('data-dentist-id'), 10);
                const isQualified = qualifiedDentistIds.length === 0 || qualifiedDentistIds.includes(docId);
                card.style.display = isQualified ? '' : 'none';
            });

            // If selectedDentist was set, check if still qualified; if not, reset to first available
            if (selectedDentist.id && qualifiedDentistIds.length > 0 && !qualifiedDentistIds.includes(parseInt(selectedDentist.id, 10))) {
                resetToFirstAvailableDentist();
                showToast(`Se cambió a "Primer especialista disponible" porque el doctor previo no realiza ${name}.`, 'info', 'Especialista ajustado');
            } else if (!selectedDentist.id) {
                const label = document.getElementById('returningDentistSelectedLabel');
                if (label) {
                    label.innerHTML = `Actualmente mostrando: <strong class="text-sky-600">Primer especialista disponible</strong> (horarios unificados del equipo de ${name}).`;
                }
            }
        }

        function goToWizardStep(step) {
            if (step === 2 && !selectedService.id) {
                showToast('Por favor selecciona un tratamiento dental para continuar.', 'warning', 'Tratamiento requerido');
                const servicesBox = document.getElementById('stepView1');
                if (servicesBox) {
                    servicesBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                return;
            }

            currentWizardStep = step;

            // Toggle step views
            const stepView1 = document.getElementById('stepView1');
            const stepView2 = document.getElementById('stepView2');
            const stepView3 = document.getElementById('stepView3');

            if (stepView1) stepView1.classList.toggle('hidden', step !== 1);
            if (stepView2) stepView2.classList.toggle('hidden', step !== 2);
            if (stepView3) stepView3.classList.toggle('hidden', step !== 3);

            // Progress bar
            const progressBar = document.getElementById('progressBarFill');
            if (progressBar) {
                if (step === 1) progressBar.style.width = '0%';
                if (step === 2) progressBar.style.width = '50%';
                if (step === 3) progressBar.style.width = '100%';
            }

            // Step bubbles
            for (let i = 1; i <= 3; i++) {
                const bubble = document.getElementById(`stepBubble${i}`);
                if (!bubble) continue;

                if (i <= step) {
                    bubble.className = 'w-10 h-10 rounded-full font-black text-xs flex items-center justify-center relative z-10 transition-all duration-300 bg-sky-500 text-white shadow-lg shadow-sky-500/30 scale-110';
                } else {
                    bubble.className = 'w-10 h-10 rounded-full font-black text-xs flex items-center justify-center relative z-10 transition-all duration-300 bg-slate-200 text-slate-500 scale-100';
                }
            }

            if (step === 2) {
                updateStep2Visibility();
                const summaryName = document.getElementById('summarySelectedServiceName');
                const summaryDuration = document.getElementById('summarySelectedDuration');
                if (summaryName) summaryName.textContent = selectedService.name || 'Por seleccionar';
                if (summaryDuration) summaryDuration.textContent = `${selectedService.duration || 45} min`;
                renderDateCarouselTabs();
                fetchAvailableSlots();
            }

            // Scroll smoothly to top of form
            const formCard = document.getElementById('appointmentScheduleForm');
            if (formCard) {
                formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function selectPatientType(val) {
            isFirstVisit = parseInt(val, 10);
            document.getElementById('formIsFirstVisit').value = isFirstVisit;

            const newCard = document.getElementById('patient-type-new');
            const returningCard = document.getElementById('patient-type-returning');

            if (isFirstVisit === 1) {
                if (newCard) {
                    newCard.className = 'patient-type-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-sky-500 ring-2 ring-sky-500/20 shadow-xs bg-white';
                    const circle = newCard.querySelector('.check-circle-patient');
                    if (circle) circle.className = 'check-circle-patient w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors bg-sky-500 border-sky-500 text-white';
                }
                if (returningCard) {
                    returningCard.className = 'patient-type-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-slate-200 hover:border-slate-300 bg-white';
                    const circle = returningCard.querySelector('.check-circle-patient');
                    if (circle) circle.className = 'check-circle-patient w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors border-slate-200 text-transparent';
                }

                // New patients get unified slots with automatic allocation
                selectedDentist.id = '';
                selectedDentist.name = 'Primer Especialista Disponible';
                document.getElementById('formDentistId').value = '';
            } else {
                if (returningCard) {
                    returningCard.className = 'patient-type-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-sky-500 ring-2 ring-sky-500/20 shadow-xs bg-white';
                    const circle = returningCard.querySelector('.check-circle-patient');
                    if (circle) circle.className = 'check-circle-patient w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors bg-sky-500 border-sky-500 text-white';
                }
                if (newCard) {
                    newCard.className = 'patient-type-card p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-200 flex items-center justify-between border-slate-200 hover:border-slate-300 bg-white';
                    const circle = newCard.querySelector('.check-circle-patient');
                    if (circle) circle.className = 'check-circle-patient w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors border-slate-200 text-transparent';
                }
            }

            updateStep2Visibility();
        }

        function updateStep2Visibility() {
            const returningSelector = document.getElementById('returningPatientDentistSelector');
            const typeSummary = document.getElementById('summarySelectedPatientType');

            if (isFirstVisit === 1) {
                if (returningSelector) returningSelector.classList.add('hidden');
                if (typeSummary) typeSummary.textContent = 'Paciente Nuevo';
                selectedDentist.id = '';
                selectedDentist.name = 'Primer Especialista Disponible';
                document.getElementById('formDentistId').value = '';
            } else {
                if (returningSelector) returningSelector.classList.remove('hidden');
                if (typeSummary) typeSummary.textContent = 'Ya soy Paciente';

                // Keep collapsed by default if no specific doctor was selected yet
                const collapsible = document.getElementById('dentistCardsCollapsible');
                const chevron = document.getElementById('btnToggleDentistChevron');
                const btnText = document.getElementById('btnToggleDentistText');
                if (collapsible && !selectedDentist.id) {
                    collapsible.classList.add('hidden');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                    if (btnText) btnText.textContent = 'Elegir Odontólogo';
                }
            }
        }

        function toggleDentistSelectionList() {
            const container = document.getElementById('dentistCardsCollapsible');
            const chevron = document.getElementById('btnToggleDentistChevron');
            const btnText = document.getElementById('btnToggleDentistText');
            if (!container) return;

            const isHidden = container.classList.contains('hidden');
            container.classList.toggle('hidden', !isHidden);
            if (chevron) {
                chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
            if (btnText) {
                btnText.textContent = isHidden ? 'Ocultar Opciones' : (selectedDentist.id ? 'Cambiar Odontólogo' : 'Elegir Odontólogo');
            }
        }

        function resetToFirstAvailableDentist() {
            selectWizardDentist(null, 'Primer Especialista Disponible');
            const container = document.getElementById('dentistCardsCollapsible');
            const chevron = document.getElementById('btnToggleDentistChevron');
            const btnText = document.getElementById('btnToggleDentistText');
            if (container) container.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            if (btnText) btnText.textContent = 'Elegir Odontólogo';
        }

        function selectWizardDentist(id, name) {
            selectedDentist.id = id || '';
            selectedDentist.name = name;
            document.getElementById('formDentistId').value = selectedDentist.id;

            document.querySelectorAll('.dentist-select-card').forEach(card => {
                card.classList.remove('border-sky-500', 'bg-sky-50/70');
                card.classList.add('border-slate-200', 'bg-white');
            });

            const cardId = id ? `dentist-card-${id}` : 'dentist-card-null';
            const card = document.getElementById(cardId);
            if (card) {
                card.classList.remove('border-slate-200', 'bg-white');
                card.classList.add('border-sky-500', 'bg-sky-50/70');
            }

            const label = document.getElementById('returningDentistSelectedLabel');
            const btnText = document.getElementById('btnToggleDentistText');
            if (label) {
                label.innerHTML = id
                    ? `Especialista seleccionado: <strong class="text-sky-600">${name}</strong>`
                    : `Actualmente mostrando: <strong class="text-sky-600">Primer especialista disponible</strong> (horarios unificados del equipo de ${selectedService.name || 'este tratamiento'}).`;
            }
            if (btnText) {
                btnText.textContent = id ? 'Cambiar Odontólogo' : 'Elegir Odontólogo';
            }

            fetchAvailableSlots();
        }

        function onDateChanged(dateValue) {
            selectedDate = dateValue;
            document.getElementById('formAppointmentDate').value = dateValue;
            const hiddenPicker = document.getElementById('wizardDatePickerHidden');
            if (hiddenPicker) hiddenPicker.value = dateValue;

            // Shift carouselBaseDate window if selectedDate is outside the 4-day view
            const selObj = parseYMD(dateValue);
            selObj.setHours(0, 0, 0, 0);
            const baseObj = new Date(carouselBaseDate);
            baseObj.setHours(0, 0, 0, 0);
            const endWindow = new Date(baseObj);
            endWindow.setDate(endWindow.getDate() + 3);

            if (selObj.getTime() < baseObj.getTime() || selObj.getTime() > endWindow.getTime()) {
                carouselBaseDate = new Date(selObj);
            }

            currentDateSlotCount = null;
            renderDateCarouselTabs();
            fetchAvailableSlots();
        }

        async function fetchAvailableSlots() {
            const spinner = document.getElementById('slotsLoadingSpinner');
            if (spinner) spinner.classList.remove('hidden');

            const morningContainer = document.getElementById('scheduleMorningSlots');
            const afternoonContainer = document.getElementById('scheduleAfternoonSlots');

            if (morningContainer) morningContainer.innerHTML = '<div class="py-3 text-xs text-slate-400 italic">Consultando horarios disponibles...</div>';
            if (afternoonContainer) afternoonContainer.innerHTML = '<div class="py-3 text-xs text-slate-400 italic">Consultando horarios disponibles...</div>';

            selectedTime = '';
            document.getElementById('formAppointmentTime').value = '';

            try {
                const params = new URLSearchParams({
                    date: selectedDate,
                    service_id: selectedService.id || '',
                    dentist_id: selectedDentist.id || ''
                });

                const res = await fetch(`/api/appointments/available-slots?${params.toString()}`);
                const data = await res.json();

                const morningSlots = data.morning_slots || [];
                const afternoonSlots = data.afternoon_slots || [];

                renderShiftSlots(morningContainer, morningSlots);
                renderShiftSlots(afternoonContainer, afternoonSlots);

                const availableCount = morningSlots.filter(s => s.available).length + afternoonSlots.filter(s => s.available).length;
                currentDateSlotCount = availableCount;

                const countLabel = document.getElementById('activeDateSlotCountLabel');
                if (countLabel) {
                    countLabel.textContent = availableCount > 0
                        ? `${availableCount} Turnos Disponibles`
                        : 'Sin turnos disponibles';
                    countLabel.className = availableCount > 0
                        ? 'text-[10px] sm:text-[11px] font-semibold text-emerald-600 mt-0.5'
                        : 'text-[10px] sm:text-[11px] font-semibold text-slate-400 mt-0.5';
                }
            } catch (err) {
                if (morningContainer) morningContainer.innerHTML = '<div class="py-3 text-xs text-rose-500">Error al cargar horarios. Por favor intenta nuevamente.</div>';
                if (afternoonContainer) afternoonContainer.innerHTML = '';
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }

        function renderShiftSlots(container, slots) {
            if (!container) return;
            if (slots.length === 0) {
                container.innerHTML = '<div class="py-3 text-xs text-slate-400 italic">No hay turnos disponibles para este horario.</div>';
                return;
            }

            container.innerHTML = '';
            slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = slot.label || formatTime(slot.time);
                btn.dataset.time = slot.time;
                btn.disabled = !slot.available;

                if (!slot.available) {
                    btn.className = 'px-6 py-2.5 rounded-full font-bold text-xs bg-slate-50 border border-slate-200 text-slate-400 cursor-not-allowed line-through opacity-50 transition-all';
                } else {
                    btn.className = 'time-slot-btn px-6 py-2.5 rounded-full font-bold text-xs bg-white border border-slate-300 text-slate-800 hover:border-sky-500 hover:text-sky-600 hover:bg-sky-50/50 shadow-2xs transition-all';
                    btn.onclick = () => selectSlot(slot.time, btn);
                }

                container.appendChild(btn);
            });
        }

        function selectSlot(time, element) {
            selectedTime = time;
            document.getElementById('formAppointmentTime').value = time;

            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.className = 'time-slot-btn px-6 py-2.5 rounded-full font-bold text-xs bg-white border border-slate-300 text-slate-800 hover:border-sky-500 hover:text-sky-600 hover:bg-sky-50/50 shadow-2xs transition-all';
            });

            element.className = 'time-slot-btn px-6 py-2.5 rounded-full font-bold text-xs bg-sky-500 text-white border-sky-500 shadow-md shadow-sky-500/25 transition-all';
        }

        function validateAndGoToStep3() {
            if (!selectedTime) {
                showToast('Por favor selecciona un horario disponible para continuar.', 'warning', 'Horario requerido');
                const slotsHeading = document.getElementById('scheduleMorningSlots');
                if (slotsHeading) {
                    slotsHeading.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            document.getElementById('finalSummaryService').textContent = selectedService.name || 'Tratamiento seleccionado';
            document.getElementById('finalSummaryDentist').textContent = (isFirstVisit === 1 || !selectedDentist.id)
                ? 'Primer Especialista Disponible (Asignación automática)'
                : selectedDentist.name;
            document.getElementById('finalSummaryPatientType').textContent = (isFirstVisit === 1)
                ? 'Paciente Nuevo (Primera visita)'
                : 'Paciente Recurrente (Ficha clínica)';
            document.getElementById('finalSummaryDateTime').textContent = `${selectedDate} a las ${formatTime(selectedTime)}`;

            goToWizardStep(3);
        }

        async function handleScheduleSubmit(e) {
            e.preventDefault();

            const form = document.getElementById('appointmentScheduleForm');
            const errorBox = document.getElementById('bookingScheduleError');
            const submitBtn = document.getElementById('btnConfirmSchedule');
            const submitText = document.getElementById('btnConfirmText');
            const spinner = document.getElementById('btnConfirmSpinner');

            errorBox.classList.add('hidden');
            errorBox.textContent = '';
            submitBtn.disabled = true;
            submitText.textContent = 'Procesando reserva...';
            spinner.classList.remove('hidden');

            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('/appointments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    let errMsg = data.message || 'Ocurrió un error al agendar tu cita.';
                    if (data.errors) {
                        errMsg = Object.values(data.errors).flat().join(' ');
                    }
                    errorBox.textContent = errMsg;
                    errorBox.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitText.textContent = 'Confirmar y Agendar Cita »';
                    spinner.classList.add('hidden');
                    showToast(errMsg, 'error', 'No se pudo agendar');
                    return;
                }

                // Success
                form.classList.add('hidden');
                const successCard = document.getElementById('scheduleSuccessCard');
                successCard.classList.remove('hidden');

                document.getElementById('successCodeDisplay').textContent = data.code || 'CIT-CONFIRMADA';
                document.getElementById('successCardService').textContent = data.appointment?.service || selectedService.name;
                document.getElementById('successCardDentist').textContent = data.appointment?.dentist || selectedDentist.name;
                document.getElementById('successCardDateTime').textContent = `${data.appointment?.date || selectedDate} • ${data.appointment?.time || formatTime(selectedTime)}`;

                if (data.whatsapp_url && document.getElementById('btnWhatsappConfirmation')) {
                    document.getElementById('btnWhatsappConfirmation').href = data.whatsapp_url;
                }

                hideToast();
                window.scrollTo({ top: 300, behavior: 'smooth' });
            } catch (err) {
                errorBox.textContent = 'Error de conexión con el servidor. Por favor verifica tu conexión y vuelve a intentar.';
                errorBox.classList.remove('hidden');
                submitBtn.disabled = false;
                submitText.textContent = 'Confirmar y Agendar Cita »';
                spinner.classList.add('hidden');
                showToast('Error de conexión con el servidor. Intenta nuevamente.', 'error', 'Error de conexión');
            }
        }

        function resetScheduleWizard() {
            document.getElementById('appointmentScheduleForm').reset();
            document.getElementById('appointmentScheduleForm').classList.remove('hidden');
            document.getElementById('scheduleSuccessCard').classList.add('hidden');
            selectPatientType(1);
            goToWizardStep(1);
        }
    </script>
</body>
</html>
