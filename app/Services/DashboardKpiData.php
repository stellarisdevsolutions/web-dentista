<?php

namespace App\Services;

class DashboardKpiData
{
    /**
     * @param  array<int, array{date: string, count: int, height: int, isToday: bool}>  $weeklyTrend
     * @param  array<int, string>  $topServices
     */
    public function __construct(
        // Tarjeta 1: Citas Confirmadas
        public string $confirmedTitle,
        public int $confirmedCount,
        public int $scheduledCount,
        public string $confirmedSubtitle,
        public array $weeklyTrend,

        // Tarjeta 2: Por Confirmar
        public int $pendingCount,
        public string $pendingSubtitle,
        public string $pendingButtonText,
        public string $pendingButtonUrl,

        // Tarjeta 3: Servicios Clínicos
        public int $activeServicesCount,
        public string $servicesSubtitle,
        public array $topServices,
        public int $remainingServicesCount,

        // Tarjeta 4: Ocupación
        public string $occupancyTitle,
        public int $occupancyPercentage,
        public int $freePercentage,
    ) {}
}
