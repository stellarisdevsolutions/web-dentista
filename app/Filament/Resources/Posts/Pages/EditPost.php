<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    public function getTitle(): string
    {
        return 'Editar Artículo';
    }

    public function getSubheading(): ?string
    {
        return 'Actualiza el contenido editorial, imagen de portada, categoría o fecha de publicación.';
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
            ->title('Artículo actualizado exitosamente')
            ->body('Los cambios del artículo han sido guardados.');
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
