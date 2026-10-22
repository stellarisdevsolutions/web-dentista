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
                const res = await $wire.getPostDetails(id);
                if (!res) {
                    this.errorMessage = '{{ __('The requested article was not found or you do not have permission to view it.') }}';
                    this.isLoading = false;
                    return;
                }
                this.data = res;
                this.isLoading = false;
            } catch (err) {
                console.error('Error al cargar datos del artículo:', err);
                this.errorMessage = '{{ __('An unexpected error occurred while loading article information.') }}';
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
     @open-post-drawer.window="open($event.detail?.id)"
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white text-base tracking-tight leading-tight">
                            {{ __('Article Details') }}
                        </h3>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ __('Editorial reading, status and web publication') }}
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
                    <div class="w-full aspect-[16/9] bg-slate-200 dark:bg-slate-800 rounded-2xl"></div>
                    <div class="h-6 w-3/4 bg-slate-200 dark:bg-slate-800 rounded-lg"></div>
                    <div class="h-4 w-1/2 bg-slate-200 dark:bg-slate-800 rounded-lg"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="h-16 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-16 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                    <div class="h-32 bg-slate-200 dark:bg-slate-800 rounded-2xl"></div>
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

                <!-- CONTENIDO PRINCIPAL DEL ARTÍCULO -->
                <div x-show="!isLoading && data" class="space-y-6">

                    <!-- FOTOGRAFÍA DE PORTADA 16:9 -->
                    <div class="w-full aspect-[16/9] rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-white/10 shadow-sm relative group">
                        <img :src="data.cover_image_url"
                             :alt="data.title"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    </div>

                    <!-- BADGES DE ESTADO, CATEGORÍA Y TIEMPO DE LECTURA -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-50 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300 border border-sky-200/60 dark:border-sky-500/30"
                              x-text="data.category">
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                              :class="data.is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'">
                            <span class="w-2 h-2 rounded-full" :class="data.is_published ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                            <span x-text="data.is_published ? '{{ __('Published on the Web') }}' : '{{ __('Private Draft') }}'"></span>
                        </span>

                        <template x-if="data.reading_time">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-500/20 dark:text-amber-300">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="data.reading_time + ' ' + '{{ __('min read') }}'"></span>
                            </span>
                        </template>
                    </div>

                    <!-- TÍTULO PRINCIPAL -->
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-tight" x-text="data.title"></h2>
                        <template x-if="data.excerpt">
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed font-normal" x-text="data.excerpt"></p>
                        </template>
                    </div>

                    <!-- ENLACE WEB PÚBLICO (SLUG) -->
                    <div class="p-3.5 bg-white dark:bg-[#0A1730] rounded-xl border border-slate-200/80 dark:border-white/10 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Public URL') }}</span>
                                <span class="text-xs font-mono font-semibold text-slate-700 dark:text-slate-300 truncate block" x-text="data.display_url || (window.location.host + '/blog/' + data.slug)"></span>
                            </div>
                        </div>
                        <template x-if="data.public_url">
                            <a :href="data.public_url"
                               target="_blank"
                               class="p-2 rounded-lg text-slate-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-white/5 transition-colors shrink-0"
                               title="{{ __('View on website') }}">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </template>
                    </div>

                    <!-- FECHA DE PUBLICACIÓN Y TIEMPOS -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white dark:bg-[#0A1730] p-3.5 rounded-xl border border-slate-200/80 dark:border-white/10 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Publication Date') }}</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white" x-text="data.published_at || '{{ __('No date') }}'"></span>
                        </div>

                        <div class="bg-white dark:bg-[#0A1730] p-3.5 rounded-xl border border-slate-200/80 dark:border-white/10 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">{{ __('Age') }}</span>
                            <span class="text-sm font-black text-sky-600 dark:text-sky-400" x-text="data.published_at_human || '—'"></span>
                        </div>
                    </div>

                    <!-- CONTENIDO COMPLETO DEL ARTÍCULO (VISTA PREVIA) -->
                    <div class="bg-white dark:bg-[#0A1730] p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 space-y-3">
                        <div class="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-xs uppercase tracking-wider">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-sky-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            {{ __('Article Content') }}
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal prose dark:prose-invert max-w-none"
                             x-html="data.content || '<p class=\'italic text-slate-400\'>{{ __('No written content yet.') }}</p>'">
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
                        {{ __('Edit Article') }}
                    </a>
                </template>
            </div>

        </div>

    </div>

</div>
