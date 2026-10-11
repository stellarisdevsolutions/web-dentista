@php
    use Filament\Support\Facades\FilamentAsset;

    $data = $this->chartData;
    $type = $this->getType();
    $maxHeight = $this->getMaxHeight() ?? '220px';
    $chartAccessibleLabel = $data->ariaLabel;
@endphp

<x-filament-widgets::widget class="fi-wi-chart aura-donut-widget-container">
    <div
        @if ($pollingInterval = $this->getPollingInterval())
            wire:poll.{{ $pollingInterval }}="updateChartData"
        @endif
        class="aura-chart-card aura-donut-card"
    >
        <div class="aura-chart-header">
            <h3 class="aura-chart-title">
                {{ $this->getHeading() }}
            </h3>
        </div>

        <div class="aura-donut-body">
            <div class="aura-donut-canvas-container" style="position: relative; width: 220px; height: {{ $maxHeight }}; flex-shrink: 0;">
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
                    <div class="aura-donut-empty-overlay">
                        <span class="aura-donut-empty-text">
                            Aún no hay tratamientos registrados
                        </span>
                    </div>
                @endif
            </div>

            {{-- Leyenda propia HTML a la derecha / debajo --}}
            <div class="aura-donut-legend" aria-hidden="true">
                @if ($data->isEmpty)
                    <div class="aura-donut-empty-legend-hint">
                        Sin datos en los últimos 30 días
                    </div>
                @else
                    @foreach ($data->legendItems as $item)
                        <div class="aura-donut-legend-item">
                            <span class="aura-donut-legend-box" style="background-color: {{ $item['color'] }};"></span>
                            <span class="aura-donut-legend-name" title="{{ $item['name'] }}">
                                {{ $item['name'] }}
                            </span>
                            <span class="aura-donut-legend-sep">·</span>
                            <span class="aura-donut-legend-pct">
                                {{ $item['percentage'] }}%
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Tabla de datos accesible para lectores de pantalla --}}
        <div class="sr-only fi-sr-only" style="position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0, 0, 0, 0) !important; white-space: nowrap !important; border: 0 !important;">
            <table>
                <caption>Distribución de tratamientos en los últimos 30 días</caption>
                <thead>
                    <tr>
                        <th scope="col">Tratamiento</th>
                        <th scope="col">Citas</th>
                        <th scope="col">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->tableRows as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $row['percentage'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>
