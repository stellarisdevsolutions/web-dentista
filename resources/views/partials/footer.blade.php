@php
    $footerClinic = $clinic ?? \App\Models\ClinicSetting::first() ?? new \App\Models\ClinicSetting([
        'clinic_name' => 'Aura Dental Studio',
        'tagline' => 'Odontología integral, moderna y sin dolor.',
        'phone' => '+1 (555) 234-5678',
        'whatsapp' => '+15552345678',
        'email' => 'hola@auradental.com',
        'address' => 'Av. Las Palmas 742, Piso 4, Distrito Salud',
        'emergency_phone' => '+1 (555) 911-DENT',
    ]);

    $footerServices = \App\Models\Service::where('is_active', true)->orderBy('order')->take(5)->get();
@endphp

<!-- ========================================== -->
<!-- GLOBAL UNIFIED FOOTER                       -->
<!-- ========================================== -->
<footer class="bg-[#0c1830] text-slate-300 pt-16 pb-8 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Main 4-Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80">
            
            <!-- Col 1: Brand & Mission -->
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-full bg-sky-500 text-white flex items-center justify-center shadow-md shadow-sky-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-white">
                        {{ explode(' ', $footerClinic->clinic_name)[0] }}<span class="text-sky-400">{{ count(explode(' ', $footerClinic->clinic_name)) > 1 ? implode(' ', array_slice(explode(' ', $footerClinic->clinic_name), 1)) : 'Studio' }}</span>
                    </span>
                </a>

                <p class="text-slate-400 text-xs leading-relaxed font-light">
                    {{ $footerClinic->tagline ?? 'Odontología integral, moderna y sin dolor. Escaneos 3D, tecnología láser y atención personalizada para toda la familia.' }}
                </p>

                <div class="flex items-center gap-3 pt-2">
                    <div class="w-8 h-8 rounded-full bg-slate-800 text-sky-400 flex items-center justify-center text-xs font-bold border border-slate-700">
                        ✓
                    </div>
                    <span class="text-xs font-bold text-slate-300">Especialistas Médicos Colegiados</span>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-5">Navegación</h4>
                <ul class="space-y-3 text-xs font-medium">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-sky-400 transition-colors flex items-center gap-2">
                            <span class="text-sky-500 font-bold">›</span>
                            <span>Página de Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-sky-400 transition-colors flex items-center gap-2">
                            <span class="text-sky-500 font-bold">›</span>
                            <span>Sobre Nosotros y Doctores</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}" class="hover:text-sky-400 transition-colors flex items-center gap-2">
                            <span class="text-sky-500 font-bold">›</span>
                            <span>Catálogo de Tratamientos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog.index') }}" class="hover:text-sky-400 transition-colors flex items-center gap-2">
                            <span class="text-sky-500 font-bold">›</span>
                            <span>Blog y Consejos Clínicos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="hover:text-sky-400 transition-colors flex items-center gap-2">
                            <span class="text-sky-500 font-bold">›</span>
                            <span>Contacto y Ubicación</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('schedule') }}" class="hover:text-sky-400 transition-colors flex items-center gap-2 font-bold text-sky-400">
                            <span class="text-sky-500 font-bold">›</span>
                            <span>Agendar Cita en Línea 24/7</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Specialized Services -->
            <div>
                <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-5">Servicios Especializados</h4>
                <ul class="space-y-3 text-xs font-medium">
                    @foreach($footerServices as $fSvc)
                        <li>
                            <a href="{{ route('services.show', $fSvc->slug) }}" class="hover:text-sky-400 transition-colors flex items-center gap-2">
                                <span class="text-sky-500 font-bold">›</span>
                                <span class="truncate">{{ $fSvc->name }}</span>
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('services.index') }}" class="text-sky-400 hover:text-sky-300 font-bold flex items-center gap-2 pt-1">
                            <span>✦</span>
                            <span>Ver Todos los Procedimientos »</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Emergency Contact -->
            <div class="space-y-4">
                <h4 class="text-white font-extrabold text-sm uppercase tracking-wider mb-5">Contacto y Urgencias</h4>
                
                <div class="p-4 rounded-2xl bg-[#13223f] border border-slate-800 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Línea Telefónica 24/7</p>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerClinic->emergency_phone ?? $footerClinic->phone) }}" class="text-xs font-black text-sky-400 hover:underline">
                                {{ $footerClinic->emergency_phone ?? $footerClinic->phone }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2 border-t border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Dirección de la Clínica</p>
                            <p class="text-xs text-slate-300 leading-tight">{{ $footerClinic->address }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-1">
                    <a 
                        href="{{ route('schedule') }}"
                        class="w-full inline-flex items-center justify-center py-2.5 px-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs uppercase tracking-wider transition-colors shadow-sm"
                    >
                        Agendar Cita Médica »
                    </a>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400 gap-4">
            <p>© {{ date('Y') }} {{ $footerClinic->clinic_name }}. Todos los derechos reservados.</p>
            <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-bold text-slate-400">
                <a href="{{ url('/admin') }}" class="hover:text-sky-400 transition-colors">Portal Administrativo</a>
                <span class="hover:text-sky-400 cursor-pointer">Política de Privacidad</span>
                <span class="hover:text-sky-400 cursor-pointer">Términos del Servicio</span>
                <span class="hover:text-sky-400 cursor-pointer">Derechos del Paciente</span>
            </div>
        </div>

    </div>
</footer>

<!-- Floating Action Group: Emergency Phone & Online Booking -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2.5">
    <!-- 1. Emergency Call Action (Icon by default, expands text on hover) -->
    <a 
        href="tel:{{ preg_replace('/[^0-9+]/', '', $footerClinic->emergency_phone ?? $footerClinic->phone) }}"
        class="flex items-center bg-rose-600 hover:bg-rose-700 text-white p-3.5 rounded-full shadow-2xl shadow-rose-600/40 hover:scale-105 active:scale-95 transition-all duration-300 group"
        title="Llamar a Emergencias Odontológicas"
        aria-label="Llamar a Emergencias Odontológicas"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
        </svg>
        <span class="max-w-0 opacity-0 overflow-hidden whitespace-nowrap text-xs font-bold uppercase tracking-wider transition-all duration-300 ease-in-out group-hover:max-w-xs group-hover:opacity-100 group-hover:pl-2 group-hover:pr-1">
            Emergencias
        </span>
    </a>

    <!-- 2. Book Appointment Action (Icon by default, expands text on hover) -->
    <a 
        href="{{ route('schedule') }}"
        class="flex items-center bg-sky-500 hover:bg-sky-600 text-white p-3.5 rounded-full shadow-2xl shadow-sky-500/40 hover:scale-105 active:scale-95 transition-all duration-300 group"
        title="Reservar Cita Online"
        aria-label="Reservar Cita Online"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
            <line x1="16" x2="16" y1="2" y2="6"></line>
            <line x1="8" x2="8" y1="2" y2="6"></line>
            <line x1="3" x2="21" y1="10" y2="10"></line>
            <path d="m9 16 2 2 4-4"></path>
        </svg>
        <span class="max-w-0 opacity-0 overflow-hidden whitespace-nowrap text-xs font-bold uppercase tracking-wider transition-all duration-300 ease-in-out group-hover:max-w-xs group-hover:opacity-100 group-hover:pl-2 group-hover:pr-1">
            Reservar Cita
        </span>
    </a>
</div>
