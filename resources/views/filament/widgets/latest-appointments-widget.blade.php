<x-filament-widgets::widget class="fi-wi-appointments-feed">
    @php
        $isDentist = $currentUser->isDentist();
        $pageTitle = $isDentist ? __('My appointments · today') : __('Patient appointments and requests · today');
        $pageSubtitle = $isDentist 
            ? __('Your consultations and patients scheduled for today.') 
            : __('Monitor scheduled appointments and requests in real time.');
        $headerBtnText = $isDentist ? __('View my schedule') : __('View full schedule');
    @endphp

    <div class="aura-feed-container">
        <!-- Encabezado Estructurado -->
        <div class="aura-feed-header-block">
            <!-- Fila 1: Título y Botón a la Derecha -->
            <div class="aura-feed-title-row">
                <h3 class="aura-feed-title">
                    {{ $pageTitle }}
                </h3>

                <!-- Botón de Cabecera -->
                <a href="{{ url('/admin/appointments') }}" class="aura-btn-go-module" title="{{ $headerBtnText }}">
                    <span>{{ $headerBtnText }}</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>

            <!-- Fila 2: Párrafo Descriptivo -->
            <p class="aura-feed-subtitle">
                {{ $pageSubtitle }}
            </p>

            <!-- Fila 3: Filtros debajo del Párrafo -->
            <div class="aura-feed-filters-row">
                <div class="aura-feed-tabs">
                    <button 
                        type="button" 
                        wire:click="setTab('all')" 
                        class="aura-feed-tab-btn {{ $activeTab === 'all' ? 'aura-tab-active' : '' }}"
                    >
                        {{ __('All (filter)') }} ({{ $counts['all'] }})
                    </button>
                    <button 
                        type="button" 
                        wire:click="setTab('confirmed')" 
                        class="aura-feed-tab-btn {{ $activeTab === 'confirmed' ? 'aura-tab-active' : '' }}"
                    >
                        {{ __('Confirmed (filter)') }} ({{ $counts['confirmed'] }})
                    </button>
                    <button 
                        type="button" 
                        wire:click="setTab('pending')" 
                        class="aura-feed-tab-btn {{ $activeTab === 'pending' ? 'aura-tab-active' : '' }}"
                    >
                        {{ __('Unconfirmed') }} ({{ $counts['pending'] }})
                    </button>
                </div>
            </div>
        </div>

        <!-- Listado Visual de Citas (Sin botones de acción individuales) -->
        <div class="aura-feed-list">
            @forelse($appointments as $appointment)
                @php
                    $names = explode(' ', trim($appointment->patient_name));
                    $initials = count($names) >= 2 
                        ? mb_substr($names[0], 0, 1) . mb_substr($names[1], 0, 1)
                        : mb_substr($appointment->patient_name, 0, 2);
                    $initials = mb_strtoupper($initials);

                    $statusKey = $appointment->status instanceof \App\Enums\AppointmentStatus
                        ? $appointment->status->value
                        : (string) $appointment->status;

                    $statusMap = [
                        'confirmed' => ['label' => __('Confirmed'), 'class' => 'aura-status-confirmed', 'dot' => '#10b981'],
                        'pending' => ['label' => __('Pending confirmation'), 'class' => 'aura-status-pending', 'dot' => '#f59e0b'],
                        'in_progress' => ['label' => __('In consultation'), 'class' => 'aura-status-progress', 'dot' => '#0ea5e9'],
                        'completed' => ['label' => __('Completed'), 'class' => 'aura-status-completed', 'dot' => '#64748b'],
                        'cancelled' => ['label' => __('Cancelled'), 'class' => 'aura-status-cancelled', 'dot' => '#ef4444'],
                        'no_show' => ['label' => __('No show'), 'class' => 'aura-status-noshow', 'dot' => '#ef4444'],
                    ];
                    $statusInfo = $statusMap[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'aura-status-completed', 'dot' => '#64748b'];
                @endphp

                <div class="aura-feed-row">
                    <!-- Columna 1: Paciente (Avatar, Nombre, Teléfono) -->
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

                    <!-- Columna 2: Servicio y Dentista (Oculto para Dentista) -->
                    <div class="aura-feed-service-col">
                        <span class="aura-service-badge">
                            {{ $appointment->service?->name ?? __('General consultation') }}
                        </span>
                        @if (! $isDentist)
                            <span class="aura-dentist-tag">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                {{ $appointment->dentist?->name ?? __('To assign') }}
                            </span>
                        @endif
                    </div>

                    <!-- Columna 3: Horario -->
                    <div class="aura-feed-datetime">
                        <span class="aura-time-pill">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $appointment->formatted_time }}
                        </span>
                    </div>

                    <!-- Columna 4: Estado -->
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
                    <h4>{{ __('No appointments registered in this view') }}</h4>
                    <p>{{ __('The schedule is clear at the moment.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Botón Inferior para ir a Citas -->
        <div class="aura-feed-bottom-action">
            <a href="{{ url('/admin/appointments') }}" class="aura-btn-footer-appointments">
                <span>{{ $isDentist ? __('View all my appointments in schedule') : __('View all appointments in schedule') }}</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
