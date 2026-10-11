<x-filament-widgets::widget class="fi-wi-today-specialists">
    <div class="aura-specialists-card">
        <!-- Cabecera -->
        <div class="aura-specialists-header">
            <div class="aura-specialists-header-text">
                <h3 class="aura-specialists-title">Especialistas</h3>
                <p class="aura-specialists-subtitle">Equipo odontológico en la clínica</p>
            </div>
            <div class="aura-specialists-shift-chip" title="Especialistas activos o con agenda hoy">
                <span>{{ $data->inShiftCount }} en turno</span>
            </div>
        </div>

        <!-- Cuerpo / Lista de Especialistas -->
        <div class="aura-specialists-body">
            @if ($data->isEmpty)
                <div class="aura-specialists-empty">
                    <div class="aura-specialists-empty-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </div>
                    <p class="aura-specialists-empty-text">Aún no hay especialistas registrados</p>
                    @if (auth()->user()?->isAdmin())
                        <a href="{{ url('/admin/dentists/create') }}" class="aura-btn-specialist-add">
                            + Agregar especialista
                        </a>
                    @endif
                </div>
            @else
                <ul class="aura-specialists-list {{ count($data->specialists) > 6 ? 'aura-specialists-list-scrollable' : '' }}">
                    @foreach ($data->specialists as $specialist)
                        <li class="aura-specialist-item-row" tabindex="0">
                            <!-- Avatar circular determinista -->
                            <div
                                class="aura-specialist-avatar"
                                style="background-color: {{ $specialist->avatarBgColor }};"
                                aria-hidden="true"
                            >
                                <span>{{ $specialist->initials }}</span>
                            </div>

                            <!-- Información principal -->
                            <div class="aura-specialist-info">
                                <span class="aura-specialist-name" title="{{ $specialist->name }}">
                                    {{ $specialist->name }}
                                </span>
                                <span class="aura-specialist-specialty">
                                    {{ $specialist->specialty }}
                                </span>
                            </div>

                            <!-- Estado y conteo a la derecha -->
                            <div class="aura-specialist-status-col">
                                <span
                                    class="aura-specialist-chip"
                                    style="background-color: {{ $specialist->statusBgColor }}; color: {{ $specialist->statusTextColor }};"
                                >
                                    {{ $specialist->statusLabel }}
                                </span>
                                <span class="aura-specialist-citas-count">
                                    {{ $specialist->todayAppointmentsText }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>

                @if (count($data->specialists) > 6)
                    <div class="aura-specialists-see-all">
                        <a href="{{ auth()->user()?->isAdmin() ? url('/admin/dentists') : url('/admin/appointments') }}">
                            Ver todos ({{ $data->totalCount }})
                        </a>
                    </div>
                @endif
            @endif
        </div>

        <!-- Pie con botón de ancho completo -->
        <div class="aura-specialists-footer">
            @if (auth()->user()?->isAdmin())
                <a href="{{ url('/admin/dentists') }}" class="aura-btn-specialists-action" title="Gestionar especialistas de la clínica">
                    <span>Gestionar especialistas →</span>
                </a>
            @else
                <a href="{{ url('/admin/appointments') }}" class="aura-btn-specialists-action" title="Ver la agenda completa de citas por especialista">
                    <span>Ver agenda por especialista →</span>
                </a>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
