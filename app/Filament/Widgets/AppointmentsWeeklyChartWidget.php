<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AppointmentsWeeklyChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

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
        return 'Demanda de Citas (Últimos 7 Días)';
    }

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days = [];
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = ucfirst($date->locale('es')->isoFormat('ddd D'));
            $counts[] = Appointment::whereDate('appointment_date', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Citas Agendadas',
                    'data' => $counts,
                    'backgroundColor' => '#0ea5e9',
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
