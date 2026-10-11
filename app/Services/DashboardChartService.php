<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class DashboardChartService
{
    /**
     * Get demand chart data for the last 7 days (6 previous days + today).
     */
    public function getDemandChartData(?User $user = null): AppointmentDemandChartData
    {
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->startOfDay();
        $startDate = $today->copy()->subDays(6)->startOfDay();

        $startDateStr = $startDate->toDateString();
        $todayStr = $today->toDateString();

        $isDentist = $user && $user->isDentist() && $user->dentist_id;
        $dentistId = $isDentist ? $user->dentist_id : null;

        $query = Appointment::query()
            ->where('status', '!=', 'cancelled')
            ->whereDate('appointment_date', '>=', $startDateStr)
            ->whereDate('appointment_date', '<=', $todayStr);

        if ($dentistId) {
            $query->where('dentist_id', $dentistId);
        }

        $countsByDate = $query
            ->selectRaw('date(appointment_date) as day_date, count(*) as total')
            ->groupBy('day_date')
            ->pluck('total', 'day_date');

        $labels = [];
        $counts = [];
        $backgroundColors = [];
        $fullDates = [];
        $tableRows = [];
        $totalAppointments = 0;

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $rawDate = $date->toDateString();
            $count = (int) ($countsByDate[$rawDate] ?? 0);
            $isToday = ($i === 0);

            $label = str_replace('.', '', ucfirst($date->locale('es')->isoFormat('ddd D')));
            $fullDate = ucfirst($date->locale('es')->isoFormat('dddd D [de] MMMM'));

            $color = $isToday ? '#0A1730' : '#7DD3FC';

            $labels[] = $label;
            $counts[] = $count;
            $backgroundColors[] = $color;
            $fullDates[] = $fullDate;
            $totalAppointments += $count;

            $tableRows[] = [
                'label' => $label,
                'fullDate' => $fullDate,
                'count' => $count,
                'isToday' => $isToday,
            ];
        }

        $isEmpty = ($totalAppointments === 0);

        $ariaParts = [];
        foreach ($tableRows as $row) {
            $ariaParts[] = "{$row['label']}: {$row['count']} cita(s)";
        }
        $ariaLabel = "Demanda de citas últimos 7 días. Total: {$totalAppointments} citas. ".implode(', ', $ariaParts);

        return new AppointmentDemandChartData(
            labels: $labels,
            counts: $counts,
            backgroundColors: $backgroundColors,
            fullDates: $fullDates,
            tableRows: $tableRows,
            totalAppointments: $totalAppointments,
            isEmpty: $isEmpty,
            ariaLabel: $ariaLabel,
        );
    }

    /**
     * Get treatment distribution for the last 30 days.
     */
    public function getDistributionChartData(?User $user = null): TreatmentDistributionChartData
    {
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->startOfDay();
        $thirtyDaysAgo = $today->copy()->subDays(29)->startOfDay();

        $startDateStr = $thirtyDaysAgo->toDateString();
        $todayStr = $today->toDateString();

        $isDentist = $user && $user->isDentist() && $user->dentist_id;
        $dentistId = $isDentist ? $user->dentist_id : null;

        $query = Appointment::query()
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', '!=', 'cancelled')
            ->whereDate('appointments.appointment_date', '>=', $startDateStr)
            ->whereDate('appointments.appointment_date', '<=', $todayStr);

        if ($dentistId) {
            $query->where('appointments.dentist_id', $dentistId);
        }

        $serviceCounts = $query
            ->selectRaw('services.id, services.name, count(*) as total')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total')
            ->get();

        $colorPalette = ['#0A1730', '#0A74B8', '#0EA5E9', '#7DD3FC', '#CFE3F4'];

        $totalAppointments = (int) $serviceCounts->sum('total');

        if ($totalAppointments === 0) {
            return new TreatmentDistributionChartData(
                labels: [],
                counts: [],
                percentages: [],
                backgroundColors: [],
                legendItems: [],
                tableRows: [],
                totalAppointments: 0,
                isEmpty: true,
                ariaLabel: 'Distribución de tratamientos: Aún no hay tratamientos registrados en los últimos 30 días.',
            );
        }

        $items = [];
        $otherCount = 0;

        foreach ($serviceCounts as $index => $row) {
            if ($index < 4) {
                $items[] = [
                    'name' => (string) $row->name,
                    'count' => (int) $row->total,
                ];
            } else {
                $otherCount += (int) $row->total;
            }
        }

        if ($otherCount > 0) {
            $items[] = [
                'name' => 'Otros',
                'count' => $otherCount,
            ];
        }

        $percentages = $this->calculatePercentagesWithLargestRemainder(
            array_column($items, 'count'),
            $totalAppointments
        );

        $labels = [];
        $counts = [];
        $backgroundColors = [];
        $legendItems = [];
        $tableRows = [];
        $ariaParts = [];

        foreach ($items as $idx => $item) {
            $pct = $percentages[$idx] ?? 0;
            $color = ($item['name'] === 'Otros') ? '#CFE3F4' : ($colorPalette[$idx] ?? '#CFE3F4');

            $labels[] = $item['name'];
            $counts[] = $item['count'];
            $backgroundColors[] = $color;

            $legendItems[] = [
                'name' => $item['name'],
                'count' => $item['count'],
                'percentage' => $pct,
                'color' => $color,
            ];

            $tableRows[] = [
                'name' => $item['name'],
                'count' => $item['count'],
                'percentage' => $pct,
            ];

            $ariaParts[] = "{$item['name']}: {$pct}% ({$item['count']} citas)";
        }

        $ariaLabel = "Distribución de tratamientos últimos 30 días. Total: {$totalAppointments} citas. ".implode(', ', $ariaParts);

        return new TreatmentDistributionChartData(
            labels: $labels,
            counts: $counts,
            percentages: $percentages,
            backgroundColors: $backgroundColors,
            legendItems: $legendItems,
            tableRows: $tableRows,
            totalAppointments: $totalAppointments,
            isEmpty: false,
            ariaLabel: $ariaLabel,
        );
    }

    /**
     * Compute integer percentages summing exactly to 100 using the Largest Remainder Method.
     *
     * @param  array<int, int>  $counts
     * @return array<int, int>
     */
    public function calculatePercentagesWithLargestRemainder(array $counts, int $total): array
    {
        if ($total === 0 || empty($counts)) {
            return array_fill(0, count($counts), 0);
        }

        $floored = [];
        $remainders = [];
        $sumFloored = 0;

        foreach ($counts as $index => $count) {
            $rawPercent = ($count / $total) * 100;
            $floorVal = (int) floor($rawPercent);
            $floored[$index] = $floorVal;
            $remainders[$index] = $rawPercent - $floorVal;
            $sumFloored += $floorVal;
        }

        $diff = 100 - $sumFloored;
        arsort($remainders);

        foreach (array_keys($remainders) as $index) {
            if ($diff <= 0) {
                break;
            }
            $floored[$index]++;
            $diff--;
        }

        ksort($floored);

        return $floored;
    }
}
