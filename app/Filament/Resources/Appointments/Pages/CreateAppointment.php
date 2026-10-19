<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Crear Cita Odontológica';
    }

    public function getSubheading(): ?string
    {
        return 'Programa una nueva cita dental con validación y verificación de agenda en tiempo real.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar Cita');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancelar')
            ->url($this->getResource()::getUrl('index'))
            ->color('gray');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cita creada exitosamente')
            ->body('La cita odontológica ha sido registrada en el sistema.');
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title('Campos requeridos incompletos')
            ->body('Por favor completa todos los campos obligatorios antes de continuar.')
            ->danger()
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $newDate = Carbon::parse($data['appointment_date'])->toDateString();
        $newTime = substr(trim((string) $data['appointment_time']), 0, 5);
        $data['appointment_time'] = $newTime;

        $serviceId = $data['service_id'] ?? null;
        $service = $serviceId ? Service::find($serviceId) : null;
        $durationMinutes = (int) ($data['duration_minutes'] ?? ($service?->duration_minutes ?: 45));

        // If the logged-in user is a dentist, enforce their dentist_id
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->isDentist() && $user->dentist_id) {
            $data['dentist_id'] = $user->dentist_id;
        }

        $dentistId = $data['dentist_id'] ?? null;

        if ($dentistId) {
            // Verify qualification if service has specific specialists assigned
            if ($service && $service->dentists()->exists() && ! $service->dentists()->where('dentists.id', $dentistId)->exists()) {
                Notification::make()
                    ->title('Especialista no calificado')
                    ->body('El odontólogo seleccionado no realiza este tratamiento.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            $dentist = Dentist::find($dentistId);
            if (! $dentist || ! $dentist->isTimeSlotAvailable($newDate, $newTime, $durationMinutes)) {
                Notification::make()
                    ->title('Conflicto de horario')
                    ->body('El especialista no tiene disponibilidad en la fecha y hora seleccionadas.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        } else {
            // "Primer Especialista Disponible" -> Intelligent allocation by load balancing
            $candidateQuery = $service && $service->dentists()->exists()
                ? $service->dentists()->where('is_active', true)
                : Dentist::where('is_active', true);

            $candidates = $candidateQuery
                ->withCount(['appointments' => fn ($q) => $q->whereDate('appointment_date', $newDate)->whereNotIn('status', ['cancelled', 'no_show'])])
                ->get();

            $availableCandidates = $candidates->filter(function (Dentist $doc) use ($newDate, $newTime, $durationMinutes) {
                return $doc->isTimeSlotAvailable($newDate, $newTime, $durationMinutes);
            });

            if ($availableCandidates->isEmpty()) {
                Notification::make()
                    ->title('Sin disponibilidad')
                    ->body('No hay odontólogos calificados disponibles para este turno y fecha.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            // Pick the qualified dentist with lowest load on that day
            $assignedDentist = $availableCandidates->sortBy('appointments_count')->first();
            $data['dentist_id'] = $assignedDentist->id;
        }

        return $data;
    }
}
