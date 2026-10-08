<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Filament\Widgets\AppointmentStatsOverviewWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return 'Gestión de Citas';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Cita'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AppointmentStatsOverviewWidget::class,
        ];
    }
}
