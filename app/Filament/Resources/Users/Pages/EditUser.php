<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Editar Usuario';
    }

    public function getSubheading(): ?string
    {
        return 'Actualiza las credenciales, rol o vinculación clínica del usuario.';
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
            ->title('Usuario actualizado exitosamente')
            ->body('Los datos de la cuenta de usuario han sido guardados.');
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
            DeleteAction::make()
                ->visible(fn (): bool => auth()->id() !== $this->record->id),
        ];
    }
}
