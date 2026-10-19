<div x-data="{
        isOpen: false,
        isLoading: true,
        errorMessage: null,
        data: null,
        currentId: null,

        open(id) {
            this.currentId = id;
            this.isOpen = true;
            this.isLoading = true;
            this.errorMessage = null;
            this.data = null;

            document.body.style.overflow = 'hidden';
            this.fetchDetails(id);
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        async fetchDetails(id) {
            try {
                const res = await $wire.getDentistDetails(id);
                if (!res) {
                    this.errorMessage = 'No se encontró el especialista solicitado o no tienes permisos para verlo.';
                    this.isLoading = false;
                    return;
                }
                this.data = res;
                this.isLoading = false;
            } catch (err) {
                console.error('Error al cargar datos del especialista:', err);
                this.errorMessage = 'Ocurrió un error inesperado al cargar la información del especialista.';
                this.isLoading = false;
            }
        },

        retry() {
            if (this.currentId) {
                this.isLoading = true;
                this.errorMessage = null;
                this.fetchDetails(this.currentId);
            }
        }
     }"
     x-cloak
     @open-dentist-drawer.window="open($event.detail?.id)"
     @keydown.escape.window="close()"
     class="relative z-50">

    <!-- BACKDROP OSCURO CON BLUR SUAVE -->
    <div x-show="isOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
         style="backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
    </div>

    <!-- PANEL LATERAL DERECHO (SLIDE-OVER DRAWER) -->
    <div x-show="isOpen"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 flex max-w-full pl-10">

        <div class="w-screen max-w-md bg-slate-50 dark:bg-[#060D1A] text-slate-900 dark:text-slate-100 shadow-2xl flex flex-col border-l border-slate-200 dark:border-white/10"
             @click.away="close()">

            <!-- HEADER DEL DRAWER -->
            <div class="px-6 py-5 bg-white dark:bg-[#0A1730] border-b border-slate-200/80 dark:border-white/10 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div style="width: 40px; height: 40px; min-width: 40px; border-radius: 12px; background-color: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; color: #0EA5E9;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-base tracking-tight leading-tight">
                            Perfil del Especialista
                        </h3>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                            Información profesional y horarios clínicos
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <template x-if="data?.edit_url">
                        <a :href="data.edit_url"
                           class="p-2 rounded-xl text-slate-400 hover:text-sky-500 dark:hover:text-sky-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors"
                           title="Editar especialista">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                    </template>
                    <button type="button"
                            @click="close()"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- CONTENIDO DEL DRAWER CON SCROLL -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4">

                <!-- 1. SKELETON LOADER -->
                <div x-show="isLoading" class="space-y-4 animate-pulse">
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/80 dark:border-white/10 space-y-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-white/10"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-slate-200 dark:bg-white/10 rounded w-3/4"></div>
                                <div class="h-3 bg-slate-200 dark:bg-white/10 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/80 dark:border-white/10 space-y-3">
                        <div class="h-4 bg-slate-200 dark:bg-white/10 rounded w-1/3"></div>
                        <div class="h-10 bg-slate-200 dark:bg-white/10 rounded"></div>
                        <div class="h-10 bg-slate-200 dark:bg-white/10 rounded"></div>
                    </div>
                </div>

                <!-- 2. ERROR STATE -->
                <div x-show="!isLoading && errorMessage" class="p-6 rounded-2xl bg-white dark:bg-[#0A1730] border border-rose-200 dark:border-rose-500/20 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium" x-text="errorMessage"></p>
                    <div class="flex justify-center gap-2">
                        <button type="button" @click="retry()" class="px-4 py-2 rounded-full text-xs font-extrabold bg-[#0EA5E9] text-white">Reintentar</button>
                        <button type="button" @click="close()" class="px-4 py-2 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">Cerrar</button>
                    </div>
                </div>

                <!-- 3. DETALLE DEL ESPECIALISTA -->
                <div x-show="!isLoading && !errorMessage && data" class="space-y-4">

                    <!-- TARJETA PERFIL PRINCIPAL -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm transition-all">
                        <div class="flex items-start gap-4">
                            <!-- Foto o Avatar con botón de cambiar -->
                            <div class="relative group/drawer-avatar shrink-0">
                                <template x-if="data?.photo_url">
                                    <img :src="data.photo_url"
                                         :alt="data?.name"
                                         class="w-16 h-16 rounded-2xl object-cover ring-2 ring-sky-500/30 shrink-0 shadow-md">
                                </template>
                                <template x-if="!data?.photo_url">
                                    <div style="width: 64px; height: 64px; border-radius: 16px; background-color: #0EA5E9; color: #0F172A; font-weight: 800; font-size: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none;"
                                         x-text="data?.initials">
                                    </div>
                                </template>

                                <a :href="data?.edit_url"
                                   title="Cambiar fotografía del especialista"
                                   class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-slate-900/90 hover:bg-sky-600 text-white flex items-center justify-center shadow-md transition-colors border-2 border-white dark:border-slate-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8]"
                                          x-text="data?.specialty || 'Especialista'">
                                    </span>
                                    <!-- Badge Disponibilidad -->
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black shrink-0"
                                          :class="data?.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/20'"
                                          x-text="data?.is_active ? 'Disponible' : 'No disponible'">
                                    </span>
                                </div>

                                <h4 class="font-extrabold text-slate-900 dark:text-white text-lg leading-snug truncate mt-0.5"
                                    x-text="data?.name">
                                </h4>

                                <template x-if="data?.title">
                                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mt-0.5"
                                       x-text="data.title">
                                    </p>
                                </template>

                                <template x-if="data?.license_number">
                                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        <span>Col. <strong class="text-slate-900 dark:text-white" x-text="data.license_number"></strong></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Canales de contacto -->
                        <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-white/10 space-y-2 text-xs">
                            <template x-if="data?.phone">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        Teléfono:
                                    </span>
                                    <a :href="'tel:' + data.phone"
                                       class="font-extrabold text-[#0EA5E9] hover:underline"
                                       x-text="data.phone">
                                    </a>
                                </div>
                            </template>

                            <template x-if="data?.email">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Correo:
                                    </span>
                                    <a :href="'mailto:' + data.email"
                                       class="font-extrabold text-slate-900 dark:text-white hover:underline truncate max-w-[220px]"
                                       x-text="data.email">
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- BIOGRAFÍA -->
                    <template x-if="data?.bio">
                        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-2">
                            <span class="text-xs font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] flex items-center gap-1.5">
                                <svg width="16" height="16" class="text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Biografía y Formación
                            </span>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium"
                               x-text="data.bio">
                            </p>
                        </div>
                    </template>

                    <!-- TRATAMIENTOS ASIGNADOS -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] flex items-center gap-1.5">
                                <svg width="16" height="16" class="text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Tratamientos que Atiende
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-[#E0F2FE] dark:bg-[#16294D] text-[#0A74B8] dark:text-[#38BDF8]"
                                  x-text="(data?.services?.length || 0) + ' asignados'">
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-1.5 pt-1">
                            <template x-for="service in (data?.services || [])" :key="service.id">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-white/5 text-slate-800 dark:text-slate-200 border border-slate-200/60 dark:border-white/5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#0EA5E9]"></span>
                                    <span x-text="service.name"></span>
                                </span>
                            </template>
                            <template x-if="!data?.services || data.services.length === 0">
                                <span class="text-xs text-slate-500 dark:text-slate-400 italic">
                                    Sin tratamientos específicos asignados (atiende tratamientos generales).
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- HORARIOS DE ATENCIÓN SEMANAL -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-3">
                        <span class="text-xs font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] flex items-center gap-1.5">
                            <svg width="16" height="16" class="text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            Turnos y Horarios Semanales
                        </span>

                        <div class="space-y-2 pt-1">
                            <template x-for="item in (data?.schedule_days || [])" :key="item.day_key">
                                <div class="p-3 rounded-xl border flex items-center justify-between text-xs"
                                     :class="item.is_working ? 'bg-slate-50 dark:bg-white/5 border-slate-200/60 dark:border-white/5' : 'bg-slate-100/40 dark:bg-white/[0.02] border-transparent opacity-60'">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full"
                                              :class="item.is_working ? 'bg-emerald-500' : 'bg-slate-400'">
                                        </span>
                                        <span class="font-extrabold text-slate-800 dark:text-slate-200 capitalize"
                                              x-text="item.day_name">
                                        </span>
                                    </div>

                                    <template x-if="item.is_working">
                                        <div class="text-right">
                                            <div class="font-bold text-slate-900 dark:text-white" x-text="item.schedule_text"></div>
                                        </div>
                                    </template>
                                    <template x-if="!item.is_working">
                                        <span class="text-slate-400 dark:text-slate-500 font-semibold italic">Descanso</span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

            </div>

            <!-- FOOTER DEL DRAWER CON BOTÓN DE EDITAR RÁPIDO -->
            <div class="p-5 bg-white dark:bg-[#0A1730] border-t border-slate-200/80 dark:border-white/10 shrink-0 flex items-center justify-between gap-3">
                <button type="button"
                        @click="close()"
                        class="px-5 py-2.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 transition-colors">
                    Cerrar
                </button>

                <template x-if="data?.edit_url">
                    <a :href="data.edit_url"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-extrabold bg-[#0EA5E9] hover:bg-[#0284C7] text-[#0F172A] shadow-md shadow-sky-500/25 transition-all">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Editar Especialista</span>
                    </a>
                </template>
            </div>

        </div>
    </div>
</div>
