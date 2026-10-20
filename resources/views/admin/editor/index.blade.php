<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Editor Visual | Personalizador de Secciones</title>
    
    @include('partials.favicon')

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 overflow-hidden select-none">

    <div 
        x-data="visualCustomizer({
            currentPage: '{{ $currentPage }}',
            sections: {{ Js::from($sections) }},
            saveUrl: '{{ route('admin.editor.save') }}',
            csrfToken: '{{ csrf_token() }}'
        })" 
        class="h-screen w-screen flex flex-col overflow-hidden"
    >
        <!-- ========================================== -->
        <!-- 1. TOP GLOBAL TOOLBAR (56px)               -->
        <!-- ========================================== -->
        <header class="h-14 bg-slate-950 border-b border-slate-800 px-4 flex items-center justify-between shrink-0 z-30">
            
            <!-- Left: Back Button & Page Selector -->
            <div class="flex items-center gap-3">
                <a 
                    href="/admin" 
                    class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all flex items-center gap-1.5 text-xs font-semibold"
                    title="Salir al Panel de Control"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="hidden sm:inline">Panel</span>
                </a>

                <div class="h-4 w-px bg-slate-800"></div>

                <!-- Brand & Section State -->
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-bold text-white tracking-tight hidden md:inline">Editor Visual</span>
                </div>

                <!-- Page Dropdown -->
                <div class="relative">
                    <select 
                        x-model="currentPage" 
                        @change="changePage($event.target.value)" 
                        class="bg-slate-900 border border-slate-700 text-xs text-white rounded-xl px-3 py-1.5 pr-8 focus:ring-1 focus:ring-sky-500 appearance-none cursor-pointer"
                    >
                        @foreach($availablePages as $key => $pageInfo)
                            <option value="{{ $key }}" {{ $currentPage === $key ? 'selected' : '' }}>
                                {{ $pageInfo['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Center: Viewport Switcher (Desktop vs Mobile 390px) -->
            <div class="flex items-center bg-slate-900 border border-slate-800 p-1 rounded-xl gap-1">
                <button 
                    type="button" 
                    @click="setDeviceMode('desktop')" 
                    :class="deviceMode === 'desktop' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-400 hover:text-white'" 
                    class="px-3 py-1 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5"
                    title="Vista de Computadora"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="20" height="14" x="2" y="3" rx="2" stroke-width="2"/><line x1="8" x2="16" y1="21" y2="21" stroke-width="2"/><line x1="12" x2="12" y1="17" y2="21" stroke-width="2"/></svg>
                    <span class="hidden sm:inline">Escritorio</span>
                </button>
                <button 
                    type="button" 
                    @click="setDeviceMode('mobile')" 
                    :class="deviceMode === 'mobile' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-400 hover:text-white'" 
                    class="px-3 py-1 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5"
                    title="Vista Móvil (iPhone / Android 390px)"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="14" height="20" x="5" y="2" rx="2" stroke-width="2"/><line x1="12" x2="12" y1="18" y2="18.01" stroke-width="2"/></svg>
                    <span class="hidden sm:inline">Móvil (390px)</span>
                </button>
            </div>

            <!-- Right: Status & Publish Button -->
            <div class="flex items-center gap-3">
                <div x-show="hasUnsavedChanges" class="hidden sm:flex items-center gap-1.5 text-xs text-amber-400 font-medium animate-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>Cambios sin publicar</span>
                </div>
                <div x-show="!hasUnsavedChanges" class="hidden sm:flex items-center gap-1.5 text-xs text-emerald-400 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Todo guardado</span>
                </div>

                <button 
                    type="button" 
                    @click="saveChanges()" 
                    :disabled="isSaving" 
                    class="bg-emerald-500 hover:bg-emerald-600 active:scale-95 disabled:opacity-50 text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2 cursor-pointer"
                >
                    <svg x-show="isSaving" class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <svg x-show="!isSaving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span x-text="isSaving ? 'Publicando...' : 'Publicar Cambios'"></span>
                </button>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- 2. SPLIT WORKSPACE BODY (2 COLUMNS)        -->
        <!-- ========================================== -->
        <div class="flex-1 flex overflow-hidden">

            <!-- LEFT CONTROL PANEL (380px) -->
            <aside class="w-[380px] bg-slate-950 border-r border-slate-800 flex flex-col shrink-0 z-20 overflow-hidden">
                
                <!-- VIEW 1: SECTIONS LIST -->
                <div x-show="activeSectionKey === null" class="flex-1 flex flex-col overflow-hidden">
                    <div class="p-4 border-b border-slate-800 bg-slate-900/30 flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-300">Secciones de la Página</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Haz clic en una sección para editarla o en el ojo para ocultarla.</p>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-3 space-y-2">
                        <template x-for="(section, index) in sections" :key="section.section_key">
                            <div 
                                @click="selectSection(section.section_key)" 
                                class="p-3 bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-xl transition-all flex items-center justify-between cursor-pointer group hover:bg-slate-900/90"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-slate-800 text-sky-400 flex items-center justify-center font-bold text-xs shrink-0 group-hover:bg-sky-500/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                                    </div>
                                    <div class="truncate">
                                        <h3 class="text-xs font-semibold text-white group-hover:text-sky-300 truncate transition-colors" x-text="section.name"></h3>
                                        <span class="text-[10px]" :class="section.is_active ? 'text-emerald-400' : 'text-slate-500'" x-text="section.is_active ? '● Visible' : '○ Oculta en web'"></span>
                                    </div>
                                </div>

                                <!-- Eye Visibility Switch -->
                                <button 
                                    type="button" 
                                    @click.stop="toggleSectionVisibility(section.section_key)" 
                                    class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors shrink-0" 
                                    :title="section.is_active ? 'Ocultar sección' : 'Mostrar sección'"
                                >
                                    <svg x-show="section.is_active" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="!section.is_active" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Bottom Quick Help -->
                    <div class="p-3 border-t border-slate-800 bg-slate-900/30 text-[11px] text-slate-500 text-center">
                        También puedes hacer clic directamente en cualquier sección del lienzo para seleccionarla.
                    </div>
                </div>

                <!-- VIEW 2: CONTEXTUAL FORM FOR ACTIVE SECTION -->
                <div x-show="activeSectionKey !== null" class="flex-1 flex flex-col overflow-hidden">
                    
                    <!-- Back Button Header -->
                    <div class="p-4 border-b border-slate-800 bg-slate-900/40 flex items-center justify-between shrink-0">
                        <button 
                            type="button" 
                            @click="deselectSection()" 
                            class="flex items-center gap-1.5 text-xs text-sky-400 hover:text-sky-300 font-semibold transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            <span>Volver a Secciones</span>
                        </button>
                        <span class="text-xs font-bold text-white truncate max-w-[170px]" x-text="activeSection?.name"></span>
                    </div>

                    <!-- Scrollable Form Inputs -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <template x-if="activeSection">
                            <div class="space-y-4">
                                
                                <!-- Visibility Toggle inside section -->
                                <div class="p-3 bg-slate-900/80 border border-slate-800 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-white block">Estado de la Sección</span>
                                        <span class="text-[10px] text-slate-400" x-text="activeSection.is_active ? 'Visible en el sitio web' : 'Oculta al público'"></span>
                                    </div>
                                    <button 
                                        type="button"
                                        @click="toggleSectionVisibility(activeSection.section_key)" 
                                        :class="activeSection.is_active ? 'bg-emerald-500' : 'bg-slate-700'"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none"
                                    >
                                        <span :class="activeSection.is_active ? 'translate-x-4' : 'translate-x-0.5'" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5"></span>
                                    </button>
                                </div>

                                <!-- Dynamic Content Fields -->
                                <template x-for="fieldKey in (activeSection ? Object.keys(activeSection.content) : [])" :key="fieldKey">
                                    <div class="space-y-1.5">
                                        
                                        <!-- LABEL MAPPING (no mostrar para arrays de testimonios que ya tienen cabecera propia) -->
                                        <template x-if="!Array.isArray(activeSection.content[fieldKey]) && fieldKey !== 'items'">
                                            <label class="text-xs font-bold text-slate-300 flex items-center justify-between" x-text="getFieldLabel(fieldKey)"></label>
                                        </template>

                                        <!-- TYPE 0: REPEATER (fieldKey === 'items' o Array de Elementos: Testimonios / FAQs) -->
                                        <template x-if="fieldKey === 'items' || Array.isArray(activeSection.content[fieldKey])">
                                            <div class="space-y-3 pt-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs font-bold text-sky-400 flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                        </svg>
                                                        <span x-text="activeSection.section_key === 'faqs' ? 'Preguntas Frecuentes (' + (activeSection.content[fieldKey] || []).length + ')' : 'Testimonios (' + (activeSection.content[fieldKey] || []).length + ')'"></span>
                                                    </span>
                                                    <button 
                                                        type="button" 
                                                        @click="addRepeaterItem(fieldKey)"
                                                        class="bg-sky-500 hover:bg-sky-400 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors shadow-sm cursor-pointer"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                        <span x-text="activeSection.section_key === 'faqs' ? 'Añadir Pregunta' : 'Añadir Testimonio'"></span>
                                                    </button>
                                                </div>

                                                <!-- Lista de tarjetas repetibles -->
                                                <div class="space-y-3">
                                                    <template x-for="(item, itemIndex) in (activeSection.content[fieldKey] || [])" :key="itemIndex">
                                                        <div 
                                                            @click="previewRepeaterItem(itemIndex)"
                                                            class="p-3.5 bg-slate-900 border border-slate-800 rounded-xl space-y-3 relative group hover:border-sky-500/40 transition-colors"
                                                        >
                                                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="w-5 h-5 rounded-full bg-sky-500/20 text-sky-400 text-[10px] font-black flex items-center justify-center" x-text="itemIndex + 1"></span>
                                                                    <span class="text-xs font-bold text-white truncate max-w-[140px]" x-text="activeSection.section_key === 'faqs' ? (item.question || 'Nueva Pregunta') : (item.patient_name || 'Nuevo Testimonio')"></span>
                                                                </div>
                                                                <div class="flex items-center gap-2">
                                                                    <!-- Star Badge preview solo para testimonios -->
                                                                    <template x-if="activeSection.section_key !== 'faqs'">
                                                                        <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20 flex items-center gap-1">
                                                                            <svg class="w-3 h-3 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
                                                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                                            </svg>
                                                                            <span x-text="Number(item.rating || 5)"></span>
                                                                        </span>
                                                                    </template>

                                                                    <!-- Botón Eliminar Elemento -->
                                                                    <button 
                                                                        type="button" 
                                                                        @click.stop="removeRepeaterItem(fieldKey, itemIndex)"
                                                                        class="text-slate-500 hover:text-rose-400 transition-colors p-1 cursor-pointer"
                                                                        :title="activeSection.section_key === 'faqs' ? 'Eliminar pregunta' : 'Eliminar testimonio'"
                                                                    >
                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <!-- CASO A: CAMPOS DE PREGUNTAS FRECUENTES (FAQS) -->
                                                            <template x-if="activeSection.section_key === 'faqs'">
                                                                <div class="space-y-2.5">
                                                                    <div>
                                                                        <label class="text-[10px] font-semibold text-slate-400 block mb-1">Pregunta del Paciente</label>
                                                                        <input 
                                                                            type="text" 
                                                                            :value="item.question" 
                                                                            @input="updateRepeaterItem(fieldKey, itemIndex, 'question', $event.target.value)"
                                                                            placeholder="Ej. ¿Aceptan seguros o planes de pago?"
                                                                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-sky-500"
                                                                        >
                                                                    </div>
                                                                    <div>
                                                                        <label class="text-[10px] font-semibold text-slate-400 block mb-1">Respuesta Detallada</label>
                                                                        <textarea 
                                                                            rows="3" 
                                                                            :value="item.answer" 
                                                                            @input="updateRepeaterItem(fieldKey, itemIndex, 'answer', $event.target.value)"
                                                                            placeholder="Escribe la respuesta clara..."
                                                                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-sky-500 leading-relaxed"
                                                                        ></textarea>
                                                                    </div>
                                                                </div>
                                                            </template>

                                                            <!-- CASO B: CAMPOS DE TESTIMONIOS -->
                                                            <template x-if="activeSection.section_key !== 'faqs'">
                                                                <div class="space-y-2.5">
                                                                    <!-- Nombre del Paciente -->
                                                                    <div>
                                                                        <label class="text-[10px] font-semibold text-slate-400 block mb-1">Nombre del Paciente</label>
                                                                        <input 
                                                                            type="text" 
                                                                            :value="item.patient_name" 
                                                                            @input="updateRepeaterItem(fieldKey, itemIndex, 'patient_name', $event.target.value)"
                                                                            placeholder="Ej. Maria Elena Delgado"
                                                                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-sky-500"
                                                                        >
                                                                    </div>

                                                                    <!-- Tratamiento -->
                                                                    <div>
                                                                        <label class="text-[10px] font-semibold text-slate-400 block mb-1">Tratamiento Realizado</label>
                                                                        <input 
                                                                            type="text" 
                                                                            :value="item.treatment" 
                                                                            @input="updateRepeaterItem(fieldKey, itemIndex, 'treatment', $event.target.value)"
                                                                            placeholder="Ej. Implante Dental y Corona"
                                                                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-sky-500"
                                                                        >
                                                                    </div>

                                                                    <!-- Calificación con input (ej. 1, 1.5, 2, ..., 5) -->
                                                                    <div>
                                                                        <div class="flex items-center justify-between mb-1">
                                                                            <label class="text-[10px] font-semibold text-slate-400">Calificación en Estrellas (1 a 5)</label>
                                                                            <span class="text-amber-400 font-bold text-xs" x-text="Number(item.rating || 5) + ' estrellas'"></span>
                                                                        </div>
                                                                        <div class="flex items-center gap-2">
                                                                            <input 
                                                                                type="number" 
                                                                                min="1" 
                                                                                max="5" 
                                                                                step="0.5" 
                                                                                :value="item.rating" 
                                                                                @input="updateRepeaterItem(fieldKey, itemIndex, 'rating', parseFloat($event.target.value) || 1)"
                                                                                class="w-20 bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-sky-500 text-center font-bold"
                                                                            >
                                                                            <input 
                                                                                type="range" 
                                                                                min="1" 
                                                                                max="5" 
                                                                                step="0.5" 
                                                                                :value="item.rating" 
                                                                                @input="updateRepeaterItem(fieldKey, itemIndex, 'rating', parseFloat($event.target.value) || 1)"
                                                                                class="flex-1 accent-amber-400 cursor-pointer h-1.5 bg-slate-800 rounded-lg"
                                                                            >
                                                                        </div>
                                                                    </div>

                                                                    <!-- Comentario -->
                                                                    <div>
                                                                        <label class="text-[10px] font-semibold text-slate-400 block mb-1">Comentario / Opinión del Paciente</label>
                                                                        <textarea 
                                                                            rows="3" 
                                                                            :value="item.comment" 
                                                                            @input="updateRepeaterItem(fieldKey, itemIndex, 'comment', $event.target.value)"
                                                                            placeholder="Escribe la experiencia del paciente..."
                                                                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-sky-500 leading-relaxed"
                                                                        ></textarea>
                                                                    </div>

                                                                    <!-- Avatar / Fotografía (Opcional) -->
                                                                    <div>
                                                                        <label class="text-[10px] font-semibold text-slate-400 block mb-1">Fotografía del Paciente (Opcional)</label>
                                                                        <div class="flex items-center gap-2">
                                                                            <div class="w-8 h-8 rounded-full bg-slate-800 overflow-hidden shrink-0 border border-slate-700 flex items-center justify-center">
                                                                                <template x-if="item.avatar_url">
                                                                                    <img :src="item.avatar_url" class="w-full h-full object-cover">
                                                                                </template>
                                                                                <template x-if="!item.avatar_url">
                                                                                    <span class="text-[10px] font-bold text-slate-500" x-text="(item.patient_name || 'P').charAt(0).toUpperCase()"></span>
                                                                                </template>
                                                                            </div>
                                                                            <button 
                                                                                type="button" 
                                                                                @click="openMediaPickerForRepeater(fieldKey, itemIndex, 'avatar_url')"
                                                                                class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-medium py-1.5 px-3 rounded-lg border border-slate-700 truncate text-left cursor-pointer"
                                                                            >
                                                                                <span x-text="item.avatar_url ? 'Cambiar Foto' : 'Elegir Foto (Opcional)'"></span>
                                                                            </button>
                                                                            <button 
                                                                                x-show="item.avatar_url" 
                                                                                type="button" 
                                                                                @click="updateRepeaterItem(fieldKey, itemIndex, 'avatar_url', '')"
                                                                                class="text-slate-500 hover:text-rose-400 p-1 text-xs cursor-pointer"
                                                                                title="Quitar foto y usar inicial"
                                                                            >
                                                                                ✕
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- TYPE 1: IMAGE FIELD (Sólo Reemplazar / Cambiar para evitar quedarse sin imagen) -->
                                        <template x-if="!Array.isArray(activeSection.content[fieldKey]) && fieldKey !== 'items' && (fieldKey.includes('image') || fieldKey.includes('photo'))">
                                            <div class="space-y-2">
                                                <!-- Preview Card (Clic directo para cambiar) -->
                                                <div 
                                                    @click="openMediaPicker(fieldKey)"
                                                    class="relative aspect-video rounded-xl overflow-hidden bg-slate-900 border border-slate-800 flex items-center justify-center cursor-pointer group hover:border-sky-500/50 transition-all shadow-inner"
                                                    title="Clic para seleccionar o cambiar imagen"
                                                >
                                                    <img x-show="activeSection.content[fieldKey]" :src="activeSection.content[fieldKey]" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                                    <div x-show="!activeSection.content[fieldKey]" class="text-slate-500 text-xs text-center p-4">
                                                        Sin imagen asignada (haz clic para elegir una)
                                                    </div>

                                                    <!-- Hover Overlay Informativo -->
                                                    <div class="absolute inset-0 bg-slate-950/65 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 text-white">
                                                        <svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-[11px] font-semibold text-sky-200">Clic para cambiar imagen</span>
                                                    </div>
                                                </div>

                                                <!-- Botón Destacado Único: Reemplazar Imagen (Sin botón de eliminar) -->
                                                <div>
                                                    <button 
                                                        type="button" 
                                                        @click="openMediaPicker(fieldKey)" 
                                                        class="w-full bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 hover:text-white border border-sky-500/40 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer"
                                                    >
                                                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                        <span x-text="activeSection.content[fieldKey] ? 'Cambiar / Reemplazar Imagen' : 'Seleccionar Imagen'"></span>
                                                    </button>
                                                    <p class="text-[10px] text-slate-500 mt-1 px-1">
                                                        Fotografía obligatoria de diseño: Para actualizarla, selecciona una imagen de la biblioteca.
                                                    </p>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- TYPE 2: LONG TEXT / SUBTITLE / DESCRIPTION -->
                                        <template x-if="!Array.isArray(activeSection.content[fieldKey]) && fieldKey !== 'items' && !fieldKey.includes('image') && !fieldKey.includes('photo') && (fieldKey.includes('desc') || fieldKey.includes('subtitle'))">
                                            <textarea 
                                                rows="3" 
                                                :value="activeSection.content[fieldKey]" 
                                                @input="updateField(fieldKey, $event.target.value)" 
                                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500 transition-colors"
                                            ></textarea>
                                        </template>

                                        <!-- TYPE 3: REGULAR SINGLE LINE TEXT -->
                                        <template x-if="!Array.isArray(activeSection.content[fieldKey]) && fieldKey !== 'items' && !fieldKey.includes('image') && !fieldKey.includes('photo') && !fieldKey.includes('desc') && !fieldKey.includes('subtitle')">
                                            <input 
                                                type="text" 
                                                :value="activeSection.content[fieldKey]" 
                                                @input="updateField(fieldKey, $event.target.value)" 
                                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500 transition-colors"
                                            >
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </aside>

            <!-- RIGHT CANVAS: LIVE PREVIEW CONTAINER -->
            <main class="flex-1 bg-slate-900/70 flex flex-col items-center justify-center p-3 relative overflow-hidden">
                <div 
                    :class="deviceMode === 'desktop' ? 'w-full h-full' : 'w-[390px] h-[844px] shadow-2xl rounded-[44px] border-[10px] border-slate-950 ring-1 ring-slate-800'" 
                    class="transition-all duration-300 bg-white overflow-hidden relative"
                >
                    <iframe 
                        x-ref="previewIframe" 
                        :src="'/' + (currentPage === 'home' ? '' : currentPage) + '?editor_preview=1'" 
                        class="w-full h-full border-0 select-auto"
                        @load="initIframeBridge()"
                    ></iframe>
                </div>
            </main>
        </div>

        <!-- ========================================== -->
        <!-- 3. REUSABLE WORDPRESS-LIKE MEDIA LIBRARY   -->
        <!-- ========================================== -->
        @include('admin.media-library.modal')

        <!-- Toast Notification -->
        <div 
            x-show="toast.visible" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            x-cloak
            class="fixed bottom-6 right-6 z-50 rounded-2xl shadow-2xl p-4 flex items-center gap-3 border backdrop-blur-md max-w-sm text-xs font-semibold"
            :class="{
                'bg-emerald-950/95 border-emerald-500/40 text-emerald-200 shadow-emerald-500/10': toast.type === 'success',
                'bg-rose-950/95 border-rose-500/40 text-rose-200 shadow-rose-500/10': toast.type === 'error',
                'bg-sky-950/95 border-sky-500/40 text-sky-200 shadow-sky-500/10': toast.type === 'info',
            }"
        >
            <div class="shrink-0">
                <template x-if="toast.type === 'success'">
                    <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </template>
                <template x-if="toast.type === 'error'">
                    <div class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                </template>
                <template x-if="toast.type === 'info'">
                    <div class="w-6 h-6 rounded-full bg-sky-500 text-white flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </template>
            </div>
            <div class="flex-1 min-w-0">
                <span x-text="toast.message"></span>
            </div>
            <button type="button" @click="toast.visible = false" class="text-slate-400 hover:text-white transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Script de Gestión del Customizer con Alpine.js -->
    <script>
        function visualCustomizer(config) {
            return {
                currentPage: config.currentPage,
                sections: config.sections,
                saveUrl: config.saveUrl,
                csrfToken: config.csrfToken,
                deviceMode: 'desktop',
                activeSectionKey: null,
                hasUnsavedChanges: false,
                isSaving: false,
                toast: { visible: false, message: '' },

                init() {
                    const self = this;
                    // Escuchar clics e inicializaciones del iframe desde el primer instante
                    window.addEventListener('message', function(event) {
                        if (!event.data || !event.data.type) return;

                        if (event.data.type === 'CMS_SECTION_CLICKED') {
                            self.activeSectionKey = event.data.sectionKey;
                        }

                        if (event.data.type === 'CMS_BRIDGE_READY') {
                            self.syncAllToIframe();
                        }
                    });
                },

                get activeSection() {
                    return this.sections.find(s => s.section_key === this.activeSectionKey) || null;
                },

                setDeviceMode(mode) {
                    this.deviceMode = mode;
                },

                changePage(page) {
                    window.location.href = `/admin/editor?page=${page}`;
                },

                selectSection(key) {
                    this.activeSectionKey = key;
                    this.sendToIframe({
                        type: 'SELECT_SECTION',
                        sectionKey: key
                    });
                },

                deselectSection() {
                    this.activeSectionKey = null;
                    this.sendToIframe({
                        type: 'DESELECT_SECTION'
                    });
                },

                toggleSectionVisibility(key) {
                    const section = this.sections.find(s => s.section_key === key);
                    if (!section) return;

                    section.is_active = !section.is_active;
                    this.hasUnsavedChanges = true;

                    this.sendToIframe({
                        type: 'TOGGLE_VISIBILITY',
                        sectionKey: key,
                        isActive: section.is_active
                    });
                },

                updateField(fieldKey, value) {
                    if (!this.activeSection) return;
                    this.activeSection.content[fieldKey] = value;
                    this.hasUnsavedChanges = true;

                    this.sendToIframe({
                        type: 'UPDATE_FIELD',
                        sectionKey: this.activeSection.section_key,
                        fieldKey: fieldKey,
                        value: value
                    });
                },

                notifyIframeUpdate() {
                    this.hasUnsavedChanges = true;
                    if (!this.activeSection) return;

                    this.sendToIframe({
                        type: 'UPDATE_SECTION_CONTENT',
                        sectionKey: this.activeSection.section_key,
                        content: this.activeSection.content
                    });
                },

                addRepeaterItem(fieldKey) {
                    if (!this.activeSection) return;
                    if (!Array.isArray(this.activeSection.content[fieldKey])) {
                        this.activeSection.content[fieldKey] = [];
                    }

                    let newItem;
                    if (this.activeSection.section_key === 'faqs') {
                        newItem = {
                            question: '¿Nueva pregunta frecuente?',
                            answer: 'Escribe aquí la respuesta clara y detallada para tus pacientes.'
                        };
                    } else {
                        newItem = {
                            patient_name: 'Nuevo Paciente',
                            treatment: 'Tratamiento Odontológico',
                            rating: 5,
                            comment: 'Excelente atención clínica y trato humano inmejorable.',
                            avatar_url: ''
                        };
                    }

                    this.activeSection.content[fieldKey].push(newItem);
                    this.hasUnsavedChanges = true;
                    this.notifyIframeUpdate();

                    const newIdx = this.activeSection.content[fieldKey].length - 1;
                    if (this.activeSection.section_key === 'faqs') {
                        this.sendToIframe({
                            type: 'PREVIEW_FAQ_INDEX',
                            index: newIdx
                        });
                    } else {
                        this.sendToIframe({
                            type: 'PREVIEW_TESTIMONIAL_INDEX',
                            index: newIdx
                        });
                    }
                },

                removeRepeaterItem(fieldKey, index) {
                    if (!this.activeSection || !this.activeSection.content[fieldKey]) return;
                    this.activeSection.content[fieldKey].splice(index, 1);
                    this.hasUnsavedChanges = true;
                    this.notifyIframeUpdate();
                },

                updateRepeaterItem(fieldKey, index, subKey, value) {
                    if (!this.activeSection || !this.activeSection.content[fieldKey]) return;
                    this.activeSection.content[fieldKey][index][subKey] = value;
                    this.hasUnsavedChanges = true;
                    this.notifyIframeUpdate();

                    if (this.activeSection.section_key === 'faqs') {
                        this.sendToIframe({
                            type: 'PREVIEW_FAQ_INDEX',
                            index: index
                        });
                    } else {
                        this.sendToIframe({
                            type: 'PREVIEW_TESTIMONIAL_INDEX',
                            index: index
                        });
                    }
                },

                previewRepeaterItem(index) {
                    if (!this.activeSection) return;
                    if (this.activeSection.section_key === 'faqs') {
                        this.sendToIframe({
                            type: 'PREVIEW_FAQ_INDEX',
                            index: index
                        });
                    } else {
                        this.sendToIframe({
                            type: 'PREVIEW_TESTIMONIAL_INDEX',
                            index: index
                        });
                    }
                },

                openMediaPickerForRepeater(fieldKey, index, subKey) {
                    const self = this;
                    window.dispatchEvent(new CustomEvent('open-media-library', {
                        detail: {
                            callback: function(url) {
                                if (self.activeSection && self.activeSection.content[fieldKey]) {
                                    self.updateRepeaterItem(fieldKey, index, subKey, url);
                                }
                            }
                        }
                    }));
                },

                openMediaPicker(fieldKey) {
                    const self = this;
                    window.dispatchEvent(new CustomEvent('open-media-library', {
                        detail: {
                            callback: function(url) {
                                if (self.activeSection) {
                                    self.updateField(fieldKey, url);
                                }
                            }
                        }
                    }));
                },

                getFieldLabel(key) {
                    if (key === 'items') {
                        return this.activeSection?.section_key === 'faqs' ? 'Preguntas Frecuentes' : 'Listado de Testimonios';
                    }

                    const labels = {
                        'eyebrow': 'Etiqueta Superior (Eyebrow)',
                        'title': 'Título Principal',
                        'subtitle': 'Subtítulo / Bajada',
                        'description': 'Descripción Detallada',
                        'benefit_1': 'Beneficio 1',
                        'benefit_2': 'Beneficio 2',
                        'check_text': 'Texto con Checkmark (✓)',
                        'check_1': 'Beneficio / Checkmark 1',
                        'check_2': 'Beneficio / Checkmark 2',
                        'check_3': 'Beneficio / Checkmark 3',
                        'button_text': 'Texto del Botón',
                        'button_url': 'Enlace del Botón',
                        'button_primary_text': 'Texto de Botón Principal',
                        'button_primary_url': 'Enlace de Botón Principal',
                        'badge_title': 'Título de Insignia Flotante',
                        'badge_subtitle': 'Subtítulo de Insignia Flotante',
                        'badge_years': 'Cifra en Sello Circular (ej. 20+)',
                        'badge_text': 'Texto en Sello Circular (ej. Años Exp.)',
                        'image_url': 'Fotografía Principal',
                        'image_1_url': 'Fotografía Principal (Superior)',
                        'image_2_url': 'Fotografía Secundaria (Superpuesta)',
                        'emergency_title': 'Título Urgencias',
                        'emergency_subtitle': 'Subtítulo Urgencias',
                        'schedule_title': 'Título Agenda en Línea',
                        'schedule_subtitle': 'Subtítulo Agenda en Línea',
                        'schedule_button_text': 'Texto del Botón de Reserva',
                        'stat_1_number': 'Cifra Estadística 1',
                        'stat_1_label': 'Etiqueta Estadística 1',
                        'stat_2_number': 'Cifra Estadística 2',
                        'stat_2_label': 'Etiqueta Estadística 2',
                        'stat_3_number': 'Cifra Estadística 3',
                        'stat_3_label': 'Etiqueta Estadística 3',
                        'stat_4_number': 'Cifra Estadística 4',
                        'stat_4_label': 'Etiqueta Estadística 4',
                        'step_1_title': 'Paso / Solución 1',
                        'step_2_title': 'Paso / Solución 2',
                        'step_3_title': 'Paso / Solución 3',
                        'step_4_title': 'Paso / Solución 4'
                    };

                    return labels[key] || key.replace(/_/g, ' ').toUpperCase();
                },

                sendToIframe(data) {
                    const iframe = this.$refs.previewIframe;
                    if (iframe && iframe.contentWindow) {
                        try {
                            const cleanPayload = JSON.parse(JSON.stringify(data));
                            iframe.contentWindow.postMessage(cleanPayload, '*');
                        } catch (e) {
                            console.error('Error enviando datos al iframe:', e);
                        }
                    }
                },

                syncAllToIframe() {
                    this.sections.forEach(s => {
                        this.sendToIframe({
                            type: 'TOGGLE_VISIBILITY',
                            sectionKey: s.section_key,
                            isActive: s.is_active
                        });
                    });
                },

                initIframeBridge() {
                    this.syncAllToIframe();
                },

                async saveChanges() {
                    this.isSaving = true;

                    try {
                        const res = await fetch(this.saveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                page: this.currentPage,
                                sections: this.sections
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.hasUnsavedChanges = false;
                            this.showToast(data.message || '¡Cambios publicados exitosamente!', 'success');
                        } else {
                            this.showToast(data.message || 'Error al guardar los cambios.', 'error');
                        }
                    } catch (e) {
                        console.error('Error saving sections:', e);
                        this.showToast('Ocurrió un error de conexión al guardar.', 'error');
                    } finally {
                        this.isSaving = false;
                    }
                },

                showToast(msg, type = 'success') {
                    this.toast.message = msg;
                    this.toast.type = type;
                    this.toast.visible = true;
                    if (this.toastTimeout) clearTimeout(this.toastTimeout);
                    this.toastTimeout = setTimeout(() => {
                        this.toast.visible = false;
                    }, 4000);
                }
            };
        }
    </script>
</body>
</html>
