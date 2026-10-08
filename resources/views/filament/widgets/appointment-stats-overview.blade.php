<x-filament-widgets::widget class="fi-wi-appointment-stats">
    <div class="aura-app-compact-bar">
        <!-- 1. Confirmadas Hoy -->
        <div class="aura-mini-stat aura-mini-emerald">
            <div class="aura-mini-icon aura-mini-icon-emerald">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="aura-mini-info">
                <span class="aura-mini-num">{{ $confirmedToday }}</span>
                <span class="aura-mini-label">Confirmadas Hoy</span>
            </div>
        </div>

        <!-- 2. Pendientes Total -->
        <div class="aura-mini-stat aura-mini-orange">
            <div class="aura-mini-icon aura-mini-icon-orange">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </div>
            <div class="aura-mini-info">
                <span class="aura-mini-num">{{ $pendingTotal }}</span>
                <span class="aura-mini-label">Pendientes Total</span>
            </div>
        </div>

        <!-- 3. En Consulta -->
        <div class="aura-mini-stat aura-mini-blue">
            <div class="aura-mini-icon aura-mini-icon-blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
            </div>
            <div class="aura-mini-info">
                <span class="aura-mini-num">{{ $inProgressToday }}</span>
                <span class="aura-mini-label">En Consulta</span>
            </div>
        </div>

        <!-- 4. Completadas Hoy -->
        <div class="aura-mini-stat aura-mini-slate">
            <div class="aura-mini-icon aura-mini-icon-slate">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="aura-mini-info">
                <span class="aura-mini-num">{{ $completedToday }}</span>
                <span class="aura-mini-label">Completadas Hoy</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
