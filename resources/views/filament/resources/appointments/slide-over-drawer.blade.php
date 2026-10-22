<div x-data="{
        isOpen: false,
        isLoading: true,
        errorMessage: null,
        data: null,
        currentId: null,

        // Modal de reprogramación
        isRescheduleOpen: false,
        rescheduleDate: '',
        rescheduleTime: '',
        rescheduleServiceId: '',
        rescheduleDentistId: '',
        rescheduleLoading: false,
        rescheduleSlotsLoading: false,
        rescheduleError: null,
        rescheduleCatalogs: { services: [], dentists: [], is_dentist_user: false, user_dentist_id: null },
        rescheduleSlots: { morning_slots: [], afternoon_slots: [], has_available_slots: false },
        rescheduleCatalogsLoaded: false,

        // Modal de cancelación
        isCancelOpen: false,
        cancelReason: '',
        cancelLoading: false,

        init() {
            // Handled cleanly via @open-appointment-drawer.window on root element
        },

        open(id) {
            this.currentId = id;
            this.isOpen = true;
            this.isLoading = true;
            this.errorMessage = null;
            this.data = null;
            this.isRescheduleOpen = false;
            this.isCancelOpen = false;

            document.body.style.overflow = 'hidden';

            this.fetchDetails(id);
        },

        close() {
            this.isOpen = false;
            this.isRescheduleOpen = false;
            this.isCancelOpen = false;
            document.body.style.overflow = '';
        },

        async fetchDetails(id) {
            try {
                const res = await $wire.getAppointmentDetails(id);
                if (!res) {
                    this.errorMessage = '{{ __('The requested appointment was not found or you do not have permission to view it.') }}';
                    this.isLoading = false;
                    return;
                }
                this.data = res;
                this.isLoading = false;
            } catch (err) {
                console.error(err);
                this.errorMessage = '{{ __('An error occurred while loading appointment data.') }}';
                this.isLoading = false;
            }
        },

        retry() {
            if (this.currentId) {
                this.isLoading = true;
                this.errorMessage = null;
                this.fetchDetails(this.currentId);
            }
        },

        // Acciones rápidas desde el drawer
        async confirmAppointment() {
            if (!this.data?.id) return;
            this.isLoading = true;
            try {
                const updated = await $wire.drawerConfirm(this.data.id);
                if (updated) this.data = updated;
            } finally {
                this.isLoading = false;
            }
        },

        async markInProgress() {
            if (!this.data?.id) return;
            this.isLoading = true;
            try {
                const updated = await $wire.drawerMarkInProgress(this.data.id);
                if (updated) this.data = updated;
            } finally {
                this.isLoading = false;
            }
        },

        async completeAppointment() {
            if (!this.data?.id) return;
            this.isLoading = true;
            try {
                const updated = await $wire.drawerComplete(this.data.id);
                if (updated) this.data = updated;
            } finally {
                this.isLoading = false;
            }
        },

        toYmd(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        },

        get filteredRescheduleDentists() {
            if (!this.rescheduleCatalogs?.dentists) return [];
            if (this.rescheduleCatalogs.is_dentist_user) return this.rescheduleCatalogs.dentists;
            if (!this.rescheduleServiceId) return this.rescheduleCatalogs.dentists;

            const selectedService = this.rescheduleCatalogs.services.find(s => s.id == this.rescheduleServiceId);
            if (!selectedService || !selectedService.dentist_ids || selectedService.dentist_ids.length === 0) {
                return this.rescheduleCatalogs.dentists;
            }

            return this.rescheduleCatalogs.dentists.filter(d => selectedService.dentist_ids.includes(d.id));
        },

        get currentSelectedServiceDuration() {
            if (!this.rescheduleServiceId || !this.rescheduleCatalogs?.services) return 45;
            const s = this.rescheduleCatalogs.services.find(srv => srv.id == this.rescheduleServiceId);
            return s ? s.duration_minutes : 45;
        },

        async openReschedule() {
            this.rescheduleDate = this.data?.appointment_date_raw || this.toYmd(new Date());
            this.rescheduleTime = this.data?.appointment_time ? this.data.appointment_time.substring(0, 5) : '';
            this.rescheduleServiceId = this.data?.service_id || '';
            this.rescheduleDentistId = this.data?.dentist_id || '';
            this.rescheduleError = null;
            this.rescheduleSlotsLoading = true;
            this.isRescheduleOpen = true;

            if (!this.rescheduleCatalogsLoaded) {
                try {
                    const catalogs = await $wire.getRescheduleInitialData();
                    if (catalogs) {
                        this.rescheduleCatalogs = catalogs;
                        this.rescheduleCatalogsLoaded = true;
                    }
                } catch (e) {
                    console.error('Error cargando catálogos de reprogramación', e);
                }
            }

            if (this.rescheduleCatalogs.is_dentist_user && this.rescheduleCatalogs.user_dentist_id) {
                this.rescheduleDentistId = this.rescheduleCatalogs.user_dentist_id;
            }

            await this.loadRescheduleSlots();
        },

        closeReschedule() {
            this.isRescheduleOpen = false;
            this.rescheduleError = null;
        },

        async onRescheduleServiceChange() {
            const validDocs = this.filteredRescheduleDentists;
            if (this.rescheduleDentistId && !validDocs.some(d => d.id == this.rescheduleDentistId)) {
                this.rescheduleDentistId = validDocs.length > 0 ? validDocs[0].id : '';
            }
            await this.loadRescheduleSlots();
        },

        async onRescheduleDentistChange() {
            await this.loadRescheduleSlots();
        },

        async onRescheduleDateChange() {
            await this.loadRescheduleSlots();
        },

        async loadRescheduleSlots() {
            if (!this.rescheduleDate) {
                this.rescheduleSlots = { morning_slots: [], afternoon_slots: [], has_available_slots: false };
                return;
            }
            this.rescheduleSlotsLoading = true;
            this.rescheduleError = null;
            try {
                const res = await $wire.getRescheduleAvailableSlots(
                    this.rescheduleDate,
                    this.rescheduleDentistId ? parseInt(this.rescheduleDentistId, 10) : null,
                    this.rescheduleServiceId ? parseInt(this.rescheduleServiceId, 10) : null,
                    this.data?.id || null
                );
                if (res) {
                    this.rescheduleSlots = res;
                    if (res.error) {
                        this.rescheduleError = res.error;
                    }
                }
            } catch (err) {
                console.error('Error al consultar horarios', err);
                this.rescheduleError = '{{ __('Error querying available time slots.') }}';
            } finally {
                this.rescheduleSlotsLoading = false;
            }
        },

        selectRescheduleTime(time) {
            this.rescheduleTime = time;
            this.rescheduleError = null;
        },

        async submitReschedule() {
            if (!this.rescheduleDate || !this.rescheduleTime) {
                this.rescheduleError = '{{ __('Please select a date and an available time slot.') }}';
                return;
            }
            this.rescheduleLoading = true;
            this.rescheduleError = null;
            try {
                const res = await $wire.drawerReschedule(
                    this.data.id,
                    this.rescheduleDate,
                    this.rescheduleTime,
                    this.rescheduleServiceId ? parseInt(this.rescheduleServiceId, 10) : null,
                    this.rescheduleDentistId ? parseInt(this.rescheduleDentistId, 10) : null
                );
                if (res.success && res.data) {
                    this.data = res.data;
                    this.isRescheduleOpen = false;
                } else {
                    this.rescheduleError = res.error || '{{ __('Error rescheduling appointment.') }}';
                }
            } catch (err) {
                this.rescheduleError = '{{ __('Connection error while rescheduling appointment.') }}';
            } finally {
                this.rescheduleLoading = false;
            }
        },

        openCancel() {
            this.cancelReason = '';
            this.isCancelOpen = true;
        },

        closeCancel() {
            this.isCancelOpen = false;
        },

        async submitCancel() {
            this.cancelLoading = true;
            try {
                const updated = await $wire.drawerCancel(this.data.id, this.cancelReason);
                if (updated) this.data = updated;
                this.isCancelOpen = false;
            } catch (err) {
                console.error(err);
            } finally {
                this.cancelLoading = false;
            }
        }
     }"
     x-cloak
     @open-appointment-drawer.window="open($event.detail?.id)"
     @keydown.window.escape="if(isOpen) close()"
     style="display: contents;">

    <!-- BACKDROP OSCURO CON BLUR ELEGANTE -->
    <div x-show="isOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 z-50 bg-[#0A1730]/65 backdrop-blur-sm"
         style="backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
    </div>

    <!-- PANEL LATERAL DERECHO (SLIDE-OVER) CON FONDO AZUL MARINO DE SIDEBAR (#0A1730) EN HEADER -->
    <div x-show="isOpen"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-250"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 w-full max-w-lg bg-white dark:bg-[#0A1730] shadow-2xl flex flex-col border-l border-slate-200/90 dark:border-white/10"
         style="box-shadow: -15px 0 45px -5px rgba(10, 23, 48, 0.35);">

        <!-- HEADER CON EL AZUL DEL SIDEBAR (#0A1730) Y ACENTOS CELESTES VIBRANTES -->
        <div class="px-6 py-5 border-b border-white/10 bg-[#0A1730] text-white shrink-0 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#0EA5E9] text-[#0F172A] flex items-center justify-center font-extrabold shadow-md shadow-sky-500/30">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-extrabold text-white leading-tight">
                            {{ __('Appointment Detail') }}
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full font-mono text-xs font-black bg-[#16294D] text-[#38BDF8] border border-sky-400/30 shadow-inner"
                              x-text="data?.code ? data.code : '...'">
                        </span>
                    </div>
                    <p class="text-xs text-sky-200/90 font-medium mt-0.5">
                        {{ __('Clinical and operational appointment management') }}
                    </p>
                </div>
            </div>

            <!-- Botón Cerrar (con hover sutil estilo sidebar) -->
            <button type="button"
                    @click="close()"
                    aria-label="{{ __('Close panel') }}"
                    class="w-9 h-9 rounded-full flex items-center justify-center text-slate-300 hover:text-white hover:bg-[#16294D] transition-all">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- CONTENIDO SCROLLABLE CON CONTRASTE ALTO -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-[#F1F5F9] dark:bg-[#060e1f]">

            <!-- 1. SKELETON LOADER -->
            <div x-show="isLoading" class="space-y-4 animate-pulse">
                <!-- Skeleton Paciente Row -->
                <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-[14px] bg-slate-200 dark:bg-slate-700 shrink-0"></div>
                    <div class="space-y-2 flex-1">
                        <div class="h-4.5 w-44 bg-slate-300 dark:bg-slate-600 rounded-md"></div>
                        <div class="h-3.5 w-28 bg-slate-200 dark:bg-slate-700 rounded-md"></div>
                    </div>
                    <div class="h-7 w-24 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
                </div>

                <!-- Skeleton Bloques Principales -->
                <div class="grid grid-cols-2 gap-3.5">
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 space-y-2.5">
                        <div class="h-3.5 w-20 bg-slate-200 dark:bg-slate-700 rounded"></div>
                        <div class="h-6 w-28 bg-slate-300 dark:bg-slate-600 rounded"></div>
                        <div class="h-3.5 w-20 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 space-y-2.5">
                        <div class="h-3.5 w-24 bg-slate-200 dark:bg-slate-700 rounded"></div>
                        <div class="h-6 w-32 bg-slate-300 dark:bg-slate-600 rounded"></div>
                        <div class="h-3.5 w-16 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 space-y-3">
                    <div class="h-3.5 w-32 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    <div class="h-4 w-full bg-slate-200 dark:bg-slate-700 rounded"></div>
                    <div class="h-4 w-4/5 bg-slate-200 dark:bg-slate-700 rounded"></div>
                </div>
            </div>

            <!-- 2. ESTADO DE ERROR O VACÍO -->
            <div x-show="!isLoading && errorMessage" class="py-12 px-4 text-center space-y-4">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 border border-amber-200 dark:border-amber-800/40 flex items-center justify-center shadow-inner">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="font-extrabold text-slate-900 dark:text-white text-base">
                        {{ __('Could not load appointment') }}
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium max-w-xs mx-auto" x-text="errorMessage"></p>
                </div>
                <div class="flex items-center justify-center gap-2.5 pt-2">
                    <button type="button"
                            @click="retry()"
                            class="px-5 py-2.5 rounded-full text-xs font-extrabold bg-[#0EA5E9] hover:bg-[#0284C7] text-[#0F172A] shadow-md shadow-sky-500/25 transition-all">
                        {{ __('Retry') }}
                    </button>
                    <button type="button"
                            @click="close()"
                            class="px-4 py-2.5 rounded-full text-xs font-bold bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>

            <!-- 3. DETALLE DE LA CITA CON TIPOGRAFÍAS NOTORIAS Y LEGIBLES -->
            <div x-show="!isLoading && !errorMessage && data" class="space-y-3.5">

                <!-- TARJETA PACIENTE -->
                <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm transition-all">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Avatar de Paciente idéntico al de la tabla -->
                            <div style="width: 48px; height: 48px; min-width: 48px; max-width: 48px; border-radius: 14px; background-color: #0EA5E9; color: #0F172A; font-weight: 800; font-size: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);"
                                 x-text="data?.patient_initials">
                            </div>

                            <div class="min-w-0">
                                <span class="text-[11px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8]">
                                    {{ __('Patient') }}
                                </span>
                                <h4 class="font-extrabold text-slate-900 dark:text-white text-lg leading-snug truncate"
                                    x-text="data?.patient_name">
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <template x-if="data?.patient_phone">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                              x-text="'{{ __('Phone:') }} ' + data?.patient_phone">
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Estado con Badge Aura -->
                        <div class="shrink-0">
                            <span :class="'aura-status-badge aura-status-' + data?.status"
                                  style="user-select: none; font-weight: 800; font-size: 12px;"
                                  x-text="data?.status_label">
                            </span>
                        </div>
                    </div>

                    <!-- Información de Contacto: Correo -->
                    <template x-if="data?.patient_email">
                        <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-white/10 flex items-center justify-between text-xs">
                            <span class="text-slate-600 dark:text-slate-300 font-bold">{{ __('Email address:') }}</span>
                            <span class="font-extrabold text-slate-900 dark:text-white" x-text="data?.patient_email"></span>
                        </div>
                    </template>
                </div>

                <!-- CITA PROGRAMADA: ENCABEZADO VIBRANTE Y LEGIBLE -->
                <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <!-- 'CITA PROGRAMADA' ahora en Azul Intenso de Marca (#0A74B8 / #38BDF8) en vez de plomo claro -->
                        <span class="text-xs font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] flex items-center gap-1.5">
                            <svg width="16" height="16" class="text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ __('Scheduled Appointment') }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#E0F2FE] dark:bg-[#16294D] text-[#0A74B8] dark:text-[#38BDF8] border border-sky-300/40 dark:border-sky-500/20"
                                x-text="data?.duration">
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-1">
                        <div>
                            <span class="text-xs text-slate-600 dark:text-slate-300 font-bold">{{ __('Schedule') }}</span>
                            <div class="agenda-time-text font-black text-2xl mt-0.5 leading-none text-slate-900 dark:text-white"
                                 x-text="data?.appointment_time">
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-slate-600 dark:text-slate-300 font-bold">{{ __('Date') }}</span>
                            <div class="font-extrabold text-slate-900 dark:text-white text-sm mt-1 capitalize leading-snug"
                                 x-text="data?.appointment_date">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TRATAMIENTO Y ESPECIALISTA: TITULOS NOTORIOS (#0A74B8) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    
                    <!-- Tarjeta Tratamiento -->
                    <div class="p-4.5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] block">
                                {{ __('Treatment') }}
                            </span>
                            <div class="agenda-treatment-cell text-sm font-extrabold mt-1 leading-snug text-slate-900 dark:text-white"
                                 x-text="data?.service_name">
                            </div>
                        </div>
                        <template x-if="data?.service_price">
                            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-white/10 text-xs text-slate-700 dark:text-slate-300 font-bold"
                                 x-text="'{{ __('From') }} ' + data?.service_price">
                            </div>
                        </template>
                    </div>

                    <!-- Tarjeta Especialista -->
                    <div class="p-4.5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] block">
                                {{ __('Specialist') }}
                            </span>
                            <div class="agenda-specialist-name text-sm font-extrabold mt-1 leading-snug"
                                 x-text="data?.dentist_name">
                            </div>
                        </div>
                        <template x-if="data?.dentist_specialty">
                            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-white/10 text-xs text-slate-600 dark:text-slate-300 font-semibold truncate"
                                 x-text="data?.dentist_specialty">
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SÍNTOMAS / MOTIVO INFORMADO POR EL PACIENTE -->
                <template x-if="data?.symptoms">
                    <div class="p-4.5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-1.5">
                        <span class="text-[11px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] flex items-center gap-1.5">
                            <svg width="15" height="15" class="text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Reported Reason / Symptoms') }}
                        </span>
                        <p class="text-xs font-semibold leading-relaxed text-slate-800 dark:text-slate-200" x-text="data?.symptoms"></p>
                    </div>
                </template>

                <!-- NOTAS DE RECEPCIÓN -->
                <div class="p-4.5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-1.5">
                    <span class="text-[11px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] flex items-center gap-1.5">
                        <svg width="15" height="15" class="text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('Reception Notes') }}
                    </span>
                    <p class="text-xs font-semibold leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line"
                       x-text="data?.internal_notes || '{{ __('No reception notes registered for this appointment.') }}'">
                    </p>
                </div>

                <!-- INFORMACIÓN CLÍNICA Y OPERATIVA (Datos reales) -->
                <div class="p-4.5 rounded-2xl bg-white dark:bg-[#0A1730] border border-slate-200/90 dark:border-white/10 shadow-sm space-y-2.5 text-xs">
                    <span class="text-[11px] font-black uppercase tracking-wider text-[#0A74B8] dark:text-[#38BDF8] block mb-1">
                        {{ __('Clinical Information and Record') }}
                    </span>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-white/10">
                        <span class="text-slate-600 dark:text-slate-300 font-bold">{{ __('Patient type') }}</span>
                        <span :class="data?.is_first_visit ? 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 border-sky-300/50' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 border-slate-200 dark:border-white/10'"
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border"
                              x-text="data?.is_first_visit_label">
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-white/10">
                        <span class="text-slate-600 dark:text-slate-300 font-bold">{{ __('Booking date') }}</span>
                        <span class="font-extrabold text-slate-900 dark:text-white" x-text="data?.created_at_formatted"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER ELEGANTE CON DOS FILAS: ACCIONES CLÍNICAS ARRIBA Y [EDITAR / CERRAR] EN LA ÚLTIMA FILA -->
        <template x-if="!isLoading && !errorMessage && data">
            <div class="p-4 sm:p-5 border-t border-slate-200/80 dark:border-white/10 bg-white dark:bg-[#0A1730] flex flex-col gap-3 shrink-0">
                
                <!-- FILA 1: Acciones Clínicas de Estado (Cancelar Cita, Reprogramar, En Sala, Confirmar, Completar) -->
                <div class="flex items-center justify-end gap-2 flex-wrap">
                    
                    <!-- Cancelar Cita (Acción clínica destructiva con confirmación) -->
                    <template x-if="data?.can_cancel">
                        <button type="button"
                                @click="openCancel()"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-black bg-[#EF4444] hover:bg-[#DC2626] text-white shadow-md shadow-rose-500/30 transition-all hover:scale-102 active:scale-98"
                                title="{{ __('Cancel this clinical appointment') }}">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ __('Cancel Appointment') }}</span>
                        </button>
                    </template>

                    <!-- Reprogramar Cita -->
                    <template x-if="data?.can_reschedule">
                        <button type="button"
                                @click="openReschedule()"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-black bg-[#F59E0B] hover:bg-[#D97706] text-white shadow-md shadow-amber-500/30 transition-all hover:scale-102 active:scale-98">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ __('Reschedule') }}</span>
                        </button>
                    </template>

                    <!-- Marcar En Sala -->
                    <template x-if="data?.can_mark_in_progress">
                        <button type="button"
                                @click="markInProgress()"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-black bg-[#0EA5E9] hover:bg-[#0284C7] text-[#0F172A] shadow-md shadow-sky-500/30 transition-all hover:scale-102 active:scale-98">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            </svg>
                            <span>{{ __('In Clinic') }}</span>
                        </button>
                    </template>

                    <!-- Confirmar Cita -->
                    <template x-if="data?.can_confirm">
                        <button type="button"
                                @click="confirmAppointment()"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-black bg-[#10B981] hover:bg-[#059669] text-white shadow-md shadow-emerald-500/30 transition-all hover:scale-102 active:scale-98">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('Confirm') }}</span>
                        </button>
                    </template>

                    <!-- Completada -->
                    <template x-if="data?.can_complete">
                        <button type="button"
                                @click="completeAppointment()"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-black bg-[#10B981] hover:bg-[#059669] text-white shadow-md shadow-emerald-500/30 transition-all hover:scale-102 active:scale-98">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('Complete') }}</span>
                        </button>
                    </template>
                </div>

                <!-- FILA 2 (ÚLTIMA FILA): Acciones del Panel (Editar y Cerrar) con fondo azul del sidebar (#0A1730) -->
                <div class="pt-3 border-t border-slate-100 dark:border-white/10 flex items-center justify-between gap-2">
                    
                    <!-- Botón Editar con Azul del Sidebar (#0A1730) -->
                    <div>
                        <template x-if="data?.can_edit">
                            <a :href="data?.edit_url"
                               class="inline-flex items-center gap-1.5 px-4.5 py-2.5 rounded-full text-xs font-extrabold text-white bg-[#0A1730] hover:bg-[#16294D] border border-white/15 shadow-sm transition-all hover:scale-102 active:scale-98"
                               title="{{ __('Edit all appointment fields') }}">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>{{ __('Edit') }}</span>
                            </a>
                        </template>
                    </div>

                    <!-- Botón Cerrar con Azul del Sidebar (#0A1730) -->
                    <button type="button"
                            @click="close()"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-xs font-extrabold text-white bg-[#0A1730] hover:bg-[#16294D] border border-white/15 shadow-sm transition-all hover:scale-102 active:scale-98"
                            title="{{ __('Close this panel') }}">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        <span>{{ __('Close') }}</span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- MODAL DE REPROGRAMACIÓN (IN-DRAWER CON TRATAMIENTO, ESPECIALISTA, FECHA Y SLOTS) -->
    <div x-show="isRescheduleOpen"
         x-transition:enter="transition-opacity ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-[#0A1730]/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
        
        <div @click.outside="closeReschedule()"
             class="bg-white dark:bg-[#0A1730] rounded-3xl max-w-xl w-full p-5 sm:p-7 shadow-2xl border border-slate-200 dark:border-white/10 space-y-5 my-auto max-h-[92vh] flex flex-col">
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between border-b border-slate-100 dark:border-white/10 pb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path>
                            <path d="M16 21h5v-5"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 dark:text-white text-lg tracking-tight">
                            {{ __('Reschedule Appointment') }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ __('Appointment') }} <strong class="text-sky-600 dark:text-sky-400" x-text="data?.code"></strong> · <span class="font-bold text-slate-700 dark:text-slate-200" x-text="data?.patient_name"></span>
                        </p>
                    </div>
                </div>
                <button type="button" 
                        @click="closeReschedule()" 
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    ✕
                </button>
            </div>

            <!-- Error Banner -->
            <template x-if="rescheduleError">
                <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span x-text="rescheduleError"></span>
                </div>
            </template>

            <!-- Scrollable Body -->
            <div class="space-y-4 text-xs overflow-y-auto pr-1 flex-1">
                
                <!-- 1 & 2: Tratamiento y Especialista en Grid 2 columnas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <!-- 1. Tratamiento Dental -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="font-black text-slate-800 dark:text-slate-200">{{ __('1. Dental Treatment') }}</label>
                            <span class="text-[10px] text-sky-600 dark:text-sky-400 font-extrabold" x-text="currentSelectedServiceDuration + ' min'"></span>
                        </div>
                        <select x-model="rescheduleServiceId"
                                @change="onRescheduleServiceChange()"
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#16294D] font-bold text-slate-900 dark:text-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all cursor-pointer">
                            <template x-for="srv in rescheduleCatalogs.services" :key="srv.id">
                                <option :value="srv.id" x-text="srv.name + ' · ' + srv.duration_minutes + ' min'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 2. Especialista -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="font-black text-slate-800 dark:text-slate-200">{{ __('2. Specialist') }}</label>
                            <span class="text-[10px] text-slate-400 font-medium">{{ __('Assigned') }}</span>
                        </div>
                        <select x-model="rescheduleDentistId"
                                @change="onRescheduleDentistChange()"
                                :disabled="rescheduleCatalogs.is_dentist_user"
                                class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#16294D] font-bold text-slate-900 dark:text-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">{{ __('First available specialist (unified schedule)') }}</option>
                            <template x-for="doc in filteredRescheduleDentists" :key="doc.id">
                                <option :value="doc.id" x-text="doc.name + (doc.specialty ? ' · ' + doc.specialty : '')"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- 3. Fecha de la Cita -->
                <div>
                    <label class="block font-black text-slate-800 dark:text-slate-200 mb-1.5">{{ __('3. New Appointment Date') }}</label>
                    <input type="date"
                           x-model="rescheduleDate"
                           @change="onRescheduleDateChange()"
                           :min="toYmd(new Date())"
                           class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#16294D] font-bold text-slate-900 dark:text-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition-all cursor-pointer" />
                </div>

                <!-- 4. Horarios Disponibles (Diseño Pill Buttons Idéntico a la Web de Reservas) -->
                <div class="pt-2 border-t border-slate-100 dark:border-white/10 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="block font-black text-slate-900 dark:text-white text-xs">
                                {{ __('4. Available Time Slots') }}
                            </span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ __('Selected Time:') }} <strong class="text-sky-600 dark:text-sky-400" x-text="rescheduleTime ? rescheduleTime : '{{ __('None') }}'"></strong>
                            </span>
                        </div>
                        <div x-show="rescheduleSlotsLoading" class="flex items-center gap-1.5 text-[11px] text-sky-600 dark:text-sky-400 font-bold">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"></circle>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>{{ __('Checking shifts...') }}</span>
                        </div>
                    </div>

                    <!-- Si está cargando slots -->
                    <div x-show="rescheduleSlotsLoading" class="py-6 text-center text-slate-400 space-y-2">
                        <div class="inline-block animate-pulse text-xs font-semibold">{{ __('Loading specialist availability...') }}</div>
                    </div>

                    <!-- Si no hay slots disponibles en esa fecha -->
                    <div x-show="!rescheduleSlotsLoading && !rescheduleSlots.has_available_slots" 
                         class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-800 dark:text-amber-300 text-xs text-center space-y-1">
                        <p class="font-bold">{{ __('No available schedules for this date.') }}</p>
                        <p class="text-[11px] text-amber-700 dark:text-amber-400 font-medium">{{ __('The specialist has no shifts configured or they are fully booked. Please choose another date.') }}</p>
                    </div>

                    <!-- Turnos Mañana y Tarde -->
                    <div x-show="!rescheduleSlotsLoading && rescheduleSlots.has_available_slots" class="space-y-4 pt-1">
                        <!-- Turno Mañana -->
                        <div x-show="rescheduleSlots.morning_slots && rescheduleSlots.morning_slots.length > 0">
                            <div class="flex items-center gap-1.5 mb-2 text-slate-700 dark:text-slate-300 font-extrabold text-[11px] uppercase tracking-wider">
                                <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v3"></path>
                                    <path d="M4.93 4.93l2.12 2.12"></path>
                                    <path d="M2 12h3"></path>
                                    <path d="M19.07 4.93l-2.12 2.12"></path>
                                    <path d="M22 12h-3"></path>
                                    <path d="M5 16a7 7 0 0 1 14 0"></path>
                                    <path d="M2 20h20"></path>
                                </svg>
                                <span>{{ __('Morning Shift') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="slot in rescheduleSlots.morning_slots" :key="slot.time">
                                    <button type="button"
                                            @click="slot.available && selectRescheduleTime(slot.time)"
                                            :disabled="!slot.available"
                                            :title="slot.available ? '{{ __('Select') }} ' + slot.label : '{{ __('Time slot unavailable or outside shift') }}'"
                                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5"
                                            :class="{
                                                'bg-[#0A1730] text-white shadow-md ring-2 ring-sky-500 dark:bg-sky-500 dark:text-white': rescheduleTime === slot.time,
                                                'bg-white dark:bg-[#16294D] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/10 hover:border-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/50 cursor-pointer': rescheduleTime !== slot.time && slot.available,
                                                'bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 line-through cursor-not-allowed opacity-50 border border-transparent': !slot.available
                                            }">
                                        <template x-if="rescheduleTime === slot.time">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </template>
                                        <span x-text="slot.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Turno Tarde -->
                        <div x-show="rescheduleSlots.afternoon_slots && rescheduleSlots.afternoon_slots.length > 0">
                            <div class="flex items-center gap-1.5 mb-2 text-slate-700 dark:text-slate-300 font-extrabold text-[11px] uppercase tracking-wider">
                                <svg class="w-4 h-4 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13a7 7 0 0 1 14 0"></path>
                                    <path d="M2 17h20"></path>
                                    <path d="M4 21h16"></path>
                                    <path d="M12 2v3"></path>
                                    <path d="m4.93 4.93 2.12 2.12"></path>
                                    <path d="m19.07 4.93-2.12 2.12"></path>
                                </svg>
                                <span>{{ __('Afternoon Shift') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="slot in rescheduleSlots.afternoon_slots" :key="slot.time">
                                    <button type="button"
                                            @click="slot.available && selectRescheduleTime(slot.time)"
                                            :disabled="!slot.available"
                                            :title="slot.available ? '{{ __('Select') }} ' + slot.label : '{{ __('Time slot unavailable or outside shift') }}'"
                                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5"
                                            :class="{
                                                'bg-[#0A1730] text-white shadow-md ring-2 ring-sky-500 dark:bg-sky-500 dark:text-white': rescheduleTime === slot.time,
                                                'bg-white dark:bg-[#16294D] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/10 hover:border-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/50 cursor-pointer': rescheduleTime !== slot.time && slot.available,
                                                'bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 line-through cursor-not-allowed opacity-50 border border-transparent': !slot.available
                                            }">
                                        <template x-if="rescheduleTime === slot.time">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </template>
                                        <span x-text="slot.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between border-t border-slate-100 dark:border-white/10 pt-4 flex-shrink-0">
                <button type="button"
                        @click="closeReschedule()"
                        class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button"
                        @click="submitReschedule()"
                        :disabled="rescheduleLoading || !rescheduleTime || !rescheduleDate"
                        class="px-6 py-2.5 rounded-full text-xs font-extrabold bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-amber-600/20 transition-all hover:scale-102 active:scale-98">
                    <svg x-show="rescheduleLoading" class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="rescheduleLoading ? '{{ __('Rescheduling...') }}' : '{{ __('Save New Date & Time') }}'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DE CANCELACIÓN (IN-DRAWER) -->
    <div x-show="isCancelOpen"
         x-transition:enter="transition-opacity ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-[#0A1730]/75 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.outside="closeCancel()"
             class="bg-white dark:bg-[#0A1730] rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 dark:border-white/10 space-y-4">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-3">
                <h4 class="font-extrabold text-slate-900 dark:text-white text-base">
                    {{ __('Cancel Appointment') }}
                </h4>
                <button type="button" @click="closeCancel()" class="text-slate-400 hover:text-slate-600">
                    ✕
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <p class="text-slate-600 dark:text-slate-400">
                    {{ __('Are you sure you want to cancel the appointment for') }} <strong class="text-slate-900 dark:text-white" x-text="data?.patient_name"></strong>?
                </p>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Reason (optional)') }}</label>
                    <textarea x-model="cancelReason"
                              rows="2"
                              placeholder="{{ __('e.g. Patient will reschedule next week...') }}"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-[#16294D] font-medium text-slate-800 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3">
                <button type="button"
                        @click="closeCancel()"
                        class="px-4 py-2 rounded-full text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100">
                    {{ __('Go Back') }}
                </button>
                <button type="button"
                        @click="submitCancel()"
                        :disabled="cancelLoading"
                        class="px-5 py-2 rounded-full text-xs font-extrabold bg-rose-600 hover:bg-rose-500 text-white flex items-center gap-1.5 disabled:opacity-50 shadow-sm">
                    <span x-text="cancelLoading ? '{{ __('Cancelling...') }}' : '{{ __('Yes, Cancel Appointment') }}'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
