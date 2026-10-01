<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DentalClinicStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $confirmedAppointments = Appointment::where('status', 'confirmed')->count();
        $activeDentists = Dentist::where('is_active', true)->count();
        $activeServices = Service::where('is_active', true)->count();

        return [
            Stat::make(__('Total Appointments'), (string) $totalAppointments)
                ->description(__('All recorded bookings'))
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),

            Stat::make(__('Pending Review'), (string) $pendingAppointments)
                ->description(__('Requires reception confirmation'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($pendingAppointments > 0 ? 'warning' : 'gray'),

            Stat::make(__('Confirmed Appointments'), (string) $confirmedAppointments)
                ->description(__('Ready for consultation'))
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make(__('Active Specialists'), (string) $activeDentists)
                ->description("{$activeServices} ".__('treatments available'))
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('info'),
        ];
    }
}
