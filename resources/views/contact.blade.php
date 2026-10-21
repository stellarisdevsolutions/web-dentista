<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.favicon')

    <title>Contacto y Ubicación | {{ $clinic->clinic_name }}</title>
    <meta name="description" content="Ponte en contacto con {{ $clinic->clinic_name }}. Atención de urgencias dentales 24/7, consultas generales, ubicación y horarios de atención.">

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f0f7ff] text-slate-900 selection:bg-sky-500 selection:text-white" x-data="{ mobileMenuOpen: false, searchOpen: false, searchQuery: '', contactSubmitted: false, isSubmitting: false, errorMessage: '' }">

    <!-- GLOBAL NAVBAR -->
    @include('partials.navbar')

    @php
        $sectionsMap = isset($sections) ? $sections : \App\Models\SiteSection::forPage('contact')->get()->keyBy('section_key');
        $contactHero = $sectionsMap->get('contact_hero')?->content ?? [];
        $contactHeroActive = $sectionsMap->get('contact_hero')?->is_active ?? true;
        $isEditor = request()->has('editor_preview');

        $heroEyebrow = $contactHero['eyebrow'] ?? null;
        if (!array_key_exists('eyebrow', $contactHero)) {
            $heroEyebrow = 'ESTAMOS PARA AYUDARTE';
        }

        $heroTitle = $contactHero['title'] ?? null;
        if (!array_key_exists('title', $contactHero)) {
            $heroTitle = 'Contacta con ' . $clinic->clinic_name;
        }

        $heroSubtitle = $contactHero['subtitle'] ?? null;
        if (!array_key_exists('subtitle', $contactHero)) {
            $heroSubtitle = '¿Tienes dudas sobre un tratamiento o necesitas atención inmediata? Escríbenos, llámanos o visítanos en nuestras instalaciones.';
        }

        $hasHeroEyebrow = filled(trim($heroEyebrow ?? ''));
        $hasHeroTitle = filled(trim($heroTitle ?? ''));
        $hasHeroSubtitle = filled(trim($heroSubtitle ?? ''));

        $contactCards = $sectionsMap->get('contact_cards')?->content ?? [];
        $contactCardsActive = $sectionsMap->get('contact_cards')?->is_active ?? true;

        $card1Title = $contactCards['card_1_title'] ?? null;
        if (!array_key_exists('card_1_title', $contactCards)) {
            $card1Title = 'Teléfono y Urgencias';
        }
        $card1Desc = $contactCards['card_1_desc'] ?? null;
        if (!array_key_exists('card_1_desc', $contactCards)) {
            $card1Desc = 'Línea directa para consultas generales y emergencias odontológicas 24/7.';
        }

        $card2Title = $contactCards['card_2_title'] ?? null;
        if (!array_key_exists('card_2_title', $contactCards)) {
            $card2Title = 'Correo Electrónico';
        }
        $card2Desc = $contactCards['card_2_desc'] ?? null;
        if (!array_key_exists('card_2_desc', $contactCards)) {
            $card2Desc = 'Envíanos tus dudas, historial clínico o consultas sobre presupuestos.';
        }

        $card3Title = $contactCards['card_3_title'] ?? null;
        if (!array_key_exists('card_3_title', $contactCards)) {
            $card3Title = 'Ubicación de la Clínica';
        }
        $card3Desc = $contactCards['card_3_desc'] ?? null;
        if (!array_key_exists('card_3_desc', $contactCards)) {
            $card3Desc = 'Ubicación céntrica con estacionamiento gratuito para pacientes.';
        }

        $hasCard1Title = filled(trim($card1Title ?? ''));
        $hasCard1Desc = filled(trim($card1Desc ?? ''));
        $hasCard2Title = filled(trim($card2Title ?? ''));
        $hasCard2Desc = filled(trim($card2Desc ?? ''));
        $hasCard3Title = filled(trim($card3Title ?? ''));
        $hasCard3Desc = filled(trim($card3Desc ?? ''));

        $contactFaqs = $sectionsMap->get('contact_faqs')?->content ?? [];
        $contactFaqsActive = $sectionsMap->get('contact_faqs')?->is_active ?? true;

        $faqEyebrow = $contactFaqs['eyebrow'] ?? null;
        if (!array_key_exists('eyebrow', $contactFaqs)) {
            $faqEyebrow = 'PREGUNTAS FRECUENTES';
        }

        $faqTitle = $contactFaqs['title'] ?? null;
        if (!array_key_exists('title', $contactFaqs)) {
            $faqTitle = 'Orientación al Paciente';
        }

        $faqSubtitle = $contactFaqs['subtitle'] ?? null;
        if (!array_key_exists('subtitle', $contactFaqs)) {
            $faqSubtitle = 'Respuestas a las dudas más comunes antes de visitar nuestra clínica.';
        }

        $hasFaqEyebrow = filled(trim($faqEyebrow ?? ''));
        $hasFaqTitle = filled(trim($faqTitle ?? ''));
        $hasFaqSubtitle = filled(trim($faqSubtitle ?? ''));

        $contactFaqItems = $contactFaqs['items'] ?? [];
        if (empty($contactFaqItems) && isset($faqs) && $faqs->isNotEmpty()) {
            $contactFaqItems = $faqs->map(function ($f) {
                return [
                    'question' => $f->question,
                    'answer' => $f->answer,
                ];
            })->values()->toArray();
        }

        if (empty($contactFaqItems)) {
            $contactFaqItems = [
                [
                    'question' => '¿Debo reservar turno previo para una consulta o urgencia?',
                    'answer' => 'Para consultas programadas recomendamos reservar online con anticipación. Para urgencias odontológicas de dolor agudo, atendemos prioritariamente con llamada previa.',
                ],
                [
                    'question' => '¿Cuentan con opciones de financiamiento y seguro médico?',
                    'answer' => 'Sí, trabajamos con los principales seguros dentales y disponemos de planes de pago en cuotas fijas para tratamientos de ortodoncia e implantes.',
                ],
                [
                    'question' => '¿Dónde puedo estacionar cuando asisto a la clínica?',
                    'answer' => 'Contamos con estacionamiento exclusivo y gratuito para nuestros pacientes justo frente al ingreso principal de la clínica.',
                ],
            ];
        }
    @endphp

