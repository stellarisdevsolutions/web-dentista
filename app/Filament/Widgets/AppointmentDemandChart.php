<?php

namespace App\Filament\Widgets;

use App\Services\AppointmentDemandChartData;
use App\Services\DashboardChartService;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class AppointmentDemandChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 7,
        'xl' => 7,
        '2xl' => 7,
    ];

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'sm' => 'full',
            'md' => 'full',
            'lg' => 7,
            'xl' => 7,
            '2xl' => 7,
        ];
    }

    protected ?string $maxHeight = '260px';

    protected string $view = 'filament.widgets.appointment-demand-chart';

    public function getHeading(): string|Htmlable|null
    {
        return __('Appointment demand · last 7 days');
    }

    public function getChartDataProperty(): AppointmentDemandChartData
    {
        return app(DashboardChartService::class)->getDemandChartData(auth()->user());
    }

    protected function getData(): array
    {
        $data = $this->chartData;

        return [
            'datasets' => [
                [
                    'label' => __('Scheduled appointments'),
                    'data' => $data->counts,
                    'backgroundColor' => $data->backgroundColors,
                    'borderRadius' => 12,
                    'borderSkipped' => false,
                    'maxBarThickness' => 38,
                ],
            ],
            'labels' => $data->labels,
            'fullDates' => $data->fullDates,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        $single = addslashes(__('appointment'));
        $plural = addslashes(__('appointments'));

        return RawJs::make(<<<JS
            {
                maintainAspectRatio: false,
                responsive: true,
                animation: { duration: 350 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#0A1730',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                const chart = context[0].chart;
                                const fullDates = chart.data.fullDates;
                                const index = context[0].dataIndex;
                                return (fullDates && fullDates[index]) ? fullDates[index] : context[0].label;
                            },
                            label: function(context) {
                                const count = context.parsed.y;
                                return count === 1 ? '1 {$single}' : count + ' {$plural}';
                            }
                        }
                    },
                    barValuesOnTop: { display: true }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        border: { display: false },
                        ticks: {
                            color: '#5B6B86',
                            font: { size: 11, weight: '600', family: 'Manrope, sans-serif' }
                        }
                    },
                    y: {
                        min: 0,
                        suggestedMax: 2,
                        grace: '15%',
                        grid: {
                            color: '#E6ECF4',
                            drawBorder: false
                        },
                        border: { display: false },
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: '#5B6B86',
                            font: { size: 11, weight: '600', family: 'Manrope, sans-serif' }
                        }
                    }
                }
            }
        JS);
    }
}
