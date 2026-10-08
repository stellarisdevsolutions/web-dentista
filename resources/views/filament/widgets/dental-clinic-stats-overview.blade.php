<x-filament-widgets::widget class="fi-wi-stats-bento">
    <div class="aura-bento-layout">
        <!-- ============================================================
             COLUMNAS 1 Y 2 (FILAS 1 Y 2): 4 KPIS
             ============================================================ -->
        
        <!-- KPI 1: TOTAL CITAS (ÚLTIMA SEMANA) -->
        <div class="aura-kpi-item aura-kpi-theme-blue">
            <div class="aura-kpi-head-simple">
                <span class="aura-kpi-label">Total Citas</span>
                <span class="aura-kpi-chip aura-chip-blue">Última Semana</span>
            </div>

            <div class="aura-kpi-body-simple">
                <div class="aura-kpi-stat">
                    <div class="aura-kpi-num">{{ $weeklyAppointments }}</div>
                    <span class="aura-kpi-subtext">En la última semana</span>
                </div>

                <div class="aura-kpi-big-icon aura-big-icon-blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
            </div>

            <div class="aura-kpi-foot-simple">
                <span class="aura-foot-status">● <strong>{{ $todayAppointments }}</strong> agendadas para hoy</span>
            </div>
        </div>

        <!-- KPI 2: POR CONFIRMAR -->
        <div class="aura-kpi-item aura-kpi-theme-orange">
            <div class="aura-kpi-head-simple">
                <span class="aura-kpi-label">Por Confirmar</span>
                <span class="aura-kpi-chip {{ $pendingAppointments > 0 ? 'aura-chip-amber' : 'aura-chip-green' }}">
                    {{ $pendingAppointments > 0 ? 'Pendiente' : 'Al Día' }}
                </span>
            </div>

            <div class="aura-kpi-body-simple">
                <div class="aura-kpi-stat">
                    <div class="aura-kpi-num">{{ $pendingAppointments }}</div>
                    <span class="aura-kpi-subtext">Pendientes de recepción</span>
                </div>

                <div class="aura-kpi-big-icon aura-big-icon-orange">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
            </div>

            <div class="aura-kpi-foot-simple">
                <span class="aura-foot-status">
                    ● {{ $pendingAppointments > 0 ? 'Requiere confirmar la cita' : 'Agenda 100% al día' }}
                </span>
            </div>
        </div>

        <!-- KPI 3: CITAS CONFIRMADAS (ÚNICA CON EL GRÁFICO DE DÍAS L M M J V) -->
        <div class="aura-kpi-item aura-kpi-theme-emerald">
            <div class="aura-finnova-head">
                <span class="aura-kpi-label">Citas Confirmadas</span>
                <span class="aura-icon-circle aura-icon-emerald">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
            </div>

            <div class="aura-finnova-body">
                <div class="aura-finnova-stat">
                    <div class="aura-kpi-num">{{ $confirmedAppointments }}</div>
                    <span class="aura-kpi-subtext">Listas en sillón dental</span>
                </div>

                <!-- Gráfico exclusivo de días para Citas Confirmadas -->
                <div class="aura-mini-bars">
                    <div class="aura-bar-col">
                        <div class="aura-bar" style="height: 40%;"></div>
                        <span class="aura-bar-label">L</span>
                    </div>
                    <div class="aura-bar-col">
                        <div class="aura-bar" style="height: 65%;"></div>
                        <span class="aura-bar-label">M</span>
                    </div>
                    <div class="aura-bar-col">
                        <div class="aura-bar aura-bar-active" style="height: 100%;"></div>
                        <span class="aura-bar-label aura-label-active">M</span>
                    </div>
                    <div class="aura-bar-col">
                        <div class="aura-bar" style="height: 50%;"></div>
                        <span class="aura-bar-label">J</span>
                    </div>
                    <div class="aura-bar-col">
                        <div class="aura-bar" style="height: 75%;"></div>
                        <span class="aura-bar-label">V</span>
                    </div>
                </div>
            </div>

            <div class="aura-finnova-foot">
                <span class="aura-foot-status">● Box Dental 100% Operativo</span>
            </div>
        </div>

        <!-- KPI 4: SERVICIOS CLÍNICOS (Icono grande de diente dental a la derecha) -->
        <div class="aura-kpi-item aura-kpi-theme-indigo">
            <div class="aura-kpi-head-simple">
                <span class="aura-kpi-label">Servicios Clínicos</span>
                <span class="aura-kpi-chip aura-chip-indigo">Catálogo Activo</span>
            </div>

            <div class="aura-kpi-body-simple">
                <div class="aura-kpi-stat">
                    <div class="aura-kpi-num">{{ $activeServices }}</div>
                    <span class="aura-kpi-subtext">Tratamientos odontológicos</span>
                </div>

                <div class="aura-kpi-big-icon aura-big-icon-indigo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C8.5 2 6 4.5 6 8c0 3 1.5 6 2 9 .4 2.5 1.5 5 2.5 5s1.5-2 1.5-4c0-1.5 0-1.5 0-2 0 .5 0 .5 0 2 0 2 .5 4 1.5 4s2.1-2.5 2.5-5c.5-3 2-6 2-9 0-3.5-2.5-6-6-6z"></path>
                    </svg>
                </div>
            </div>

            <div class="aura-kpi-foot-simple">
                <span class="aura-foot-status">● Especialidades disponibles hoy</span>
            </div>
        </div>

        <!-- ============================================================
             COLUMNA 3 (DOBLE FILA): LISTADO DE ESPECIALISTAS
             ============================================================ -->
        <div class="aura-specialists-card">
            <div class="aura-spec-header">
                <div class="aura-spec-title-group">
                    <h3 class="aura-spec-title">Especialistas</h3>
                    <span class="aura-spec-subtitle">Equipo médico en clínica</span>
                </div>
                <span class="aura-spec-count-badge">
                    <span class="aura-pulse-dot"></span>
                    {{ $activeDentists }} En Turno
                </span>
            </div>

            <div class="aura-spec-list">
                @forelse($dentistsList as $index => $dentist)
                    @php
                        $avatarColors = ['#0284c7', '#059669', '#6366f1', '#ea580c'];
                        $bgColor = $avatarColors[$index % count($avatarColors)];
                        $dNames = explode(' ', trim($dentist->name));
                        $initials = count($dNames) >= 2 
                            ? mb_substr($dNames[0], 0, 1) . mb_substr($dNames[1], 0, 1)
                            : mb_substr($dentist->name, 0, 2);
                    @endphp
                    <div class="aura-spec-row">
                        <div class="aura-spec-avatar-box">
                            @if(!empty($dentist->photo))
                                <img src="{{ $dentist->photo_url }}" alt="{{ $dentist->name }}" class="aura-spec-img" />
                            @else
                                <div class="aura-spec-avatar-fallback" style="background-color: {{ $bgColor }};">
                                    {{ mb_strtoupper($initials) }}
                                </div>
                            @endif
                        </div>

                        <div class="aura-spec-info">
                            <div class="aura-spec-name" title="{{ $dentist->name }}">{{ $dentist->name }}</div>
                            <div class="aura-spec-specialty" title="{{ $dentist->specialty }}">
                                {{ $dentist->specialty ?: 'Odontología General' }}
                            </div>
                        </div>

                        <div class="aura-spec-status-pill">
                            <span class="aura-spec-dot"></span>
                            <span>Disponible</span>
                        </div>
                    </div>
                @empty
                    <div class="aura-spec-empty">
                        <span>No hay especialistas activos en este momento.</span>
                    </div>
                @endforelse
            </div>

            <div class="aura-spec-footer">
                <a href="{{ url('/admin/dentists') }}" class="aura-spec-view-all">
                    <span>Gestionar todos los especialistas</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
