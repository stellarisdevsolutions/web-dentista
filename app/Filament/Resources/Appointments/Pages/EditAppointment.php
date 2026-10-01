<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return app()->getLocale() === 'es' ? 'Editar Cita' : 'Edit Appointment';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