<main class="min-h-screen bg-white text-slate-900 flex flex-col justify-between overflow-x-hidden">

        <!-- ========================================== -->
        <!-- HEADER HERO BANNER (#13223f)               -->
        <!-- ========================================== -->
        <section data-cms-section="contact_hero" 
                 data-cms-name="Portada de Contacto (Hero)" 
                 class="bg-[#13223f] text-white py-14 px-4 sm:px-6 lg:px-8 relative overflow-hidden" 
                 @if(!$contactHeroActive && !$isEditor) style="display: none;" @endif>
            <div class="absolute -top-24 right-0 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
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
        <!-- 3 CONTACT INFO CARDS                        -->
        <!-- ========================================== -->
        <section class="py-14 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div data-cms-section="contact_cards" 
                     data-cms-name="Tarjetas de Contacto e Información" 
                     class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16"
                     @if(!$contactCardsActive && !$isEditor) style="display: none;" @endif>
                    
                    <!-- Card 1: Phone & Emergency -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-200/80 hover:border-sky-300 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                        <div class="w-14 h-14 rounded-2xl bg-[#13223f] text-sky-400 flex items-center justify-center mb-6 shadow-md group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <h3 data-cms-field="card_1_title" data-cms-row="card_1_title" class="text-xl font-bold text-slate-900 mb-2" @if(!$hasCard1Title) style="display: none;" @endif>
                            {{ $card1Title }}
                        </h3>
                        <p data-cms-field="card_1_desc" data-cms-row="card_1_desc" class="text-xs text-slate-500 mb-4" @if(!$hasCard1Desc) style="display: none;" @endif>
                            {{ $card1Desc }}
                        </p>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic->phone) }}" class="text-sky-500 font-extrabold text-sm hover:underline block">
                            {{ $clinic->phone }}
                        </a>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $clinic->emergency_phone ?? $clinic->phone) }}" class="text-slate-500 font-bold text-xs hover:underline block mt-1">
                            Urgencias: {{ $clinic->emergency_phone ?? $clinic->phone }}
                        </a>
                    </div>

                    <!-- Card 2: Email Support -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-200/80 hover:border-sky-300 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                        <div class="w-14 h-14 rounded-2xl bg-[#13223f] text-sky-400 flex items-center justify-center mb-6 shadow-md group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </div>
                        <h3 data-cms-field="card_2_title" data-cms-row="card_2_title" class="text-xl font-bold text-slate-900 mb-2" @if(!$hasCard2Title) style="display: none;" @endif>
                            {{ $card2Title }}
                        </h3>
                        <p data-cms-field="card_2_desc" data-cms-row="card_2_desc" class="text-xs text-slate-500 mb-4" @if(!$hasCard2Desc) style="display: none;" @endif>
                            {{ $card2Desc }}
                        </p>
                        <a href="mailto:{{ $clinic->email }}" class="text-sky-500 font-extrabold text-sm hover:underline block">
                            {{ $clinic->email }}
                        </a>
                    </div>

                    <!-- Card 3: Clinic Location -->
                    <div class="bg-slate-50 p-8 rounded-3xl border border-slate-200/80 hover:border-sky-300 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group">
                        <div class="w-14 h-14 rounded-2xl bg-[#13223f] text-sky-400 flex items-center justify-center mb-6 shadow-md group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <h3 data-cms-field="card_3_title" data-cms-row="card_3_title" class="text-xl font-bold text-slate-900 mb-2" @if(!$hasCard3Title) style="display: none;" @endif>
                            {{ $card3Title }}
                        </h3>
                        <p data-cms-field="card_3_desc" data-cms-row="card_3_desc" class="text-xs text-slate-500 mb-4" @if(!$hasCard3Desc) style="display: none;" @endif>
                            {{ $card3Desc }}
                        </p>
                        <span class="text-slate-800 font-extrabold text-xs block">{{ $clinic->address }}</span>
                        <a href="#mapSection" class="text-sky-500 font-bold text-xs hover:underline block mt-1">Ver mapa interactivo »</a>
                    </div>

                </div>

                <!-- ========================================== -->
                <!-- FORM & MAP GRID                            -->
                <!-- ========================================== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start mb-16" id="mapSection">
                    
                    <!-- Left Column: Contact Form -->
                    <div class="bg-slate-50 p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-lg">
                        <div class="mb-6">
                            <span class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1">MENSAJE DIRECTO</span>
                            <h2 class="text-3xl font-black text-slate-900">Envíanos un Mensaje</h2>
                            <p class="text-xs text-slate-500 mt-1">Nuestro equipo de atención al paciente te responderá prontamente.</p>
                        </div>

                        <!-- Feedback Success Alert -->
                        <div x-show="contactSubmitted" x-cloak class="p-8 rounded-2xl bg-emerald-50 text-emerald-800 text-center space-y-4 border border-emerald-200 animate-scale-in">
                            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl font-bold">
                                ✓
                            </div>
                            <h3 class="text-2xl font-black">¡Mensaje Enviado con Éxito!</h3>
                            <p class="text-xs text-emerald-700 leading-relaxed max-w-md mx-auto">
                                Gracias por escribir a {{ $clinic->clinic_name }}. Nuestro equipo de recepción se pondrá en contacto contigo a la brevedad.
                            </p>
                            <button type="button" @click="contactSubmitted = false" class="text-xs text-sky-600 font-bold underline">
                                Enviar otro mensaje
                            </button>
                        </div>

                        <!-- Contact Form Element -->
                        <form 
                            x-show="!contactSubmitted" 
                            id="directContactForm"
                            @submit.prevent="
                                isSubmitting = true;
                                errorMessage = '';
                                const formEl = $el;
                                const formData = new FormData(formEl);
                                fetch('{{ route('contact.submit') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    body: formData
                                })
                                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                                .then(({ status, body }) => {
                                    isSubmitting = false;
                                    if (status >= 200 && status < 300 && body.success) {
                                        contactSubmitted = true;
                                        formEl.reset();
                                    } else {
                                        errorMessage = body.message || 'Ocurrió un error al enviar el mensaje. Verifica los datos.';
                                    }
                                })
                                .catch(err => {
                                    isSubmitting = false;
                                    errorMessage = 'Error de conexión. Intenta nuevamente o contáctanos por WhatsApp.';
                                })
                            "
                            class="space-y-4"
                        >
                            @csrf

                            <div x-show="errorMessage" x-cloak class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold" x-text="errorMessage"></div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre Completo *</label>
                                <input type="text" name="name" required placeholder="Ej: Marcela Gómez" class="w-full p-3.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:border-sky-500 transition-colors" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico *</label>
                                    <input type="email" name="email" required placeholder="marcela@ejemplo.com" class="w-full p-3.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:border-sky-500 transition-colors" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Teléfono / WhatsApp</label>
                                    <input type="tel" name="phone" placeholder="+1 (555) 000-0000" class="w-full p-3.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:border-sky-500 transition-colors" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Asunto o Consulta</label>
                                <input type="text" name="subject" placeholder="Ej: Consulta sobre ortodoncia invisible, seguro médico, etc." class="w-full p-3.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:border-sky-500 transition-colors" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Mensaje *</label>
                                <textarea name="message" rows="4" required placeholder="Escribe aquí tu consulta o requerimiento específico..." class="w-full p-3.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:border-sky-500 transition-colors"></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full bg-sky-500 hover:bg-sky-600 disabled:opacity-50 text-white py-4 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky-500/25 transition-all flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95"
                            >
                                <span x-text="isSubmitting ? 'Enviando mensaje...' : 'Enviar Mensaje Directo »'"></span>
                            </button>
                        </form>
                    </div>

                    <!-- Right Column: Interactive Map & Working Hours -->
                    <div class="space-y-6">
                        
                        <!-- Map Container -->
                        <div class="rounded-3xl overflow-hidden shadow-xl border border-slate-200 h-[380px] bg-slate-100">
                            <iframe
                                title="Ubicación de la clínica"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2886.536648785863!2d-79.38705428450209!3d43.64256627912166!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b34d68bf33a9b%3A0x15f79d4949544e3!2sCN%20Tower!5e0!3m2!1sen!2sca!4v1650000000000!5m2!1sen!2sca"
                                width="100%"
                                height="100%"
                                style="border: 0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>

                        <!-- Working Hours Box -->
                        <div class="bg-[#13223f] text-white p-6 rounded-3xl shadow-xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-sky-500 text-white flex items-center justify-center flex-shrink-0 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-white text-sm">Horarios de Atención Clínica</h4>
                                    <p class="text-xs text-slate-300 mt-0.5">
                                        {{ $clinic->opening_hours ?? 'Lunes a Viernes: 8:00 AM - 7:00 PM | Sábados: 9:00 AM - 3:00 PM' }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('schedule') }}" class="hidden sm:inline-block px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-400 font-bold text-xs uppercase tracking-wider transition-colors">
                                Reservar Cita
                            </a>
                        </div>

                    </div>

                </div>

                <!-- ========================================== -->
                <!-- FAQ ACCORDION SECTION                      -->
                <!-- ========================================== -->
                <div data-cms-section="contact_faqs" 
                     data-cms-name="Preguntas Frecuentes" 
                     class="max-w-3xl mx-auto pt-8 border-t border-slate-100"
                     @if(!$contactFaqsActive && !$isEditor) style="display: none;" @endif>
                    <div class="text-center mb-8">
                        <span data-cms-field="eyebrow" data-cms-row="eyebrow" class="text-sky-500 font-bold text-xs uppercase tracking-widest block mb-1" @if(!$hasFaqEyebrow) style="display: none;" @endif>
                            {{ $faqEyebrow }}
                        </span>
                        <h2 data-cms-field="title" data-cms-row="title" class="text-3xl font-black text-slate-900" @if(!$hasFaqTitle) style="display: none;" @endif>
                            {{ $faqTitle }}
                        </h2>
                        <p data-cms-field="subtitle" data-cms-row="subtitle" class="text-slate-500 text-xs sm:text-sm mt-2 font-light leading-relaxed max-w-lg mx-auto" @if(!$hasFaqSubtitle) style="display: none;" @endif>
                            {{ $faqSubtitle }}
                        </p>
                    </div>

                    <div 
                        class="space-y-4" 
                        x-data="contactFaqsAccordion({ items: @js($contactFaqItems) })"
                    >
                        <template x-for="(faq, fIndex) in items" :key="fIndex">
                            <div class="border border-slate-200/80 rounded-2xl overflow-hidden bg-slate-50 transition-all duration-300">
                                <button
                                    type="button"
                                    @click="toggle(fIndex)"
                                    class="w-full p-5 text-left font-bold text-slate-800 text-sm flex items-center justify-between gap-4 hover:bg-slate-100/80 transition-colors cursor-pointer"
                                >
                                    <span x-text="faq.question"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-500 flex-shrink-0 transition-transform duration-300" :class="activeFaq === fIndex ? 'rotate-180' : ''">
                                        <path d="m6 9 6 6 6-6"></path>
                                    </svg>
                                </button>
                                <div x-show="activeFaq === fIndex" x-cloak class="p-5 pt-0 text-xs text-slate-600 leading-relaxed bg-white border-t border-slate-100">
                                    <p class="whitespace-pre-line" x-text="faq.answer"></p>
                                </div>
                            </div>
                        </template>
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

    <script>
        function contactFaqsAccordion(config) {
            return {
                activeFaq: 0,
                items: config.items || [],

                init() {
                    window.addEventListener('cms-contact_faqs-items-updated', (e) => {
                        if (Array.isArray(e.detail)) {
                            this.items = e.detail;
                        }
                    });

                    window.addEventListener('cms-contact_faqs-updated', (e) => {
                        if (e.detail && Array.isArray(e.detail.items)) {
                            this.items = e.detail.items;
                        }
                    });

                    window.addEventListener('cms-preview-faq-index', (e) => {
                        if (typeof e.detail === 'number') {
                            this.activeFaq = e.detail;
                        }
                    });
                },

                toggle(index) {
                    this.activeFaq = this.activeFaq === index ? null : index;
                }
            };
        }
    </script>

</body>
</html>
