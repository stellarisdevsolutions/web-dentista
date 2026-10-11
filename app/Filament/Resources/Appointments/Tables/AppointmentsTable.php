<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('appointment_date', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->color('primary'),

                TextColumn::make('patient_name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(function (Appointment $record): string {
                        $info = [];
                        if ($record->patient_phone) {
                            $info[] = "Tel: {$record->patient_phone}";
                        }
                        if ($record->is_first_visit) {
                            $info[] = '1ª Visita';
                        }

                        return implode(' • ', $info);
                    }),

                TextColumn::make('service.name')
                    ->label('Tratamiento')
                    ->placeholder('Consulta General')
                    ->sortable()
                    ->description(fn (Appointment $record): ?string => $record->service?->price_from ? 'Desde $'.number_format($record->service->price_from, 0) : null),

                TextColumn::make('dentist.name')
                    ->label('Especialista')
                    ->placeholder('Por Asignar')
                    ->sortable()
                    ->description(fn (Appointment $record): ?string => $record->dentist?->specialty),

                TextColumn::make('appointment_date')
                    ->label('Fecha')
                    ->date('d M, Y')
                    ->sortable(),

                TextColumn::make('appointment_time')
                    ->label('Horario')
                    ->badge()
                    ->color(Color::Slate)
                    ->description(fn (Appointment $record): string => "{$record->duration_minutes} min"),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'gray',
                        'cancelled', 'no_show' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmed' => 'Confirmada',
                        'pending' => 'Por Confirmar',
                        'in_progress' => 'En Consulta',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no_show' => 'No Asistió',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado de Cita')
                    ->options([
                        'pending' => 'Por Confirmar',
                        'confirmed' => 'Confirmada',
                        'in_progress' => 'En Consulta',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no_show' => 'No Asistió',
                    ]),

                SelectFilter::make('dentist_id')
                    ->label('Especialista')
                    ->relationship('dentist', 'name')
                    ->preload(),

                SelectFilter::make('service_id')
                    ->label('Tratamiento')
                    ->relationship('service', 'name')
                    ->preload(),

                TernaryFilter::make('is_first_visit')
                    ->label('Historial Paciente')
                    ->placeholder('Todos los pacientes')
                    ->trueLabel('Primera Visita')
                    ->falseLabel('Paciente Recurrente'),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color(Color::Emerald)
                    ->tooltip('Contactar paciente por WhatsApp')
                    ->url(fn (Appointment $record): string => $record->whatsapp_url, shouldOpenInNewTab: true),

                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->tooltip('Marcar cita como confirmada')
                    ->visible(fn (Appointment $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Cita')
                    ->modalDescription(fn (Appointment $record): string => "¿Confirmar la cita de {$record->patient_name} para el {$record->appointment_date?->format('d/m/Y')} a las {$record->appointment_time}?")
                    ->modalSubmitActionLabel('Sí, confirmar')
                    ->action(function (Appointment $record): void {
                        $record->update(['status' => 'confirmed']);
                        Notification::make()
                            ->title('Cita confirmada')
                            ->body("La cita de {$record->patient_name} ha sido confirmada con éxito.")
                            ->success()
                            ->send();
                    }),

                ActionGroup::make([
                    Action::make('view_details')
                        ->label('Ver Ficha / Detalles')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('gray')
                        ->modalHeading(fn (Appointment $record): string => "Detalles de Cita - {$record->code}")
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar')
                        ->modalContent(fn (Appointment $record) => view('filament.resources.appointments.view-modal', ['record' => $record])),

                    Action::make('start_consultation')
                        ->label('Ingresar a Consulta')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('info')
                        ->visible(fn (Appointment $record): bool => in_array($record->status, ['pending', 'confirmed']))
                        ->action(function (Appointment $record): void {
                            $record->update(['status' => 'in_progress']);
                            Notification::make()
                                ->title('Paciente en consulta')
                                ->info()
                                ->send();
                        }),

                    Action::make('complete_consultation')
                        ->label('Finalizar Consulta')
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->visible(fn (Appointment $record): bool => $record->status === 'in_progress')
                        ->requiresConfirmation()
                        ->action(function (Appointment $record): void {
                            $record->update(['status' => 'completed']);
                            Notification::make()
                                ->title('Consulta finalizada')
                                ->success()
                                ->send();
                        }),

                    EditAction::make()
                        ->label('Editar Cita'),

                    DeleteAction::make()
                        ->label('Eliminar Cita'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
