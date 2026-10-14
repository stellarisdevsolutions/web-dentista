@php
    $tabCounts = $this->getTabCounts();
    $activeTab = $this->activeTab ?: 'all';
    $tableSearch = trim((string) $this->getTableSearch());
    $searchActive = mb_strlen($tableSearch) >= 2;
@endphp

<div class="agenda-filter-container">
    <div class="agenda-filter-card">
        
        <!-- Fila 1: Navegador de día + Selector de Especialista -->
        <div class="agenda-filter-row-1">
            
            <!-- Navegador de Fecha (Optimista con respuesta instantánea 0ms y protección anti-rebote) -->
            <div class="agenda-date-nav {{ $searchActive ? 'agenda-date-nav-dimmed' : '' }}"
                 wire:ignore.self
                 x-data="{
                    clientDate: @js($this->selectedDate),
                    dateLabel: @js($this->getFormattedDateLabel()),
                    activeRequests: 0,
                    debounceTimer: null,

                    init() {
                        this.clientDate = @js($this->selectedDate);
                        this.updateLabel();

                        $watch('$wire.selectedDate', (newDate) => {
                            // Solo aceptar fecha del servidor si no hay peticiones activas ni debounces pendientes
                            if (this.activeRequests === 0 && !this.debounceTimer && newDate) {
                                this.clientDate = newDate;
                                this.updateLabel();
                            }
                        });
                    },

                    parseDate(str) {
                        if (!str) return new Date();
                        const parts = str.split('-');
                        return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                    },

                    toYmd(d) {
                        const y = d.getFullYear();
                        const m = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${y}-${m}-${day}`;
                    },

                    updateLabel() {
                        if (!this.clientDate) return;
                        const d = this.parseDate(this.clientDate);
                        const now = new Date();
                        const isToday = d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth() && d.getDate() === now.getDate();
                        const formatter = new Intl.DateTimeFormat('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
                        let formatted = formatter.format(d).replace(/[.,]/g, '');
                        this.dateLabel = isToday ? ('Hoy · ' + formatted) : formatted;
                    },

                    previousDay() {
                        let d = this.parseDate(this.clientDate);
                        d.setDate(d.getDate() - 1);
                        this.clientDate = this.toYmd(d);
                        this.updateLabel();
                        this.dispatchDate();
                    },

                    nextDay() {
                        let d = this.parseDate(this.clientDate);
                        d.setDate(d.getDate() + 1);
                        this.clientDate = this.toYmd(d);
                        this.updateLabel();
                        this.dispatchDate();
                    },

                    goToToday() {
                        let now = new Date();
                        this.clientDate = this.toYmd(now);
                        this.updateLabel();
                        this.dispatchDate();
                    },

                    onDateInput(e) {
                        if (!e.target.value) return;
                        this.clientDate = e.target.value;
                        this.updateLabel();
                        this.dispatchDate();
                    },

                    dispatchDate() {
                        clearTimeout(this.debounceTimer);
                        this.debounceTimer = setTimeout(() => {
                            this.debounceTimer = null;
                            const targetDate = this.clientDate;
                            this.activeRequests++;
                            $wire.set('selectedDate', targetDate).finally(() => {
                                this.activeRequests = Math.max(0, this.activeRequests - 1);
                            });
                        }, 280);
                    }
                 }">
                <!-- Anterior ‹ -->
                <button type="button"
                        @click.prevent="previousDay()"
                        aria-label="Día anterior"
                        title="Día anterior"
                        class="agenda-nav-btn">
                    ‹
                </button>

                <!-- Etiqueta de la Fecha (Respuesta Inmediata 0ms) -->
                <div class="agenda-date-label" x-text="dateLabel">
                    {{ $this->getFormattedDateLabel() }}
                </div>

                <!-- Siguiente › -->
                <button type="button"
                        @click.prevent="nextDay()"
                        aria-label="Día siguiente"
                        title="Día siguiente"
                        class="agenda-nav-btn">
                    ›
                </button>

                <!-- Icono de Calendario (selector de fecha) -->
                <div class="agenda-cal-btn" title="Elegir fecha">
                    <svg class="agenda-cal-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input type="date"
                           :value="clientDate"
                           @change="onDateInput($event)"
                           aria-label="Elegir fecha"
                           class="agenda-date-hidden-input" />
                </div>

                <!-- Botón Hoy -->
                <button type="button"
                        @click.prevent="goToToday()"
                        class="agenda-today-btn">
                    Hoy
                </button>
            </div>

            <!-- Selector de Especialista (oculto para dentista) -->
            @if(! auth()->user()?->isDentist())
                <div class="agenda-specialist-wrapper">
                    <select wire:model.live="dentistId"
                            class="agenda-specialist-select">
                        <option value="">Todos los especialistas</option>
                        @foreach($this->getDentists() as $dentist)
                            <option value="{{ $dentist->id }}">
                                {{ preg_replace('/^(Dr\.|Dra\.|Dr|Dra)\s+/i', '', $dentist->name) }}{{ $dentist->specialty ? ' · ' . $dentist->specialty : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Fila 2: Pestañas de Estado (Píldoras) + Buscador a la derecha -->
        <div class="agenda-tabs-row">
            <div class="agenda-tabs-group">
                <!-- Todas -->
                <button type="button"
                        wire:click="selectTab('all')"
                        class="agenda-tab-btn {{ $activeTab === 'all' ? 'active' : '' }}">
                    <span>Todas</span>
                    <span class="agenda-tab-badge badge-all">
                        {{ $tabCounts['all'] ?? 0 }}
                    </span>
                </button>

                <!-- Pendientes -->
                <button type="button"
                        wire:click="selectTab('pending')"
                        class="agenda-tab-btn {{ $activeTab === 'pending' ? 'active' : '' }}">
                    <span>Pendientes</span>
                    <span class="agenda-tab-badge badge-pending">
                        {{ $tabCounts['pending'] ?? 0 }}
                    </span>
                </button>

                <!-- Confirmadas -->
                <button type="button"
                        wire:click="selectTab('confirmed')"
                        class="agenda-tab-btn {{ $activeTab === 'confirmed' ? 'active' : '' }}">
                    <span>Confirmadas</span>
                    <span class="agenda-tab-badge badge-confirmed">
                        {{ $tabCounts['confirmed'] ?? 0 }}
                    </span>
                </button>

                <!-- Canceladas -->
                <button type="button"
                        wire:click="selectTab('cancelled')"
                        class="agenda-tab-btn {{ $activeTab === 'cancelled' ? 'active' : '' }}">
                    <span>Canceladas</span>
                    <span class="agenda-tab-badge badge-cancelled">
                        {{ $tabCounts['cancelled'] ?? 0 }}
                    </span>
                </button>
            </div>

            <!-- Buscador Integrado con Debounce a la derecha de las pestañas -->
            <div class="agenda-integrated-search">
                <div class="agenda-search-input-wrapper">
                    <svg class="agenda-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="search"
                           wire:model.live.debounce.350ms="tableSearch"
                           placeholder="Buscar paciente o teléfono"
                           aria-label="Buscar paciente o teléfono"
                           class="agenda-search-input" />
                </div>
            </div>
        </div>
    </div>

    <!-- Franja de Búsqueda Activa -->
    @if($searchActive)
        <div class="agenda-search-banner">
            <div class="agenda-search-banner-text">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px !important; height: 18px !important; min-width: 18px !important; max-width: 18px !important; display: block; flex-shrink: 0;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Buscando en todas las fechas · próximas citas primero</span>
            </div>
            <button type="button" wire:click="clearSearch" class="agenda-search-banner-clear">
                Limpiar búsqueda
            </button>
        </div>
    @endif

    <!-- Overlay Loader para la Tabla (Único) -->
    <div wire:loading
         wire:target="selectedDate, previousDay, nextDay, goToToday, dentistId, selectTab, tableSearch, clearSearch"
         class="agenda-table-loader-overlay"
         style="display: none;">
        <div class="agenda-loader-card">
            <svg class="agenda-loader-spinner" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Cargando citas...</span>
        </div>
    </div>
</div>
