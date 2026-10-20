@php
    $navClinic = $clinic ?? \App\Models\ClinicSetting::first() ?? new \App\Models\ClinicSetting([
        'clinic_name' => 'Aura Dental Studio',
        'phone' => '+1 (555) 234-5678',
        'whatsapp' => '+15552345678',
        'email' => 'hola@auradental.com',
        'address' => 'Av. Las Palmas 742, Piso 4, Distrito Salud',
        'emergency_phone' => '+1 (555) 911-DENT',
        'opening_hours' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM",
    ]);

    $allServices = \App\Models\Service::where('is_active', true)->orderBy('order')->get();
    $servicesByCategory = $allServices->groupBy(function($s) {
        return $s->category ?: 'Odontología Integral';
    });

    $searchItems = $allServices->map(function($s) {
        return [
            'title' => $s->name,
            'type' => 'Tratamiento',
            'category' => $s->category ?? 'Clínico',
            'url' => route('services.show', $s->slug),
            'price' => '$' . number_format($s->price, 0),
        ];
    })->values()->all();

    $searchItems[] = ['title' => 'Sobre Nosotros y Doctores', 'type' => 'Página', 'category' => 'Clínica', 'url' => route('about'), 'price' => ''];
    $searchItems[] = ['title' => 'Artículos y Consejos de Salud Dental', 'type' => 'Página', 'category' => 'Blog', 'url' => route('blog.index'), 'price' => ''];
    $searchItems[] = ['title' => 'Ubicación, Teléfonos y Contacto', 'type' => 'Página', 'category' => 'Contacto', 'url' => route('contact'), 'price' => ''];
    $searchItems[] = ['title' => 'Agendamiento y Reserva de Citas Online', 'type' => 'Cita', 'category' => 'Reservas', 'url' => route('schedule'), 'price' => '24/7'];

    $searchItemsJson = json_encode($searchItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
@endphp

<script>
    function navbarComponent() {
        return {
            mobileMenuOpen: false,
            searchOpen: false,
            searchQuery: '',
            allSearchItems: {!! $searchItemsJson !!},
            get filteredSearchResults() {
                if (!this.searchQuery.trim()) return this.allSearchItems.slice(0, 5);
                var q = this.searchQuery.toLowerCase();
                return this.allSearchItems.filter(function(item) {
                    return item.title.toLowerCase().indexOf(q) !== -1 || 
                           item.category.toLowerCase().indexOf(q) !== -1;
                });
            }
        };
    }
</script>

<header 
    class="w-full z-50 sticky top-0 bg-white border-b border-slate-100 shadow-xs"
    x-data="navbarComponent()"
    @keydown.window.ctrl.k.prevent="searchOpen = true"
    @keydown.window.cmd.k.prevent="searchOpen = true"
>
    <!-- 1. TOPBAR INFORMATIVO (Estilo Referencia: Urgencia + Email | Dirección + Redes) -->
    <div class="bg-[#0c1830] text-slate-300 text-[11px] font-medium border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5 md:py-2">
            
            <!-- A. VISTA ESCRITORIO (md y superior): 2 Columnas completas en 1 sola fila horizontal -->
            <div class="hidden md:flex justify-between items-center gap-4">
                <!-- Lado Izquierdo: Teléfono de Emergencia + Correo Electrónico -->
                <div class="flex items-center gap-6 text-slate-300">
                    <!-- Teléfono de Emergencia -->
                    <a 
                        href="tel:{{ preg_replace('/[^0-9+]/', '', $navClinic->emergency_phone ?? $navClinic->phone) }}" 
                        class="flex items-center gap-1.5 hover:text-white transition-colors group"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 group-hover:scale-110 transition-transform">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>{{ app()->getLocale() === 'en' ? 'Emergency' : 'Urgencias' }}: <strong class="text-white group-hover:text-sky-300 font-semibold">{{ $navClinic->emergency_phone ?? $navClinic->phone }}</strong></span>
                    </a>

                    <!-- Correo Electrónico -->
                    @if(!empty($navClinic->email))
                        <a 
                            href="mailto:{{ $navClinic->email }}" 
                            class="flex items-center gap-1.5 hover:text-white transition-colors group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 group-hover:scale-110 transition-transform">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            <span class="text-slate-300 group-hover:text-white">{{ $navClinic->email }}</span>
                        </a>
                    @endif
                </div>

                <!-- Lado Derecho: Dirección de la Clínica + Iconos de Redes Sociales -->
                <div class="flex items-center gap-4 text-slate-300">
                    <!-- Dirección -->
                    @if(!empty($navClinic->address))
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 shrink-0">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            @if(!empty($navClinic->google_maps_url))
                                <a href="{{ $navClinic->google_maps_url }}" target="_blank" rel="noopener" class="hover:text-white transition-colors truncate max-w-xs sm:max-w-sm">
                                    {{ $navClinic->address }}
                                </a>
                            @else
                                <span class="truncate max-w-xs sm:max-w-sm">{{ $navClinic->address }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="h-3 w-px bg-slate-700"></div>

                    <!-- Iconos de Redes Sociales -->
                    <div class="flex items-center gap-3 text-slate-400">
                        <!-- Facebook -->
                        <a 
                            href="{{ $navClinic->social_links['facebook'] ?? 'https://facebook.com' }}" 
                            target="_blank" 
                            rel="noopener" 
                            class="hover:text-sky-400 transition-colors"
                            title="Facebook"
                            aria-label="Facebook"
                        >
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>

                        <!-- X (Twitter) -->
                        <a 
                            href="{{ $navClinic->social_links['twitter'] ?? $navClinic->social_links['x'] ?? 'https://x.com' }}" 
                            target="_blank" 
                            rel="noopener" 
                            class="hover:text-sky-400 transition-colors"
                            title="X (Twitter)"
                            aria-label="X (Twitter)"
                        >
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>

                        <!-- Instagram -->
                        <a 
                            href="{{ $navClinic->social_links['instagram'] ?? 'https://instagram.com' }}" 
                            target="_blank" 
                            rel="noopener" 
                            class="hover:text-sky-400 transition-colors"
                            title="Instagram"
                            aria-label="Instagram"
                        >
                            <svg class="w-3.5 h-3.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                            </svg>
                        </a>

                        <!-- LinkedIn -->
                        <a 
                            href="{{ $navClinic->social_links['linkedin'] ?? 'https://linkedin.com' }}" 
                            target="_blank" 
                            rel="noopener" 
                            class="hover:text-sky-400 transition-colors"
                            title="LinkedIn"
                            aria-label="LinkedIn"
                        >
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9h2.79v8.37H6.46v-8.37M7.86 6.32a1.63 1.63 0 1 0 1.63 1.63 1.63 1.63 0 0 0-1.63-1.63z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- B. VISTA MÓVIL (< md): Estrictamente 1 sola fila con animación dato por dato -->
            @php
                $mobileSlideCount = 1 + (!empty($navClinic->email) ? 1 : 0) + (!empty($navClinic->address) ? 1 : 0);
                $currSlide = 0;
            @endphp
            <div 
                class="flex md:hidden items-center justify-between gap-3 w-full"
                x-data="{ 
                    current: 0, 
                    total: {{ $mobileSlideCount }},
                    timer: null,
                    start() {
                        if (this.total > 1) {
                            this.timer = setInterval(() => {
                                this.current = (this.current + 1) % this.total;
                            }, 3600);
                        }
                    },
                    stop() {
                        if (this.timer) clearInterval(this.timer);
                    }
                }"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                @touchstart.passive="stop()"
                @touchend.passive="start()"
            >
                <!-- Contenedor del Ticker (1 Fila estricta con altura fija relativa para evitar desbordes) -->
                <div class="relative h-5 flex-1 min-w-0 overflow-hidden">
                    <!-- Dato 1: Teléfono de Urgencias -->
                    <div 
                        x-show="current === {{ $currSlide }}"
                        x-transition:enter="transition ease-out duration-400 transform"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-3"
                        class="absolute inset-0 flex items-center gap-1.5 truncate text-slate-300"
                    >
                        <a 
                            href="tel:{{ preg_replace('/[^0-9+]/', '', $navClinic->emergency_phone ?? $navClinic->phone) }}" 
                            class="flex items-center gap-1.5 hover:text-white truncate"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 shrink-0">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <span class="truncate">{{ app()->getLocale() === 'en' ? 'Emergency' : 'Urgencias' }}: <strong class="text-white font-semibold">{{ $navClinic->emergency_phone ?? $navClinic->phone }}</strong></span>
                        </a>
                    </div>
                    @php $currSlide++; @endphp

                    <!-- Dato 2: Correo Electrónico -->
                    @if(!empty($navClinic->email))
                        <div 
                            x-show="current === {{ $currSlide }}"
                            x-cloak
                            x-transition:enter="transition ease-out duration-400 transform"
                            x-transition:enter-start="opacity-0 translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300 transform"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-3"
                            class="absolute inset-0 flex items-center gap-1.5 truncate text-slate-300"
                        >
                            <a 
                                href="mailto:{{ $navClinic->email }}" 
                                class="flex items-center gap-1.5 hover:text-white truncate"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 shrink-0">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                                <span class="truncate">{{ $navClinic->email }}</span>
                            </a>
                        </div>
                        @php $currSlide++; @endphp
                    @endif

                    <!-- Dato 3: Dirección de la Clínica -->
                    @if(!empty($navClinic->address))
                        <div 
                            x-show="current === {{ $currSlide }}"
                            x-cloak
                            x-transition:enter="transition ease-out duration-400 transform"
                            x-transition:enter-start="opacity-0 translate-y-3"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300 transform"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-3"
                            class="absolute inset-0 flex items-center gap-1.5 truncate text-slate-300"
                        >
                            @if(!empty($navClinic->google_maps_url))
                                <a href="{{ $navClinic->google_maps_url }}" target="_blank" rel="noopener" class="flex items-center gap-1.5 hover:text-white truncate">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 shrink-0">
                                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span class="truncate">{{ $navClinic->address }}</span>
                                </a>
                            @else
                                <div class="flex items-center gap-1.5 truncate">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400 shrink-0">
                                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    <span class="truncate">{{ $navClinic->address }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Iconos de Redes Sociales en Móvil (Fijos, compactos, en la misma fila) -->
                <div class="flex items-center gap-2.5 shrink-0 pl-2.5 border-l border-slate-800 text-slate-400">
                    <a 
                        href="{{ $navClinic->social_links['facebook'] ?? 'https://facebook.com' }}" 
                        target="_blank" 
                        rel="noopener" 
                        class="hover:text-sky-400 transition-colors"
                        title="Facebook"
                        aria-label="Facebook"
                    >
                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>

                    <a 
                        href="{{ $navClinic->social_links['twitter'] ?? $navClinic->social_links['x'] ?? 'https://x.com' }}" 
                        target="_blank" 
                        rel="noopener" 
                        class="hover:text-sky-400 transition-colors"
                        title="X (Twitter)"
                        aria-label="X (Twitter)"
                    >
                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    <a 
                        href="{{ $navClinic->social_links['instagram'] ?? 'https://instagram.com' }}" 
                        target="_blank" 
                        rel="noopener" 
                        class="hover:text-sky-400 transition-colors"
                        title="Instagram"
                        aria-label="Instagram"
                    >
                        <svg class="w-3 h-3 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                        </svg>
                    </a>

                    <a 
                        href="{{ $navClinic->social_links['linkedin'] ?? 'https://linkedin.com' }}" 
                        target="_blank" 
                        rel="noopener" 
                        class="hover:text-sky-400 transition-colors"
                        title="LinkedIn"
                        aria-label="LinkedIn"
                    >
                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9h2.79v8.37H6.46v-8.37M7.86 6.32a1.63 1.63 0 1 0 1.63 1.63 1.63 1.63 0 0 0-1.63-1.63z"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. NAVBAR PRINCIPAL -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 relative">
        <div class="flex justify-between items-center">
            <!-- Brand Logo -->
            <a class="flex items-center gap-3 group" href="{{ route('home') }}">
                @if($navClinic->logo_asset_url)
                    <img src="{{ $navClinic->logo_asset_url }}" alt="{{ $navClinic->clinic_name }}" class="h-10 w-auto max-w-[180px] object-contain group-hover:scale-105 transition-transform" />
                @else
                    <div class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-slate-900">
                        {{ explode(' ', $navClinic->clinic_name)[0] }}<span class="text-sky-500">{{ count(explode(' ', $navClinic->clinic_name)) > 1 ? implode(' ', array_slice(explode(' ', $navClinic->clinic_name), 1)) : 'Studio' }}</span>
                    </span>
                @endif
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <!-- 1. Inicio -->
                <a 
                    class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-sky-500 {{ request()->routeIs('home') ? 'text-sky-500 font-black' : 'text-slate-700' }}" 
                    href="{{ route('home') }}"
                >
                    Inicio
                </a>

                <!-- 2. Nosotros -->
                <a 
                    class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-sky-500 {{ request()->routeIs('about') ? 'text-sky-500 font-black' : 'text-slate-700' }}" 
                    href="{{ route('about') }}"
                >
                    Nosotros
                </a>

                <!-- 3. Servicios -->
                <a 
                    class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-sky-500 {{ request()->routeIs('services.*') ? 'text-sky-500 font-black' : 'text-slate-700' }}" 
                    href="{{ route('services.index') }}"
                >
                    Servicios
                </a>

                <!-- 4. Blog -->
                <a 
                    class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-sky-500 {{ request()->routeIs('blog.*') ? 'text-sky-500 font-black' : 'text-slate-700' }}" 
                    href="{{ route('blog.index') }}"
                >
                    Blog
                </a>

                <!-- 5. Contacto -->
                <a 
                    class="text-xs font-bold uppercase tracking-wider transition-colors hover:text-sky-500 {{ request()->routeIs('contact') ? 'text-sky-500 font-black' : 'text-slate-700' }}" 
                    href="{{ route('contact') }}"
                >
                    Contacto
                </a>
            </div>

            <!-- Right Actions: Lupa (Search) + Botón Reservar -->
            <div class="hidden md:flex items-center space-x-3.5">
                <button 
                    type="button" 
                    @click="searchOpen = true" 
                    class="w-10 h-10 rounded-full bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-500 flex items-center justify-center transition-all hover:scale-105 active:scale-95" 
                    title="Buscar tratamientos o servicios (Ctrl + K)"
                    aria-label="Buscar tratamientos"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </button>

                <a 
                    href="{{ route('schedule') }}" 
                    class="bg-sky-500 hover:bg-sky-600 text-white px-6 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-sky-500/25 flex items-center gap-1.5 hover:scale-105 active:scale-95"
                >
                    <span>Reservar »</span>
                </a>
            </div>

            <!-- Mobile Header Actions (Lupa & Hamburger) -->
            <div class="md:hidden flex items-center gap-2">
                <button 
                    type="button" 
                    @click="searchOpen = true" 
                    class="p-2 rounded-xl bg-slate-100 text-slate-700 hover:text-sky-500 focus:outline-none"
                    aria-label="Buscar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </button>
                <button 
                    type="button" 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="p-2 rounded-xl bg-slate-100 text-slate-900 focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                        <path d="M4 5h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 19h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-cloak style="display: none;" class="md:hidden mt-4 pt-4 border-t border-slate-100 space-y-3 pb-3">
            <a @click="mobileMenuOpen = false" class="block text-xs font-bold uppercase tracking-wider {{ request()->routeIs('home') ? 'text-sky-500 font-black' : 'text-slate-700' }} py-1" href="{{ route('home') }}">Inicio</a>
            
            <a @click="mobileMenuOpen = false" class="block text-xs font-bold uppercase tracking-wider {{ request()->routeIs('about') ? 'text-sky-500 font-black' : 'text-slate-700' }} py-1" href="{{ route('about') }}">Nosotros</a>

            <a @click="mobileMenuOpen = false" class="block text-xs font-bold uppercase tracking-wider {{ request()->routeIs('services.*') ? 'text-sky-500 font-black' : 'text-slate-700' }} py-1" href="{{ route('services.index') }}">Servicios</a>

            <a @click="mobileMenuOpen = false" class="block text-xs font-bold uppercase tracking-wider {{ request()->routeIs('blog.*') ? 'text-sky-500 font-black' : 'text-slate-700' }} py-1" href="{{ route('blog.index') }}">Blog</a>
            
            <a @click="mobileMenuOpen = false" class="block text-xs font-bold uppercase tracking-wider {{ request()->routeIs('contact') ? 'text-sky-500 font-black' : 'text-slate-700' }} py-1" href="{{ route('contact') }}">Contacto</a>
            
            <a @click="mobileMenuOpen = false" class="block text-center bg-sky-500 text-white font-bold text-xs uppercase py-3 rounded-full mt-2 shadow-md shadow-sky-500/25" href="{{ route('schedule') }}">
                Reservar Cita Online »
            </a>
        </div>
    </div>

    <!-- Search Modal Dialog -->
    <div 
        x-show="searchOpen" 
        x-cloak 
        style="display: none;"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-start justify-center pt-20 px-4 sm:px-6"
        @keydown.window.escape="searchOpen = false"
    >
        <div 
            class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-xl w-full p-6 overflow-hidden"
            @click.outside="searchOpen = false"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-800">Buscador Clínico Inteligente</h3>
                </div>
                <button @click="searchOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="mt-4 relative">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    x-ref="searchInput"
                    placeholder="Escribe un tratamiento (ej. Blanqueamiento, Ortodoncia, Implante)..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                />
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3.5 top-3.5 text-slate-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </div>

            <!-- Resultados -->
            <div class="mt-4 max-h-64 overflow-y-auto space-y-2">
                <template x-for="(res, index) in filteredSearchResults" :key="index">
                    <a 
                        :href="res.url" 
                        class="flex items-center justify-between p-3 rounded-2xl hover:bg-sky-50 border border-transparent hover:border-sky-100 transition-all group"
                        @click="searchOpen = false"
                    >
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-white text-slate-600 group-hover:text-sky-500 flex items-center justify-center text-xs font-bold">
                                ✦
                            </span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 group-hover:text-sky-600" x-text="res.title"></p>
                                <span class="text-[10px] text-slate-400 font-medium" x-text="res.category"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold text-sky-600" x-text="res.price"></span>
                            <span class="text-slate-300 group-hover:text-sky-500 font-bold">›</span>
                        </div>
                    </a>
                </template>
                <div x-show="filteredSearchResults.length === 0" class="text-center py-6 text-slate-400 text-xs">
                    No se encontraron tratamientos con ese término.
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Presiona <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded font-mono text-[10px]">Esc</kbd> para salir</span>
                <span>Atajo: <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded font-mono text-[10px]">Ctrl + K</kbd></span>
            </div>
        </div>
    </div>
</header>
