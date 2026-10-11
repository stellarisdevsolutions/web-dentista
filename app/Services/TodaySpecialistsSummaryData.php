<?php

namespace App\Services;

readonly class TodaySpecialistsSummaryData
{
    /**
     * @param  array<int, TodaySpecialistItemData>  $specialists
     */
    public function __construct(
        public array $specialists,
        public int $inShiftCount,
        public int $totalCount,
        public bool $isEmpty,
    ) {}
}
