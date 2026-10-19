<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    public function getTitle(): string
    {
        return 'Editar Tratamiento';
    }

    public function getSubheading(): ?string
    {
        return 'Actualiza la información clínica, tarifas, especialistas asignados y multimedia del servicio.';
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
            ->title('Tratamiento actualizado exitosamente')
            ->body('Los cambios del servicio médico han sido guardados.');
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
            DeleteAction::make(),
        ];
    }
}
