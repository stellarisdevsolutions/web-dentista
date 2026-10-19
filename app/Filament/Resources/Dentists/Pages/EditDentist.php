<?php

namespace App\Filament\Resources\Dentists\Pages;

use App\Filament\Resources\Dentists\DentistResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditDentist extends EditRecord
{
    protected static string $resource = DentistResource::class;

    public function getTitle(): string
    {
        return 'Editar Especialista';
    }

    public function getSubheading(): ?string
    {
        return 'Actualiza el perfil profesional, horarios de atención y tratamientos asignados.';
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
            ->title('Especialista actualizado exitosamente')
            ->body('Los cambios del perfil médico han sido guardados.');
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
            Action::make('change_photo')
                ->label('Cambiar Foto')
                ->icon('heroicon-o-camera')
                ->color('info')
                ->modalHeading(fn (): string => "Cambiar fotografía de {$this->getRecord()->name}")
                ->modalDescription('Sube o actualiza la fotografía 1:1 del especialista. Si la eliminas, se mostrarán las iniciales con fondo celeste estilo Agenda.')
                ->modalSubmitActionLabel('Guardar Fotografía')
                ->schema([
                    FileUpload::make('photo')
                        ->label('Fotografía Oficial (1:1)')
                        ->image()
                        ->imageCropAspectRatio('1:1')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            null,
                            '1:1',
                        ])
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('800')
                        ->imageResizeTargetHeight('800')
                        ->directory('dentists')
                        ->disk('public')
                        ->default(fn (): ?string => $this->getRecord()->photo)
                        ->helperText('Formato 1:1 cuadrado (800×800 px). Se convertirá automáticamente a WebP.'),
                ])
                ->action(function (array $data): void {
                    $this->getRecord()->update([
                        'photo' => $data['photo'] ?? null,
                    ]);

                    $this->fillForm();

                    Notification::make()
                        ->title('Fotografía actualizada correctamente')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
