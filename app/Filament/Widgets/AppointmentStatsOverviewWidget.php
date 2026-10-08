<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class AppointmentStatsOverviewWidget extends Widget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected string $view = 'filament.widgets.appointment-stats-overview';

    public function getViewData(): array
    {
        $today = Carbon::today()->toDateString();

        $baseQuery = Appointment::query();
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->isDentist() && $user->dentist_id) {
            $baseQuery->where('dentist_id', $user->dentist_id);
        }

        $confirmedToday = (clone $baseQuery)
            ->where('status', 'confirmed')
            ->whereDate('appointment_date', $today)
            ->count();

        $pendingTotal = (clone $baseQuery)
            ->where('status', 'pending')
            ->count();

        $inProgressToday = (clone $baseQuery)
            ->where('status', 'in_progress')
            ->whereDate('appointment_date', $today)
            ->count();

        $completedToday = (clone $baseQuery)
            ->where('status', 'completed')
            ->whereDate('appointment_date', $today)
            ->count();

        return [
            'confirmedToday' => $confirmedToday,
            'pendingTotal' => $pendingTotal,
            'inProgressToday' => $inProgressToday,
            'completedToday' => $completedToday,
            'todayFormatted' => ucfirst(Carbon::today()->locale('es')->isoFormat('dddd D, MMMM')),
        ];
    }
}
