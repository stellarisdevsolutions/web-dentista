<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class DentalClinicStatsOverview extends Widget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 'full',
        'xl' => 'full',
        '2xl' => 'full',
    ];

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'sm' => 'full',
            'md' => 'full',
            'lg' => 'full',
            'xl' => 'full',
            '2xl' => 'full',
        ];
    }

    protected string $view = 'filament.widgets.dental-clinic-stats-overview';

    public function getViewData(): array
    {
        $today = Carbon::today()->toDateString();
        $weeklyAppointments = Appointment::where('appointment_date', '>=', Carbon::now()->subDays(7))->count();
        $todayAppointments = Appointment::whereDate('appointment_date', $today)->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $confirmedAppointments = Appointment::whereIn('status', ['confirmed', 'in_progress'])->count();
        $activeDentists = Dentist::where('is_active', true)->count();
        $dentistsList = Dentist::where('is_active', true)->orderBy('order')->take(4)->get(['id', 'name', 'specialty', 'photo']);
        $activeServices = Service::where('is_active', true)->count();

        return [
            'weeklyAppointments' => $weeklyAppointments,
            'todayAppointments' => $todayAppointments,
            'pendingAppointments' => $pendingAppointments,
            'confirmedAppointments' => $confirmedAppointments,
            'activeDentists' => $activeDentists,
            'dentistsList' => $dentistsList,
            'activeServices' => $activeServices,
        ];
    }
}
