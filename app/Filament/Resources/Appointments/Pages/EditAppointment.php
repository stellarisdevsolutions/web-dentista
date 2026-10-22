<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return 'Editar Cita Odontológica';
    }

    public function getSubheading(): ?string
    {
        return 'Actualiza el estado clínico, reprograma fecha y turno, o modifica los datos del paciente.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Guardar Cambios');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancelar')
            ->url($this->getResource()::getUrl('index'))
            ->color('gray');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cita actualizada exitosamente')
            ->body('Los cambios de la cita odontológica han sido guardados.');
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title('Campos requeridos incompletos')
            ->body('Por favor completa todos los campos obligatorios antes de continuar.')
            ->danger()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('WhatsApp Paciente')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->url(function (Appointment $record): ?string {
                    $phone = preg_replace('/[^\d]/', '', (string) $record->patient_phone);
                    if (blank($phone)) {
                        return null;
                    }

                    $serviceName = $record->service?->name ?? 'su consulta';
                    $dentistName = $record->dentist?->name ?? 'nuestro equipo';
                    $dateStr = $record->appointment_date ? $record->appointment_date->format('d/m/Y') : '';
                    $timeStr = $record->appointment_time ? substr((string) $record->appointment_time, 0, 5) : '';

                    $msg = rawurlencode("Hola {$record->patient_name}, le escribimos de Aura Dental Studio respecto a su cita de {$serviceName} programada para el {$dateStr} a las {$timeStr} con {$dentistName}. ¿Nos confirma su asistencia?");

                    return "https://wa.me/{$phone}?text={$msg}";
                })
                ->openUrlInNewTab()
                ->visible(fn (Appointment $record): bool => filled($record->patient_phone) && app()->getLocale() !== 'en'),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newDate = Carbon::parse($data['appointment_date'])->toDateString();
        $newTime = substr(trim((string) $data['appointment_time']), 0, 5);
        $data['appointment_time'] = $newTime;

        $serviceId = $data['service_id'] ?? null;
        $service = $serviceId ? Service::find($serviceId) : null;
        $durationMinutes = (int) ($data['duration_minutes'] ?? ($service?->duration_minutes ?: 45));

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->isDentist() && $user->dentist_id) {
            $data['dentist_id'] = $user->dentist_id;
        }

        $dentistId = $data['dentist_id'] ?? null;
        $recordId = $this->record->id;

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
            if (! $dentist || ! $dentist->isTimeSlotAvailable($newDate, $newTime, $durationMinutes, $recordId)) {
                Notification::make()
                    ->title('Conflicto de horario')
                    ->body('El especialista no tiene disponibilidad en la fecha y hora seleccionadas.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        } else {
            // "Primer Especialista Disponible" -> Intelligent allocation
            $candidateQuery = $service && $service->dentists()->exists()
                ? $service->dentists()->where('is_active', true)
                : Dentist::where('is_active', true);

            $candidates = $candidateQuery
                ->withCount(['appointments' => fn ($q) => $q->whereDate('appointment_date', $newDate)->where('id', '!=', $recordId)->whereNotIn('status', ['cancelled', 'no_show'])])
                ->get();

            $availableCandidates = $candidates->filter(function (Dentist $doc) use ($newDate, $newTime, $durationMinutes, $recordId) {
                return $doc->isTimeSlotAvailable($newDate, $newTime, $durationMinutes, $recordId);
            });

            if ($availableCandidates->isEmpty()) {
                Notification::make()
                    ->title('Sin disponibilidad')
                    ->body('No hay odontólogos calificados disponibles para este turno y fecha.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            $assignedDentist = $availableCandidates->sortBy('appointments_count')->first();
            $data['dentist_id'] = $assignedDentist->id;
        }

        return $data;
    }
}
