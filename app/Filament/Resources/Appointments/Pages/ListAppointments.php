<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return app()->getLocale() === 'es' ? 'Gestión de Citas' : 'Appointments';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(app()->getLocale() === 'es' ? 'Nueva Cita' : 'New Appointment'),
        ];
    }
}
