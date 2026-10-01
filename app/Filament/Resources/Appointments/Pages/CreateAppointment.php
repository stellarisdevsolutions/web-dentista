<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return app()->getLocale() === 'es' ? 'Crear Cita' : 'Create Appointment';
    }
}
