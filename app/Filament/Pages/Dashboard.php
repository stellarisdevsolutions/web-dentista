<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string|Htmlable
    {
        $hour = (int) Carbon::now()->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Buenos días',
            $hour >= 12 && $hour < 19 => 'Buenas tardes',
            default => 'Buenas noches',
        };

        $user = auth()->user();
        $name = e($user?->name ?? 'Doctor');

        return new HtmlString("{$greeting}, <span class=\"aura-heading-highlight\">{$name}</span>");
    }

    public function getSubheading(): ?string
    {
        return 'Monitorea citas, solicitudes de pacientes y especialistas en tiempo real.';
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 3,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_appointment')
                ->label('Nueva Cita')
                ->icon(Heroicon::OutlinedPlus)
                ->extraAttributes([
                    'class' => 'aura-btn-header-primary',
                ])
                ->url(url('/admin/appointments/create')),

            Action::make('view_agenda')
                ->label('Ver Agenda')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->extraAttributes([
                    'class' => 'aura-btn-header-celeste',
                ])
                ->url(url('/admin/appointments')),

            Action::make('view_site')
                ->label('Ver Sitio Web')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->extraAttributes([
                    'class' => 'aura-btn-header-outline-blue',
                ])
                ->url(url('/'))
                ->openUrlInNewTab(),
        ];
    }
}
