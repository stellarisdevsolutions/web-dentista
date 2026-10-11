@php
    use Filament\Support\Facades\FilamentAsset;

    $data = $this->chartData;
    $type = $this->getType();
    $maxHeight = $this->getMaxHeight() ?? '260px';
    $chartAccessibleLabel = $data->ariaLabel;
@endphp

<x-filament-widgets::widget class="fi-wi-chart aura-demand-widget-container">
    <div
        @if ($pollingInterval = $this->getPollingInterval())
            wire:poll.{{ $pollingInterval }}="updateChartData"
        @endif
        class="aura-chart-card aura-demand-card"
    >
        <div class="aura-chart-header">
            <h3 class="aura-chart-title">
                {{ $this->getHeading() }}
            </h3>
        </div>

        <div class="aura-chart-canvas-area" style="position: relative; height: {{ $maxHeight }};">
            <div
                x-load
                x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                data-chart-type="{{ $type }}"
                x-data="chart({
                    cachedData: @js($this->getCachedData()),
                    options: @js($this->getOptions()),
                    type: @js($type),
                })"
                class="fi-wi-chart-frame fi-wi-chart-canvas-ctn fi-wi-chart-frame-no-aspect-ratio"
                style="height: 100%; width: 100%;"
            >
                <canvas
                    x-ref="canvas"
                    role="img"
                    aria-label="{{ $chartAccessibleLabel }}"
                    style="width: 100%; height: {{ $maxHeight }}; max-height: {{ $maxHeight }};"
                ></canvas>

                <span aria-hidden="true" x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                <span aria-hidden="true" x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                <span aria-hidden="true" x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                <span aria-hidden="true" x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                <span aria-hidden="true" x-ref="tooltipBackgroundColorElement" class="fi-wi-chart-tooltip-bg-color"></span>
                <span aria-hidden="true" x-ref="tooltipTextColorElement" class="fi-wi-chart-tooltip-text-color"></span>
                <span aria-hidden="true" x-ref="tooltipBorderColorElement" class="fi-wi-chart-tooltip-border-color"></span>
            </div>

            @if ($data->isEmpty)
                <div class="aura-demand-empty-overlay">
                    <span class="aura-demand-empty-badge">
                        Sin citas en los últimos 7 días
                    </span>
                </div>
            @endif
        </div>

        {{-- Leyenda propia HTML debajo --}}
        <div class="aura-demand-legend" aria-hidden="true">
            <div class="aura-demand-legend-item">
                <span class="aura-demand-dot" style="background-color: #7DD3FC;"></span>
                <span class="aura-demand-legend-text">Citas agendadas</span>
            </div>
            <div class="aura-demand-legend-item">
                <span class="aura-demand-dot" style="background-color: #0A1730;"></span>
                <span class="aura-demand-legend-text">Hoy</span>
            </div>
        </div>

        {{-- Tabla de datos accesible para lectores de pantalla --}}
        <div class="sr-only fi-sr-only" style="position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border: 0 !important;">
            <table>
                <caption>Demanda de citas de los últimos 7 días</caption>
                <thead>
                    <tr>
                        <th scope="col">Día</th>
                        <th scope="col">Fecha completa</th>
                        <th scope="col">Citas agendadas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->tableRows as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ $row['fullDate'] }}</td>
                            <td>{{ $row['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>
