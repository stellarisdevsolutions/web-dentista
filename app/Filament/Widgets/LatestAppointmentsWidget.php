<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class LatestAppointmentsWidget extends Widget
{
    protected static ?int $sort = 10;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '60s';

    public string $activeTab = 'all';

    public function getColumnSpan(): int|string|array
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user && $user->isDentist()) {
            return [
                'default' => 'full',
                'sm' => 'full',
                'md' => 'full',
                'lg' => 12,
                'xl' => 12,
                '2xl' => 12,
            ];
        }

        return [
            'default' => 'full',
            'sm' => 'full',
            'md' => 'full',
            'lg' => 8,
            'xl' => 8,
            '2xl' => 8,
        ];
    }

    protected string $view = 'filament.widgets.latest-appointments-widget';

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['all', 'confirmed', 'pending'], true)) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Obtener los datos para la vista según el rol y filtros (solo visual).
     *
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        /** @var User $user */
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $query = Appointment::query()
            ->with(['service', 'dentist'])
            ->whereDate('appointment_date', $today);

        // Scope del servidor estricto para dentistas
        if ($user && $user->isDentist()) {
            $dentistId = (int) ($user->dentist_id ?? 0);
            $query->where('dentist_id', $dentistId);
        }

        // Filtro por tab activo
        if ($this->activeTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->activeTab === 'confirmed') {
            $query->whereIn('status', ['confirmed', 'in_progress']);
        }

        $appointments = $query->orderBy('appointment_time', 'asc')->get();

        // Conteo del servidor por tab según el rol
        $baseCountQuery = Appointment::query()->whereDate('appointment_date', $today);
        if ($user && $user->isDentist()) {
            $baseCountQuery->where('dentist_id', (int) ($user->dentist_id ?? 0));
        }

        $counts = [
            'all' => (clone $baseCountQuery)->count(),
            'confirmed' => (clone $baseCountQuery)->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'pending' => (clone $baseCountQuery)->where('status', 'pending')->count(),
        ];

        return [
            'appointments' => $appointments,
            'counts' => $counts,
            'activeTab' => $this->activeTab,
            'currentUser' => $user,
        ];
    }
}
