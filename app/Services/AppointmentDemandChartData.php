<?php

namespace App\Services;

class AppointmentDemandChartData
{
    /**
     * @param  array<int, string>  $labels  E.g. ['Lun 5', 'Mar 6', ...]
     * @param  array<int, int>  $counts  E.g. [3, 0, 4, 1, 5, 2, 6]
     * @param  array<int, string>  $backgroundColors  E.g. ['#7DD3FC', ..., '#0A1730']
     * @param  array<int, string>  $fullDates  E.g. ['Lunes 5 de Octubre', ...]
     * @param  array<int, array{label: string, fullDate: string, count: int, isToday: bool}>  $tableRows
     */
    public function __construct(
        public array $labels,
        public array $counts,
        public array $backgroundColors,
        public array $fullDates,
        public array $tableRows,
        public int $totalAppointments,
        public bool $isEmpty,
        public string $ariaLabel,
    ) {}
}
