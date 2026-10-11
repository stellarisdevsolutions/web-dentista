<x-filament-widgets::widget class="fi-wi-kpi-cards">
    <div class="aura-kpi-container">
        <div class="aura-kpi-grid">
            <!-- ==========================================
                 TARJETA 1: CITAS CONFIRMADAS HOY
                 ========================================== -->
            <div class="aura-kpi-card aura-kpi-card-1">
                <!-- Zona A: Cabecera -->
                <div class="aura-kpi-zone-a">
                    <span class="aura-kpi-label" title="{{ $kpi->confirmedTitle }}">{{ $kpi->confirmedTitle }}</span>
                    <div class="aura-kpi-icon-box" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>

                <!-- Zona B: Valor -->
                <div class="aura-kpi-zone-b">
                    <span class="aura-kpi-num">{{ $kpi->confirmedCount }}</span>
                    <span class="aura-kpi-subtext">{{ $kpi->confirmedSubtitle }}</span>
                </div>

                <!-- Zona C: Contenido Secundario (Mini Gráfico con píldoras) -->
                <div class="aura-kpi-zone-c">
                    @php
                        $trendCountsList = collect($kpi->weeklyTrend)->map(fn($b) => $b['count'])->implode(', ');
                        $trendAria = "Citas confirmadas últimos 7 días: {$trendCountsList}";
                        $maxCount = collect($kpi->weeklyTrend)->max('count') ?: 1;
                    @endphp
                    <div class="aura-kpi-pills-trend" role="img" aria-label="{{ $trendAria }}">
                        @foreach($kpi->weeklyTrend as $bar)
                            @php
                                $isToday = $bar['isToday'];
                                $h = $bar['count'] > 0 ? max(14, min(34, round(($bar['count'] / $maxCount) * 34))) : 12;
                                if ($isToday) {
                                    $h = max($h, 28);
                                }
                            @endphp
                            <div 
                                class="aura-kpi-pill-bar {{ $isToday ? 'aura-kpi-pill-today' : '' }}" 
                                style="height: {{ $h }}px;" 
                                title="{{ $bar['date'] }}: {{ $bar['count'] }} citas"
                            ></div>
                        @endforeach
                    </div>
                </div>

                <!-- Zona D: Pie -->
                <div class="aura-kpi-zone-d"></div>
            </div>

            <!-- ==========================================
                 TARJETA 2: POR CONFIRMAR
                 ========================================== -->
            <div class="aura-kpi-card aura-kpi-card-2">
                <!-- Zona A: Cabecera -->
                <div class="aura-kpi-zone-a">
                    <span class="aura-kpi-label">POR CONFIRMAR</span>
                    @if($kpi->pendingCount > 0)
                        <span class="aura-kpi-chip aura-kpi-chip-amber">Pendiente</span>
                    @else
                        <span class="aura-kpi-chip aura-kpi-chip-green">Al día</span>
                    @endif
                </div>

                <!-- Zona B: Valor -->
                <div class="aura-kpi-zone-b">
                    <span class="aura-kpi-num">{{ $kpi->pendingCount }}</span>
                    <span class="aura-kpi-subtext">{{ $kpi->pendingSubtitle }}</span>
                </div>

                <!-- Zona C: Contenido Secundario -->
                <div class="aura-kpi-zone-c">
                    @if($kpi->pendingCount > 0)
                        <a href="{{ $kpi->pendingButtonUrl }}" class="aura-kpi-btn-amber">
                            <span>{{ $kpi->pendingButtonText }}</span>
                        </a>
                    @else
                        <span class="aura-kpi-discreet-text">No hay recordatorios pendientes</span>
                    @endif
                </div>

                <!-- Zona D: Pie -->
                <div class="aura-kpi-zone-d"></div>
            </div>

            <!-- ==========================================
                 TARJETA 3: SERVICIOS CLÍNICOS
                 (2 servicios en su propia fila + chip +N más)
                 ========================================== -->
            <!-- ==========================================
                 TARJETA 3: SERVICIOS CLÍNICOS
                 (Máximo 2 filas: Fila 1 = 1er y 2do servicio; Fila 2 = 3er servicio acortado + chip +N)
                 ========================================== -->
            <div class="aura-kpi-card aura-kpi-card-3">
                <!-- Zona A: Cabecera -->
                <div class="aura-kpi-zone-a">
                    <span class="aura-kpi-label">SERVICIOS CLÍNICOS</span>
                    <span class="aura-kpi-chip aura-kpi-chip-blue">Activo</span>
                </div>

                <!-- Zona B: Valor -->
                <div class="aura-kpi-zone-b">
                    <span class="aura-kpi-num">{{ $kpi->activeServicesCount }}</span>
                    <span class="aura-kpi-subtext">{{ $kpi->servicesSubtitle }}</span>
                </div>

                <!-- Zona C: Contenido Secundario (Chips en 2 filas) -->
                <div class="aura-kpi-zone-c aura-kpi-services-zone">
                    @php
                        $service1 = $kpi->topServices[0] ?? null;
                        $service2 = $kpi->topServices[1] ?? null;
                        $service3 = $kpi->topServices[2] ?? null;

                        $hiddenServices = array_slice($kpi->topServices, 3);
                        $totalHidden = count($hiddenServices) + $kpi->remainingServicesCount;
                        $tooltipText = !empty($hiddenServices)
                            ? implode(', ', $hiddenServices) . ($kpi->remainingServicesCount > 0 ? " y {$kpi->remainingServicesCount} más" : '')
                            : "{$totalHidden} tratamientos más";
                    @endphp
                    <div class="aura-kpi-services-list">
                        <!-- Fila 1: Primeros servicios (hasta 2) -->
                        @if($service1 || $service2)
                            <div class="aura-kpi-services-row aura-kpi-services-row-1">
                                @if($service1)
                                    <span class="aura-kpi-service-pill" title="{{ $service1 }}">
                                        {{ $service1 }}
                                    </span>
                                @endif
                                @if($service2)
                                    <span class="aura-kpi-service-pill" title="{{ $service2 }}">
                                        {{ $service2 }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Fila 2: 3er servicio que se acorta responsive + chip +N -->
                        @if($service3 || $kpi->remainingServicesCount > 0)
                            <div class="aura-kpi-services-row aura-kpi-services-row-2">
                                @if($service3)
                                    <span class="aura-kpi-service-pill aura-kpi-service-pill-row2" title="{{ $service3 }}">
                                        {{ $service3 }}
                                    </span>
                                @endif

                                @if($kpi->remainingServicesCount > 0)
                                    <span class="aura-kpi-service-pill aura-kpi-service-pill-more" title="{{ $tooltipText }}">
                                        +{{ $kpi->remainingServicesCount }}
                                    </span>
                                @elseif($totalHidden > 0)
                                    <span class="aura-kpi-service-pill aura-kpi-service-pill-more" title="{{ $tooltipText }}">
                                        +{{ $totalHidden }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Zona D: Pie -->
                <div class="aura-kpi-zone-d"></div>
            </div>

            <!-- ==========================================
                 TARJETA 4: OCUPACIÓN DE LA AGENDA HOY
                 (Alineado arriba a la altura de los demás, círculo y % más grandes)
                 ========================================== -->
            <div class="aura-kpi-card aura-kpi-card-4">
                <!-- Zona A: Cabecera -->
                <div class="aura-kpi-zone-a">
                    <span class="aura-kpi-label" title="{{ $kpi->occupancyTitle }}">{{ $kpi->occupancyTitle }}</span>
                </div>

                <!-- Zona B: Valor alineado arriba con círculo y porcentaje más grandes -->
                <div class="aura-kpi-zone-b aura-kpi-occupancy-zone">
                    <div class="aura-kpi-occupancy-row">
                        <!-- 1. Gráfico circular a la izquierda (88px) -->
                        <div class="aura-kpi-ring-wrapper">
                            @php
                                $p = $kpi->occupancyPercentage;
                                $dash = ($p > 0 && $p < 2) ? 2 : $p;
                                $space = max(0, 100 - $dash);
                            @endphp
                            <svg 
                                width="88" 
                                height="88" 
                                viewBox="0 0 42 42" 
                                class="aura-kpi-ring-svg"
                                role="img" 
                                aria-label="Ocupación: {{ $p }} por ciento"
                            >
                                <!-- Pista circular de fondo azul marino oscuro -->
                                <circle 
                                    class="aura-kpi-ring-bg" 
                                    cx="21" 
                                    cy="21" 
                                    r="15.91549430918954" 
                                    fill="transparent" 
                                    stroke="#142848" 
                                    stroke-width="5"
                                />
                                @if($p > 0)
                                    <!-- Arco de progreso celeste vibrante -->
                                    <circle 
                                        class="aura-kpi-ring-progress" 
                                        cx="21" 
                                        cy="21" 
                                        r="15.91549430918954" 
                                        fill="transparent" 
                                        stroke="#00A3FF" 
                                        stroke-width="5" 
                                        stroke-linecap="round" 
                                        stroke-dasharray="{{ $dash }} {{ $space }}" 
                                        stroke-dashoffset="0"
                                    />
                                @endif
                            </svg>
                        </div>

                        <!-- 2. Número y porcentaje en el medio + Texto a la derecha -->
                        <div class="aura-kpi-occupancy-data">
                            <span class="aura-kpi-occupancy-val">{{ $kpi->occupancyPercentage }}%</span>
                            <span class="aura-kpi-subtext aura-kpi-subtext-occupancy">de la agenda reservada</span>
                        </div>
                    </div>
                </div>

                <!-- Zona C: Espacio intermedio flexible -->
                <div class="aura-kpi-zone-c"></div>

                <!-- Zona D: Pie (Libre: X%) -->
                <div class="aura-kpi-zone-d">
                    <span class="aura-kpi-free-text">Libre: <strong>{{ $kpi->freePercentage }}%</strong></span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
