<?php

namespace App\Services;

readonly class TodaySpecialistItemData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $specialty,
        public string $initials,
        public string $avatarBgColor,
        public string $statusKey,
        public string $statusLabel,
        public string $statusBgColor,
        public string $statusTextColor,
        public int $todayAppointmentsCount,
        public string $todayAppointmentsText,
    ) {}
}
