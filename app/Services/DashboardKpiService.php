<?php

namespace App\Services;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class DashboardKpiService
{
    /**
     * Compute and return typed KPI data for the dashboard.
     */
    public function getKpiData(?User $user = null): DashboardKpiData
    {
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->startOfDay();
        $todayStr = $today->toDateString();

        $isDentist = $user && $user->isDentist() && $user->dentist_id;
        $dentistId = $isDentist ? $user->dentist_id : null;

        // Base query para citas de hoy
        $todayQuery = Appointment::query()->whereDate('appointment_date', $todayStr);
        if ($dentistId) {
            $todayQuery->where('dentist_id', $dentistId);
        }

        // 1 Consulta agrupada por estado para hoy
        $todayCounts = (clone $todayQuery)
            ->selectRaw('status, count(*) as total, coalesce(sum(duration_minutes), 0) as booked_minutes')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Programadas hoy: todas las citas de hoy excepto canceladas
        $scheduledCount = 0;
        $confirmedCount = 0;
        $pendingCount = 0;
        $bookedMinutes = 0;

        foreach ($todayCounts as $status => $row) {
            if ($status !== 'cancelled') {
                $scheduledCount += (int) $row->total;
                $bookedMinutes += (int) $row->booked_minutes;
            }

            if (in_array($status, ['confirmed', 'in_progress', 'completed'])) {
                $confirmedCount += (int) $row->total;
            }

            if ($status === 'pending') {
                $pendingCount += (int) $row->total;
            }
        }

        // Subtexto tarjeta 1
        $confirmedTitle = $isDentist ? __('MY CONFIRMED APPOINTMENTS TODAY') : __('CONFIRMED APPOINTMENTS TODAY');
        if ($scheduledCount === 0) {
            $confirmedSubtitle = __('No appointments scheduled today');
        } else {
            $confirmedSubtitle = $scheduledCount === 1
                ? __('of 1 scheduled')
                : __('of :count scheduled', ['count' => $scheduledCount]);
        }

        // Mini gráfico SVG: últimos 6 días + hoy (7 días en total)
        $sixDaysAgoStr = $today->copy()->subDays(6)->toDateString();
        $trendQuery = Appointment::query()
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->whereBetween('appointment_date', [$sixDaysAgoStr, $todayStr]);

        if ($dentistId) {
            $trendQuery->where('dentist_id', $dentistId);
        }

        $trendCounts = $trendQuery->selectRaw('date(appointment_date) as day_date, count(*) as total')
            ->groupBy('day_date')
            ->pluck('total', 'day_date');

        $weeklyTrend = [];
        $maxTrend = 1;

        for ($i = 6; $i >= 0; $i--) {
            $dayDate = $today->copy()->subDays($i)->toDateString();
            $count = (int) ($trendCounts[$dayDate] ?? 0);
            if ($count > $maxTrend) {
                $maxTrend = $count;
            }

            $weeklyTrend[] = [
                'date' => $dayDate,
                'count' => $count,
                'isToday' => ($i === 0),
                'height' => 0, // Se computa abajo relativo a maxTrend
            ];
        }

        foreach ($weeklyTrend as &$bar) {
            // Altura de barra entre 4px y 30px
            $bar['height'] = (int) round(($bar['count'] / $maxTrend) * 26) + 4;
        }
        unset($bar);

        // Tarjeta 2: Por confirmar
        if ($pendingCount === 0) {
            $pendingSubtitle = __('All confirmed');
        } else {
            $pendingSubtitle = __('Require reception confirmation');
        }

        $pendingButtonText = __('Send reminders');
        $pendingButtonUrl = AppointmentResource::getUrl('index', ['activeTab' => 'pending']);

        // Tarjeta 3: Servicios clínicos activos y top 3 en últimos 30 días
        $activeServicesCount = Service::where('is_active', true)->count();
        $servicesSubtitle = __('Treatments available today');

        $thirtyDaysAgoStr = $today->copy()->subDays(30)->toDateString();
        $topServices = Service::where('is_active', true)
            ->withCount(['appointments' => function ($query) use ($thirtyDaysAgoStr, $todayStr) {
                $query->whereBetween('appointment_date', [$thirtyDaysAgoStr, $todayStr])
                    ->where('status', '!=', 'cancelled');
            }])
            ->orderByDesc('appointments_count')
            ->take(3)
            ->pluck('name')
            ->toArray();

        $remainingServicesCount = max(0, $activeServicesCount - count($topServices));

        // Tarjeta 4: Ocupación de la agenda hoy
        $occupancyTitle = $isDentist ? __('MY OCCUPANCY TODAY') : __('SCHEDULE OCCUPANCY TODAY');

        $availableMinutes = 0;
        if ($isDentist && $user->dentist) {
            $dentist = $user->dentist;
            $start = $dentist->working_hours_start ? Carbon::parse($dentist->working_hours_start) : Carbon::parse('09:00:00');
            $end = $dentist->working_hours_end ? Carbon::parse($dentist->working_hours_end) : Carbon::parse('18:00:00');
            $diff = $start->diffInMinutes($end);
            $availableMinutes = $diff > 0 ? $diff : 540;
        } else {
            $activeDentists = Dentist::where('is_active', true)->get();
            foreach ($activeDentists as $dentist) {
                $start = $dentist->working_hours_start ? Carbon::parse($dentist->working_hours_start) : Carbon::parse('09:00:00');
                $end = $dentist->working_hours_end ? Carbon::parse($dentist->working_hours_end) : Carbon::parse('18:00:00');
                $diff = $start->diffInMinutes($end);
                $availableMinutes += ($diff > 0 ? $diff : 540);
            }

            if ($availableMinutes <= 0) {
                $availableMinutes = 540; // Fallback para evitar división por cero
            }
        }

        $occupancyPercentage = 0;
        if ($availableMinutes > 0 && $bookedMinutes > 0) {
            $occupancyPercentage = (int) round(($bookedMinutes / $availableMinutes) * 100);
            $occupancyPercentage = min(100, max(0, $occupancyPercentage));
        }

        $freePercentage = max(0, 100 - $occupancyPercentage);

        return new DashboardKpiData(
            confirmedTitle: $confirmedTitle,
            confirmedCount: $confirmedCount,
            scheduledCount: $scheduledCount,
            confirmedSubtitle: $confirmedSubtitle,
            weeklyTrend: $weeklyTrend,

            pendingCount: $pendingCount,
            pendingSubtitle: $pendingSubtitle,
            pendingButtonText: $pendingButtonText,
            pendingButtonUrl: $pendingButtonUrl,

            activeServicesCount: $activeServicesCount,
            servicesSubtitle: $servicesSubtitle,
            topServices: $topServices,
            remainingServicesCount: $remainingServicesCount,

            occupancyTitle: $occupancyTitle,
            occupancyPercentage: $occupancyPercentage,
            freePercentage: $freePercentage,
        );
    }
}
