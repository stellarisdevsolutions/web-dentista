<div 
    x-data="mediaLibraryModal()" 
    x-show="isOpen" 
    x-cloak 
    @open-media-library.window="openModal($event.detail)"
    @keydown.escape.window="closeModal()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
>
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

    <!-- Modal Dialog Card -->
    <div 
        class="w-full max-w-5xl overflow-hidden text-left transition-all transform bg-slate-900 border border-slate-800 shadow-2xl rounded-2xl flex flex-col h-[680px] relative z-10"
        @click.stop
    >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <rect width="18" height="18" x="3" y="3" rx="2" stroke-width="2"/>
                            <circle cx="9" cy="9" r="2" stroke-width="2"/>
                            <path stroke-width="2" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-tight">Galería de Imágenes Web</h2>
                        <p class="text-xs text-slate-400">Selecciona o sube imágenes para personalizar las secciones del sitio web.</p>
                    </div>
                </div>

                <!-- Tabs: Galería vs Subir -->
                <div class="flex items-center gap-2">
                    <div class="bg-slate-900 border border-slate-800 p-1 rounded-xl flex items-center gap-1">
                        <button 
                            type="button"
                            @click="activeTab = 'library'" 
                            :class="activeTab === 'library' ? 'bg-sky-500 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Galería Web
                        </button>
                        <button 
                            type="button"
                            @click="activeTab = 'upload'" 
                            :class="activeTab === 'upload' ? 'bg-sky-500 text-white' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Subir Imágenes
                        </button>
                    </div>

                    <button @click="closeModal()" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 flex overflow-hidden">
                
                <!-- TAB 1: BIBLIOTECA DE MEDIOS -->
                <div x-show="activeTab === 'library'" class="flex-1 flex overflow-hidden w-full">
                    
                    <!-- Left: Grid of Assets -->
                    <div class="flex-1 flex flex-col border-r border-slate-800 overflow-hidden">
                        <!-- Gallery Header & Actions (Sin buscador por renombrado automático de archivos) -->
                        <div class="p-3.5 border-b border-slate-800 bg-slate-900/60 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-bold text-white uppercase tracking-wider">Imágenes del Editor Web</span>
                                <span class="px-2 py-0.5 rounded-full bg-slate-800 text-[11px] font-semibold text-sky-400" x-text="`${assets.length} disponibles`"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="activeTab = 'upload'" 
                                    class="text-xs text-white bg-sky-500 hover:bg-sky-600 px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 font-bold shadow-sm cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>Subir Nueva Imagen</span>
                                </button>
                            </div>
                        </div>

                        <!-- Grid Area -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <!-- Loading State -->
                            <div x-show="isLoading" class="h-full flex items-center justify-center text-slate-400 gap-2">
                                <svg class="w-5 h-5 animate-spin text-sky-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span class="text-xs">Cargando galería...</span>
                            </div>

                            <!-- Empty State -->
                            <div x-show="!isLoading && assets.length === 0" class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="18" height="18" x="3" y="3" rx="2" stroke-width="2"/><circle cx="9" cy="9" r="2" stroke-width="2"/><path stroke-width="2" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <h3 class="text-xs font-bold text-white mb-1">Aún no hay imágenes en la galería del editor</h3>
                                <p class="text-[11px] text-slate-400 max-w-xs mb-4">Sube fotos de la clínica para utilizarlas en las distintas secciones de tu sitio web.</p>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="activeTab = 'upload'" class="bg-sky-500 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-sky-600 transition-colors">
                                        Subir primera imagen
                                    </button>
                                </div>
                            </div>

                            <!-- Assets Grid -->
                            <div x-show="!isLoading && assets.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                <template x-for="item in assets" :key="item.id">
                                    <div 
                                        @click="selectAsset(item)"
                                        :class="selectedAsset?.id === item.id ? 'ring-2 ring-sky-500 border-sky-400 bg-sky-500/10' : 'border-slate-800 hover:border-slate-700 bg-slate-950'"
                                        class="group relative aspect-square rounded-xl border overflow-hidden cursor-pointer transition-all flex flex-col justify-between"
                                    >
                                        <img :src="item.url" :alt="'{{ __('images.media_library_item', ['clinic' => 'Aura Dental Studio']) }}'" class="w-full h-full object-cover">
                                        
                                        <!-- Selected Badge Check -->
                                        <div x-show="selectedAsset?.id === item.id" class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-sky-500 text-white flex items-center justify-center shadow">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>

                                        <!-- Dimensions & Size on hover (sin nombre de archivo) -->
                                        <div class="absolute inset-x-0 bottom-0 bg-slate-950/90 p-1.5 text-[10px] text-slate-200 truncate opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-between">
                                            <span class="font-bold text-sky-400" x-text="item.format || (item.mime_type ? item.mime_type.replace('image/', '').toUpperCase() : 'IMG')"></span>
                                            <span class="text-slate-300 font-medium" x-text="item.formatted_size"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Asset Details Sidebar (300px) -->
                    <div class="w-76 bg-slate-950 p-4 flex flex-col justify-between shrink-0 overflow-y-auto">
                        <div x-show="selectedAsset" class="space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Detalles de la Imagen</h3>
                            
                            <!-- Thumbnail Preview -->
                            <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-800 flex items-center justify-center relative group">
                                <img :src="selectedAsset?.url" :alt="'{{ __('images.media_library_item', ['clinic' => 'Aura Dental Studio']) }}'" class="max-h-full max-w-full object-contain">
                            </div>

                            <!-- Quick Action: Copy URL -->
                            <button 
                                type="button" 
                                @click="copyAssetUrl(selectedAsset.url)" 
                                class="w-full bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white px-3 py-1.5 rounded-xl text-xs font-medium transition-colors flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span x-text="isCopied ? '¡Enlace Copiado!' : 'Copiar URL Directa'"></span>
                            </button>

                            <!-- Especificaciones de la Imagen (Únicamente Formato, Dimensiones y Tamaño) -->
                            <div class="space-y-2.5 text-xs pt-2 border-t border-slate-900 bg-slate-900/50 p-3 rounded-xl border border-slate-800/80">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400 font-medium">Formato</span>
                                    <span class="text-white font-bold tracking-wider" x-text="selectedAsset?.format || (selectedAsset?.mime_type ? selectedAsset.mime_type.replace('image/', '').toUpperCase() : 'WEBP')"></span>
                                </div>
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400 font-medium">Dimensiones</span>
                                    <span class="text-white font-bold" x-text="selectedAsset?.width && selectedAsset?.height ? `${selectedAsset.width} × ${selectedAsset.height} px` : 'Optimizada'"></span>
                                </div>
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400 font-medium">Tamaño</span>
                                    <span class="text-white font-bold" x-text="selectedAsset?.formatted_size"></span>
                                </div>
                            </div>

                            <!-- Delete Action -->
                            <div class="pt-1">
                                <button 
                                    type="button" 
                                    @click="requestDeleteAsset(selectedAsset)" 
                                    class="text-xs text-rose-400 hover:text-rose-300 font-medium transition-colors flex items-center gap-1 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar de la galería
                                </button>
                            </div>
                        </div>

                        <div x-show="!selectedAsset" class="h-full flex flex-col items-center justify-center text-center text-slate-500 p-4">
                            <svg class="w-8 h-8 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                            <p class="text-xs">Selecciona una imagen de la galería para ver sus especificaciones e insertarla.</p>
                        </div>

                        <!-- Footer Action Button -->
                        <div class="pt-3 border-t border-slate-800">
                            <button 
                                type="button" 
                                @click="confirmSelection()" 
                                :disabled="!selectedAsset"
                                class="w-full bg-emerald-500 hover:bg-emerald-600 disabled:opacity-30 disabled:cursor-not-allowed text-white py-2.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-emerald-500/20 cursor-pointer"
                            >
                                Usar esta Imagen
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: SUBIR ARCHIVOS (Drag & Drop con soporte múltiple) -->
                <div x-show="activeTab === 'upload'" class="flex-1 flex flex-col items-center justify-center p-8 bg-slate-950">
                    <div 
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                        :class="isDragging ? 'border-sky-500 bg-sky-500/10' : 'border-slate-800 hover:border-slate-700 bg-slate-900/50'"
                        class="w-full max-w-xl p-10 border-2 border-dashed rounded-3xl text-center transition-all flex flex-col items-center justify-center cursor-pointer"
                        @click="$refs.fileInputModal.click()"
                    >
                        <input 
                            type="file" 
                            x-ref="fileInputModal" 
                            class="hidden" 
                            multiple
                            accept="image/png,image/jpeg,image/webp,image/svg+xml" 
                            @change="handleFileInput($event)"
                        >
                        
                        <div class="w-16 h-16 rounded-3xl bg-slate-800 text-sky-400 flex items-center justify-center mb-4 shadow-inner">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>

                        <h3 class="text-sm font-bold text-white mb-1">Arrastra aquí una o varias imágenes</h3>
                        <p class="text-xs text-slate-400 mb-4">o haz clic para explorar en tu computadora</p>
                        <span class="text-[11px] text-slate-500">Admite PNG, JPG, WEBP y SVG de hasta 8MB por archivo</span>

                        <!-- Uploading Progress Bar -->
                        <div x-show="isUploading" class="w-full mt-6 space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Subiendo archivos a la clínica...</span>
                                <span x-text="`${uploadProgress}%`"></span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-sky-500 transition-all duration-200" :style="`width: ${uploadProgress}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- In-Modal Notification Toast -->
            <div 
                x-show="toast.visible" 
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                x-cloak 
                class="absolute top-4 right-16 z-50 rounded-xl px-4 py-2.5 shadow-2xl flex items-center gap-2.5 text-xs font-semibold border backdrop-blur-md"
                :class="{
                    'bg-emerald-950/95 text-emerald-200 border-emerald-500/40 shadow-emerald-500/10': toast.type === 'success',
                    'bg-rose-950/95 text-rose-200 border-rose-500/40 shadow-rose-500/10': toast.type === 'error',
                    'bg-sky-950/95 text-sky-200 border-sky-500/40 shadow-sky-500/10': toast.type === 'info',
                }"
            >
                <template x-if="toast.type === 'success'">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <span x-text="toast.message"></span>
            </div>

            <!-- Sleek In-Modal Confirmation Dialog -->
            <div 
                x-show="confirmDialog.isOpen" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak 
                class="absolute inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4"
                @click.self="confirmDialog.isOpen = false"
            >
                <div 
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-sm w-full shadow-2xl text-left"
                    @click.stop
                >
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-white mb-1">¿Eliminar archivo?</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Se eliminará <strong class="text-white" x-text="confirmDialog.assetName"></strong> del servidor de la clínica. Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="confirmDialog.isOpen = false" 
                            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="button" 
                            @click="executeConfirmedDelete()" 
                            class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors shadow-lg shadow-rose-500/20"
                        >
                            Sí, Eliminar
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

<script>
    function mediaLibraryModal() {
        return {
            isOpen: false,
            activeTab: 'library',
            assets: [],
            selectedAsset: null,
            folder: 'media',
            isLoading: false,
            isUploading: false,
            isSyncing: false,
            isCopied: false,
            uploadProgress: 0,
            isDragging: false,
            callback: null,
            toast: { visible: false, message: '', type: 'success' },
            toastTimeout: null,
            confirmDialog: { isOpen: false, assetId: null, assetName: '' },

            showToast(msg, type = 'success') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.visible = true;
                if (this.toastTimeout) clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => {
                    this.toast.visible = false;
                }, 3500);
            },

            openModal(detail = {}) {
                this.callback = detail.callback || null;
                this.folder = detail.folder || 'media';
                this.isOpen = true;
                this.activeTab = 'library';
                this.fetchAssets();
            },

            closeModal() {
                this.isOpen = false;
                this.selectedAsset = null;
            },

            async fetchAssets() {
                this.isLoading = true;
                try {
                    const url = new URL('{{ route('admin.media-library.index') }}', window.location.origin);
                    if (this.folder) {
                        url.searchParams.set('folder', this.folder);
                    }
                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.assets = data.assets;
                    }
                } catch (e) {
                    console.error('Error fetching media assets:', e);
                } finally {
                    this.isLoading = false;
                }
            },

            selectAsset(asset) {
                this.selectedAsset = Object.assign({}, asset);
            },

            confirmSelection() {
                if (!this.selectedAsset) return;
                if (typeof this.callback === 'function') {
                    this.callback(this.selectedAsset.url, this.selectedAsset);
                } else {
                    window.dispatchEvent(new CustomEvent('media-library-selected', {
                        detail: {
                            url: this.selectedAsset.url,
                            asset: this.selectedAsset
                        }
                    }));
                }
                this.closeModal();
            },

            copyAssetUrl(url) {
                if (!url) return;
                navigator.clipboard.writeText(url).then(() => {
                    this.isCopied = true;
                    this.showToast('Enlace copiado al portapapeles', 'info');
                    setTimeout(() => { this.isCopied = false; }, 2000);
                });
            },

            handleDrop(e) {
                this.isDragging = false;
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    this.uploadFiles(files);
                }
            },

            handleFileInput(e) {
                const files = e.target.files;
                if (files.length > 0) {
                    this.uploadFiles(files);
                }
            },

            async uploadFiles(fileList) {
                for (let i = 0; i < fileList.length; i++) {
                    if (!fileList[i].type.startsWith('image/')) {
                        this.showToast('Por favor selecciona únicamente archivos de imagen válidos.', 'error');
                        return;
                    }
                }

                this.isUploading = true;
                this.uploadProgress = 20;

                const formData = new FormData();
                if (fileList.length === 1) {
                    formData.append('file', fileList[0]);
                } else {
                    for (let i = 0; i < fileList.length; i++) {
                        formData.append('files[]', fileList[i]);
                    }
                }
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    this.uploadProgress = 60;
                    const res = await fetch('{{ route('admin.media-library.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    });

                    this.uploadProgress = 100;
                    const data = await res.json();

                    if (data.success) {
                        if (data.assets && Array.isArray(data.assets)) {
                            this.assets.unshift(...data.assets);
                            this.selectedAsset = data.assets[0];
                        } else if (data.asset) {
                            this.assets.unshift(data.asset);
                            this.selectedAsset = data.asset;
                        }
                        this.activeTab = 'library';
                        this.showToast(data.message || 'Imágenes subidas exitosamente.', 'success');
                    } else {
                        this.showToast(data.message || 'Error al subir los archivos.', 'error');
                    }
                } catch (e) {
                    console.error('Error uploading file:', e);
                    this.showToast('Ocurrió un error al subir el archivo.', 'error');
                } finally {
                    this.isUploading = false;
                    this.uploadProgress = 0;
                }
            },

            requestDeleteAsset(asset) {
                if (!asset || !asset.id) return;
                this.confirmDialog.assetId = asset.id;
                this.confirmDialog.assetName = asset.title || asset.filename;
                this.confirmDialog.isOpen = true;
            },

            async executeConfirmedDelete() {
                const id = this.confirmDialog.assetId;
                if (!id) return;

                try {
                    const res = await fetch(`/admin/media-library/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.assets = this.assets.filter(a => a.id !== id);
                        this.selectedAsset = null;
                        this.confirmDialog.isOpen = false;
                        this.showToast('Archivo eliminado de la biblioteca.', 'success');
                    } else {
                        this.showToast(data.message || 'Error al eliminar el archivo.', 'error');
                    }
                } catch (e) {
                    console.error('Error deleting asset:', e);
                    this.showToast('Ocurrió un error al eliminar.', 'error');
                }
            },

            async syncExisting() {
                this.isSyncing = true;
                try {
                    const res = await fetch('{{ route('admin.media-library.sync') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message, 'success');
                        this.fetchAssets();
                    } else {
                        this.showToast(data.message || 'Error al sincronizar.', 'error');
                    }
                } catch (e) {
                    console.error('Error syncing existing images:', e);
                    this.showToast('Error de conexión al sincronizar.', 'error');
                } finally {
                    this.isSyncing = false;
                }
            }
        };
    }
</script>
