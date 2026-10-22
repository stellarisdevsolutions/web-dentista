<?php

namespace App\Filament\Widgets;

use App\Services\DashboardChartService;
use App\Services\TreatmentDistributionChartData;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class TreatmentDistributionChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 5,
        'xl' => 5,
        '2xl' => 5,
    ];

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'sm' => 'full',
            'md' => 'full',
            'lg' => 5,
            'xl' => 5,
            '2xl' => 5,
        ];
    }

    protected ?string $maxHeight = '220px';

    protected string $view = 'filament.widgets.treatment-distribution-chart';

    public function getHeading(): string|Htmlable|null
    {
        return __('Treatment distribution');
    }

    public function getChartDataProperty(): TreatmentDistributionChartData
    {
        return app(DashboardChartService::class)->getDistributionChartData(auth()->user());
    }

    protected function getData(): array
    {
        $data = $this->chartData;

        if ($data->isEmpty) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#E6ECF4'],
                        'borderWidth' => 0,
                        'spacing' => 0,
                        'cutout' => '65%',
                    ],
                ],
                'labels' => [__('No data')],
                'percentages' => [0],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => $data->counts,
                    'backgroundColor' => $data->backgroundColors,
                    'borderWidth' => 0,
                    'spacing' => 2,
                    'cutout' => '65%',
                ],
            ],
            'labels' => $data->labels,
            'percentages' => $data->percentages,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs|null
    {
        $noDataLabel = addslashes(__('No data'));
        $noTreatmentsYet = addslashes(__('No treatments registered yet'));
        $single = addslashes(__('appointment'));
        $plural = addslashes(__('appointments'));

        return RawJs::make(<<<JS
            {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '65%',
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
                            label: function(context) {
                                const chart = context.chart;
                                const isPlaceholder = chart.data.labels[0] === '{$noDataLabel}' || chart.data.labels[0] === 'Sin datos';
                                if (isPlaceholder) {
                                    return '{$noTreatmentsYet}';
                                }
                                const count = context.parsed;
                                const percentages = chart.data.percentages || [];
                                const pct = percentages[context.dataIndex] !== undefined ? percentages[context.dataIndex] : 0;
                                const countLabel = count === 1 ? '1 {$single}' : count + ' {$plural}';
                                return countLabel + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        JS);
    }
}
