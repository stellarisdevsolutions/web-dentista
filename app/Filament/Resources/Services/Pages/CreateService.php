<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Registrar Tratamiento';
    }

    public function getSubheading(): ?string
    {
        return 'Añade un nuevo servicio clínico o procedimiento dental al catálogo de la clínica.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar Tratamiento');
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
            ->title('Tratamiento registrado exitosamente')
            ->body('El servicio ha sido añadido al catálogo y está listo para su uso.');
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title('Campos requeridos incompletos')
            ->body('Por favor completa todos los campos obligatorios (incluyendo la fotografía del tratamiento) antes de continuar.')
            ->danger()
            ->send();
    }
}
