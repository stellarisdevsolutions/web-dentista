<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class LatestAppointmentsWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    public string $activeTab = 'all';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 2,
        'xl' => 2,
        '2xl' => 2,
    ];

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'sm' => 'full',
            'md' => 'full',
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }

    protected string $view = 'filament.widgets.latest-appointments-widget';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function confirmAppointment(int $id): void
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->update(['status' => 'confirmed']);
            Notification::make()
                ->title('Cita confirmada')
                ->body("La cita del paciente {$appointment->patient_name} ha sido confirmada.")
                ->success()
                ->send();
        }
    }

    public function getViewData(): array
    {
        $query = Appointment::with(['service', 'dentist'])->latest();

        if ($this->activeTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->activeTab === 'confirmed') {
            $query->whereIn('status', ['confirmed', 'in_progress']);
        }

        $appointments = $query->take(5)->get();

        $counts = [
            'all' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::whereIn('status', ['confirmed', 'in_progress'])->count(),
        ];

        return [
            'appointments' => $appointments,
            'counts' => $counts,
            'activeTab' => $this->activeTab,
        ];
    }
}
