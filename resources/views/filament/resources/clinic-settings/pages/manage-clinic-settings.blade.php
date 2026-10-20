<x-filament-panels::page>
    <div class="space-y-6 sm:space-y-8">

        <!-- ========================================== -->
        <!-- 1. HERO CARD: IDENTIDAD Y MARCA CLÍNICA     -->
        <!-- ========================================== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0c1830] via-[#0f2347] to-[#0A1730] p-6 sm:p-8 text-white border border-sky-500/20 shadow-xl shadow-sky-950/20">
            <!-- Patrón de fondo sutil y brillo estético -->
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Info principal de la clínica -->
                <div class="flex items-start gap-5">
                    <!-- Icono o Logotipo Oficial de la Clínica -->
                    @if($record->logo_asset_url)
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 p-2 flex items-center justify-center shrink-0 shadow-lg shadow-sky-500/30 border border-white/20 backdrop-blur-xs">
                            <img src="{{ $record->logo_asset_url }}" alt="{{ $record->clinic_name }}" class="w-full h-full object-contain rounded-xl" />
                        </div>
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-sky-500 to-sky-400 text-white flex items-center justify-center shrink-0 shadow-lg shadow-sky-500/30 border border-white/20">
                            <svg class="w-9 h-9 sm:w-11 sm:h-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                {{ $record->clinic_name }}
                            </h1>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                </span>
                                Clínica Activa & Sincronizada
                            </span>
                        </div>

                        <p class="text-slate-300 text-sm sm:text-base font-normal max-w-2xl leading-relaxed">
                            {{ $record->tagline ?: 'Odontología moderna, integral y sin dolor para sonrisas radiantes.' }}
                        </p>

                        <div class="pt-1 flex items-center gap-2 text-xs text-slate-400">
                            <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Última actualización: {{ $record->updated_at ? $record->updated_at->diffForHumans() : 'Recientemente' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botón de acción principal "Editar Información" -->
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button"
                            wire:click="mountAction('edit')"
                            class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-sky-500 hover:bg-sky-400 active:bg-sky-600 text-white font-bold text-sm shadow-lg shadow-sky-500/25 transition-all duration-200 cursor-pointer transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Editar Ajustes</span>
                    </button>
                </div>
            </div>

            <!-- Accesos rápidos de contacto directo -->
            <div class="relative z-10 mt-6 pt-6 border-t border-white/10 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="tel:{{ $record->phone }}"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-semibold text-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <span class="truncate">Llamar: {{ $record->phone }}</span>
                </a>

                <a href="mailto:{{ $record->email }}"
                   class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-semibold text-slate-200 transition-colors">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <span class="truncate">Enviar Correo: {{ $record->email }}</span>
                </a>

                @if($record->google_maps_url)
                    <a href="{{ $record->google_maps_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-semibold text-amber-300 transition-colors">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span class="truncate">Ver Ubicación GPS</span>
                    </a>
                @else
                    <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white/5 text-xs text-slate-400">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span class="truncate">Sin mapa configurado</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. GRID PRINCIPAL DE INFORMACIÓN           -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- ========================================== -->
            <!-- CARD 1: CANALES DE CONTACTO Y URGENCIAS    -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-[#0A1730] rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-xs p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Canales de Atención</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Líneas directas para citas, consultas y urgencias</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Teléfono Principal -->
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Teléfono Principal</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white truncate block">{{ $record->phone }}</span>
                            </div>
                        </div>
                        <a href="tel:{{ $record->phone }}" class="px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 hover:bg-sky-100 text-xs font-bold transition-colors">
                            Llamar
                        </a>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Correo de Atención</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white truncate block">{{ $record->email }}</span>
                            </div>
                        </div>
                        <a href="mailto:{{ $record->email }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 text-xs font-bold transition-colors">
                            Escribir
                        </a>
                    </div>

                    <!-- Teléfono de Urgencias -->
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-rose-50/60 dark:bg-rose-500/5 border border-rose-200/60 dark:border-rose-500/20">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 block">Línea de Urgencias</span>
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-rose-500 text-white">Prioritario</span>
                                </div>
                                <span class="text-sm font-black text-slate-900 dark:text-white truncate block">
                                    {{ $record->emergency_phone ?: 'Sin línea de urgencias configurada' }}
                                </span>
                            </div>
                        </div>
                        @if($record->emergency_phone)
                            <a href="tel:{{ $record->emergency_phone }}" class="px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition-colors">
                                Llamar
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CARD 2: SEDE FÍSICA Y UBICACIÓN GPS        -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-[#0A1730] rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-xs p-6 space-y-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 dark:text-white">Sede y Ubicación</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Dirección física e integración con mapas GPS</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <!-- Dirección -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Dirección Registrada</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-relaxed">
                                {{ $record->address }}
                            </p>
                        </div>

                        <!-- Estado de Google Maps -->
                        @if($record->google_maps_url)
                            <div class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-500/5 border border-amber-200/60 dark:border-amber-500/20 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Enlace GPS Activo para Pacientes
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300">
                                    El enlace configurado permite a los visitantes abrir la ruta exacta hacia la clínica desde cualquier app de navegación.
                                </p>
                                <a href="{{ $record->google_maps_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-colors">
                                    <span>Abrir en Google Maps</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-center">
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    No has añadido un enlace a Google Maps. Añade uno editando este perfil para que tus pacientes puedan navegar con 1 clic.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-white/5">
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 block">
                        Visible en la sección de Contacto y en el Pie de Página (Footer) del portal público.
                    </span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CARD 3: HORARIOS DE ATENCIÓN               -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-[#0A1730] rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-xs p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Horarios de Atención</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Jornadas y disponibilidad semanal para citas</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    @php
                        $lines = array_filter(array_map('trim', explode("\n", $record->opening_hours ?? '')));
                    @endphp

                    @forelse($lines as $line)
                        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                                {{ $line }}
                            </span>
                        </div>
                    @empty
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 text-center text-xs text-slate-400">
                            No hay horarios configurados actualmente.
                        </div>
                    @endforelse
                </div>

                <div class="p-3.5 rounded-xl bg-sky-50/50 dark:bg-sky-500/5 border border-sky-200/60 dark:border-sky-500/20 flex items-center gap-3">
                    <svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        Estos horarios se reflejan en el footer del portal y en la tarjeta informativa de reservas.
                    </p>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CARD 4: PRESENCIA WEB Y REDES SOCIALES     -->
            <!-- ========================================== -->
            <div class="bg-white dark:bg-[#0A1730] rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-xs p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Presencia Web y Redes</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Configuración global del sitio y perfiles sociales</p>
                        </div>
                    </div>
                </div>

                <!-- Identidad Visual en la Web (Logo y Favicon) -->
                <div class="space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Identidad Visual Web</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Preview Logo -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 flex items-center justify-center shrink-0 p-1">
                                @if($record->logo_asset_url)
                                    <img src="{{ $record->logo_asset_url }}" alt="Logo" class="max-h-full max-w-full object-contain rounded-lg" />
                                @else
                                    <svg class="w-6 h-6 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Logo de la Clínica</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block truncate">
                                    {{ $record->logo_url ? 'Logotipo oficial activo' : 'Usando isotipo por defecto' }}
                                </span>
                            </div>
                        </div>

                        <!-- Preview Favicon -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 flex items-center justify-center shrink-0 p-1.5">
                                @if($record->favicon_asset_url)
                                    <img src="{{ $record->favicon_asset_url }}" alt="Favicon" class="w-7 h-7 object-contain" />
                                @else
                                    <svg class="w-6 h-6 text-sky-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Favicon de Pestaña</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block truncate">
                                    {{ $record->favicon_url ? 'Icono de pestaña activo' : 'Icono SVG por defecto' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociales -->
                <div class="space-y-3 pt-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Redes Sociales Oficiales</span>

                    @php
                        $socials = $record->social_links ?? [];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <!-- Instagram -->
                        @if(!empty($socials['instagram']))
                            <a href="{{ $socials['instagram'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex items-center gap-2.5 p-3 rounded-xl bg-pink-50/50 dark:bg-pink-500/10 border border-pink-200/60 dark:border-pink-500/20 text-pink-700 dark:text-pink-300 hover:bg-pink-100/60 text-xs font-bold transition-colors">
                                <svg class="w-4 h-4 text-pink-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                                <span class="truncate">Instagram</span>
                            </a>
                        @else
                            <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-slate-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                <span>Instagram</span>
                            </div>
                        @endif

                        <!-- Facebook -->
                        @if(!empty($socials['facebook']))
                            <a href="{{ $socials['facebook'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex items-center gap-2.5 p-3 rounded-xl bg-blue-50/50 dark:bg-blue-500/10 border border-blue-200/60 dark:border-blue-500/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100/60 text-xs font-bold transition-colors">
                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.199l-.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.999-3.199a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                                <span class="truncate">Facebook</span>
                            </a>
                        @else
                            <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-slate-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                <span>Facebook</span>
                            </div>
                        @endif

                        <!-- TikTok -->
                        @if(!empty($socials['tiktok']))
                            <a href="{{ $socials['tiktok'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-100 dark:bg-white/10 border border-slate-300/60 dark:border-white/15 text-slate-900 dark:text-white hover:bg-slate-200/60 text-xs font-bold transition-colors">
                                <svg class="w-4 h-4 text-slate-900 dark:text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <span class="truncate">TikTok</span>
                            </a>
                        @else
                            <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 text-slate-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                <span>TikTok</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- 3. FOOTER INFORMATIVO                       -->
        <!-- ========================================== -->
        <div class="bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200/80 dark:border-white/10 p-4 sm:p-5 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
            <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286z" />
                </svg>
            </div>
            <span>
                Los datos configurados en este módulo se sincronizan instantáneamente con el portal público, horarios de consulta y correos electrónicos del sistema.
            </span>
        </div>

    </div>
</x-filament-panels::page>
