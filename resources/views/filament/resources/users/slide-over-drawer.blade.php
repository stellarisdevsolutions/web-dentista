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
                const res = await $wire.getUserDetails(id);
                if (!res) {
                    this.errorMessage = '{{ __('The requested user was not found or you do not have permission to view it.') }}';
                    this.isLoading = false;
                    return;
                }
                this.data = res;
                this.isLoading = false;
            } catch (err) {
                console.error('Error al cargar datos del usuario:', err);
                this.errorMessage = '{{ __('An unexpected error occurred while loading user information.') }}';
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
     @open-user-drawer.window="open($event.detail?.id)"
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

        <div class="w-screen max-w-lg bg-slate-50 dark:bg-[#060D1A] text-slate-900 dark:text-slate-100 shadow-2xl flex flex-col border-l border-slate-200 dark:border-white/10"
             @click.away="close()">

            <!-- HEADER DEL DRAWER -->
            <div class="px-6 py-5 bg-white dark:bg-[#0A1730] border-b border-slate-200/80 dark:border-white/10 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div style="width: 40px; height: 40px; min-width: 40px; border-radius: 12px; background-color: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; color: #0EA5E9;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-base tracking-tight leading-tight">
                            {{ __('User Details') }}
                        </h3>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ __('Access profile, role, and clinical assignment') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <template x-if="data?.edit_url">
                        <a :href="data.edit_url"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-50 hover:bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300 dark:hover:bg-sky-500/30 transition-colors">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Edit') }}
                        </a>
                    </template>
                    <button @click="close()"
                            type="button"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-white/5 dark:hover:text-white transition-colors"
                            aria-label="{{ __('Close') }}">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- CUERPO DEL DRAWER (CON SCROLL) -->
            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">

                <!-- SKELETON LOADER (0ms feedback) -->
                <div x-show="isLoading" class="space-y-4 animate-pulse">
                    <div class="flex items-center gap-4 p-5 bg-white dark:bg-slate-800 rounded-2xl">
                        <div class="w-16 h-16 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                        <div class="space-y-2 flex-1">
                            <div class="h-5 w-1/2 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                            <div class="h-4 w-3/4 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                        </div>
                    </div>
                    <div class="h-24 bg-slate-200 dark:bg-slate-800 rounded-2xl"></div>
                    <div class="h-20 bg-slate-200 dark:bg-slate-800 rounded-2xl"></div>
                </div>

                <!-- MENSAJE DE ERROR -->
                <div x-show="!isLoading && errorMessage" class="text-center py-10 px-4">
                    <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white" x-text="errorMessage"></p>
                    <button @click="retry()" class="mt-4 px-4 py-2 bg-sky-500 text-white rounded-lg text-xs font-bold hover:bg-sky-600 transition-colors">
                        {{ __('Retry') }}
                    </button>
                </div>

                <!-- CONTENIDO PRINCIPAL DEL USUARIO -->
                <div x-show="!isLoading && data" class="space-y-6">

                    <!-- CARD IDENTIDAD PRINCIPAL -->
                    <div class="bg-white dark:bg-[#0A1730] p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center gap-4">
                        <template x-if="data.dentist?.photo_url">
                            <img :src="data.dentist.photo_url"
                                 :alt="data.name"
                                 class="w-16 h-16 rounded-2xl object-cover shadow-sm border border-slate-200 dark:border-white/10 shrink-0" />
                        </template>

                        <template x-if="!data.dentist?.photo_url">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-xl text-white shadow-sm shrink-0 border border-white/20"
                                 :style="'background-color: ' + (data.role === 'admin' ? '#6366F1' : (data.role === 'dentist' ? '#10B981' : '#0EA5E9'))"
                                 x-text="data.initials || 'US'">
                            </div>
                        </template>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-lg font-black text-slate-900 dark:text-white tracking-tight truncate" x-text="data.name"></h2>
                                <template x-if="data.is_current_user">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300">
                                        {{ __('Your Session') }}
                                    </span>
                                </template>
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block truncate mt-0.5" x-text="data.email"></span>

                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                      :class="data.role === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200/60 dark:bg-rose-500/20 dark:text-rose-300' : (data.role === 'dentist' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-sky-50 text-sky-700 border border-sky-200/60 dark:bg-sky-500/20 dark:text-sky-300')">
                                    <template x-if="data.role === 'admin'">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                        </svg>
                                    </template>
                                    <template x-if="data.role === 'dentist'">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5Zm0 0v-3.675A55.378 55.378 0 0112 8.443m-5.25 6.557c0 1.54 1.157 2.805 2.668 2.969" />
                                        </svg>
                                    </template>
                                    <template x-if="data.role === 'receptionist'">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h7.5" />
                                        </svg>
                                    </template>
                                    <span x-text="data.role_label"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD DE PERMISOS Y PRIVILEGIOS -->
                    <div class="bg-white dark:bg-[#0A1730] p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-xs uppercase tracking-wider">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-sky-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            {{ __('Authorization & Access Level') }}
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal"
                           x-text="data.role_description">
                        </p>
                    </div>

                    <!-- ESPECIALISTA VINCULADO (SI ES ODONTÓLOGO) -->
                    <template x-if="data.dentist">
                        <div class="bg-white dark:bg-[#0A1730] p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 space-y-3">
                            <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-xs uppercase tracking-wider">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Linked Clinical Specialist') }}
                            </div>

                            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5">
                                <template x-if="data.dentist.photo_url">
                                    <img :src="data.dentist.photo_url" class="w-11 h-11 rounded-xl object-cover border border-slate-200/80 shrink-0" />
                                </template>
                                <template x-if="!data.dentist.photo_url">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white font-black text-xs flex items-center justify-center shrink-0"
                                         x-text="data.dentist.initials || 'DR'">
                                    </div>
                                </template>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block truncate" x-text="data.dentist.name"></span>
                                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block truncate" x-text="data.dentist.specialty || data.dentist.title || '{{ __('Surgeon Dentist') }}'"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- METADATOS DE LA CUENTA -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white dark:bg-[#0A1730] p-3.5 rounded-xl border border-slate-200/80 dark:border-white/10 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Registration Date') }}</span>
                            <span class="text-xs font-black text-slate-900 dark:text-white" x-text="data.created_at || '—'"></span>
                        </div>

                        <div class="bg-white dark:bg-[#0A1730] p-3.5 rounded-xl border border-slate-200/80 dark:border-white/10 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Age') }}</span>
                            <span class="text-xs font-black text-sky-600 dark:text-sky-400" x-text="data.created_at_human || '—'"></span>
                        </div>
                    </div>

                </div>

            </div>

            <!-- FOOTER DEL DRAWER -->
            <div class="px-6 py-4 bg-white dark:bg-[#0A1730] border-t border-slate-200/80 dark:border-white/10 flex items-center justify-between shrink-0">
                <button @click="close()"
                        type="button"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                    {{ __('Close') }}
                </button>

                <template x-if="data?.edit_url">
                    <a :href="data.edit_url"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-extrabold bg-sky-500 hover:bg-sky-600 text-white shadow-lg shadow-sky-500/25 transition-all">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Edit User') }}
                    </a>
                </template>
            </div>

        </div>

    </div>

</div>
