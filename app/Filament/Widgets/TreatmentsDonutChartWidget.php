<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;

class TreatmentsDonutChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 1,
        'xl' => 1,
        '2xl' => 1,
    ];

    public function getHeading(): ?string
    {
        return 'Distribución de Tratamientos';
    }

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $services = Service::withCount('appointments')
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($services as $service) {
            $labels[] = $service->name;
            $data[] = max(1, $service->appointments_count);
        }

        if (empty($data)) {
            $labels = ['Ortodoncia Invisible', 'Blanqueamiento Láser', 'Limpieza Ultrasonido', 'Carillas Estéticas'];
            $data = [35, 25, 20, 15];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Citas',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9', // Sky blue
                        '#10b981', // Emerald
                        '#6366f1', // Indigo
                        '#f59e0b', // Amber
                        '#ec4899', // Pink
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
