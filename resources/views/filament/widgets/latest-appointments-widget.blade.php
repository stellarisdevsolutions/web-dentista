<x-filament-widgets::widget class="fi-wi-appointments-feed">
    <div class="aura-feed-container">
        <!-- Encabezado Estructurado -->
        <div class="aura-feed-header-block">
            <!-- Fila 1: Título y Botón Amigable a la Derecha -->
            <div class="aura-feed-title-row">
                <h3 class="aura-feed-title">
                    Citas y Solicitudes de Pacientes
                </h3>

                <!-- Botón de Cabecera Amigable -->
                <a href="{{ url('/admin/appointments') }}" class="aura-btn-go-module" title="Ver la agenda completa de citas">
                    <span>Ver Agenda Completa</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <!-- Fila 2: Párrafo Descriptivo -->
            <p class="aura-feed-subtitle">
                Monitorea las citas programadas y solicitudes de atención en tiempo real.
            </p>

            <!-- Fila 3: Filtros debajo del Párrafo -->
            <div class="aura-feed-filters-row">
                <div class="aura-feed-tabs">
                    <button 
                        type="button" 
                        wire:click="setTab('all')" 
                        class="aura-feed-tab-btn {{ $activeTab === 'all' ? 'aura-tab-active' : '' }}"
                    >
                        Todas ({{ $counts['all'] }})
                    </button>
                    <button 
                        type="button" 
                        wire:click="setTab('pending')" 
                        class="aura-feed-tab-btn {{ $activeTab === 'pending' ? 'aura-tab-active' : '' }}"
                    >
                        Por Confirmar ({{ $counts['pending'] }})
                    </button>
                    <button 
                        type="button" 
                        wire:click="setTab('confirmed')" 
                        class="aura-feed-tab-btn {{ $activeTab === 'confirmed' ? 'aura-tab-active' : '' }}"
                    >
                        Confirmadas ({{ $counts['confirmed'] }})
                    </button>
                </div>
            </div>
        </div>

        <!-- Listado de las 5 Últimas Citas (Sin Acciones Individuales) -->
        <div class="aura-feed-list">
            @forelse($appointments as $appointment)
                @php
                    $names = explode(' ', trim($appointment->patient_name));
                    $initials = count($names) >= 2 
                        ? mb_substr($names[0], 0, 1) . mb_substr($names[1], 0, 1)
                        : mb_substr($appointment->patient_name, 0, 2);
                    $initials = mb_strtoupper($initials);

                    $statusMap = [
                        'confirmed' => ['label' => 'Confirmada', 'class' => 'aura-status-confirmed', 'dot' => '#10b981'],
                        'pending' => ['label' => 'Por Confirmar', 'class' => 'aura-status-pending', 'dot' => '#f59e0b'],
                        'in_progress' => ['label' => 'En Sillón', 'class' => 'aura-status-progress', 'dot' => '#0ea5e9'],
                        'completed' => ['label' => 'Completada', 'class' => 'aura-status-completed', 'dot' => '#64748b'],
                        'cancelled' => ['label' => 'Cancelada', 'class' => 'aura-status-cancelled', 'dot' => '#ef4444'],
                        'no_show' => ['label' => 'No Asistió', 'class' => 'aura-status-noshow', 'dot' => '#ef4444'],
                    ];
                    $statusInfo = $statusMap[$appointment->status] ?? ['label' => ucfirst($appointment->status), 'class' => 'aura-status-completed', 'dot' => '#64748b'];
                @endphp

                <div class="aura-feed-row">
                    <!-- Columna 1: Datos del Paciente (Avatar, Nombre, Teléfono) -->
                    <div class="aura-feed-patient">
                        <div class="aura-feed-avatar">
                            <span>{{ $initials }}</span>
                        </div>
                        <div class="aura-feed-patient-info">
                            <span class="aura-patient-name">{{ $appointment->patient_name }}</span>
                            <span class="aura-patient-phone">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                {{ $appointment->patient_phone }}
                            </span>
                        </div>
                    </div>

                    <!-- Columna 2: Servicio y Doctor Asignado -->
                    <div class="aura-feed-service-col">
                        <span class="aura-service-badge">
                            {{ $appointment->service?->name ?? 'Consulta General' }}
                        </span>
                        <span class="aura-dentist-tag">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{ $appointment->dentist?->name ?? 'Por Asignar' }}
                        </span>
                    </div>

                    <!-- Columna 3: Fecha y Hora Programada -->
                    <div class="aura-feed-datetime">
                        <span class="aura-date-text">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('D MMM, YYYY') }}
                        </span>
                        <span class="aura-time-pill">
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                        </span>
                    </div>

                    <!-- Columna 4: Estado de la Cita -->
                    <div class="aura-feed-status">
                        <span class="aura-status-badge {{ $statusInfo['class'] }}">
                            <span class="aura-dot" style="background-color: {{ $statusInfo['dot'] }};"></span>
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="aura-feed-empty">
                    <div class="aura-empty-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <h4>No hay citas registradas en esta vista</h4>
                    <p>La agenda se encuentra despejada en este momento.</p>
                </div>
            @endforelse
        </div>

        <!-- Botón Inferior para ir a Citas -->
        <div class="aura-feed-bottom-action">
            <a href="{{ url('/admin/appointments') }}" class="aura-btn-footer-appointments">
                <span>Ver todas las citas en la agenda</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
