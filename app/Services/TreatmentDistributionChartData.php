<?php

namespace App\Services;

class TreatmentDistributionChartData
{
    /**
     * @param  array<int, string>  $labels  E.g. ['Limpieza', 'Extracción', ...]
     * @param  array<int, int>  $counts  E.g. [20, 15, ...]
     * @param  array<int, int>  $percentages  E.g. [45, 30, ...] summing to 100
     * @param  array<int, string>  $backgroundColors  E.g. ['#0A1730', '#0A74B8', ...]
     * @param  array<int, array{name: string, count: int, percentage: int, color: string}>  $legendItems
     * @param  array<int, array{name: string, count: int, percentage: int}>  $tableRows
     */
    public function __construct(
        public array $labels,
        public array $counts,
        public array $percentages,
        public array $backgroundColors,
        public array $legendItems,
        public array $tableRows,
        public int $totalAppointments,
        public bool $isEmpty,
        public string $ariaLabel,
    ) {}
}
