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
    <!-- 1. TOPBAR INFORMATIVO -->
    <div class="bg-[#0c1830] text-slate-300 text-[11px] font-medium border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-6 text-slate-300">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-slate-400">Emergencias 24/7:</span>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $navClinic->emergency_phone ?? $navClinic->phone) }}" class="font-bold text-white hover:text-sky-400 transition-colors">
                        {{ $navClinic->emergency_phone ?? $navClinic->phone }}
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-1.5 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>{{ $navClinic->address }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-slate-400 hidden lg:inline">Lun - Sáb: 8:00 AM - 7:00 PM</span>
                <span class="text-slate-700 hidden lg:inline">•</span>
                <a 
                    href="tel:{{ $navClinic->phone }}" 
                    class="text-sky-400 hover:text-sky-300 font-bold flex items-center gap-1.5 transition-colors"
                >
                    <svg class="w-3.5 h-3.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <span>Llamar: {{ $navClinic->phone }}</span>
                </a>
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
