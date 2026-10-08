<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->fontFamily('mono'),

                TextColumn::make('patient_name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Appointment $record): string => (string) $record->patient_phone),

                TextColumn::make('service.name')
                    ->label('Tratamiento')
                    ->placeholder('Consulta General')
                    ->sortable(),

                TextColumn::make('dentist.name')
                    ->label('Especialista')
                    ->placeholder('Por Asignar')
                    ->sortable(),

                TextColumn::make('appointment_date')
                    ->label('Fecha')
                    ->date('d M, Y')
                    ->sortable(),

                TextColumn::make('appointment_time')
                    ->label('Hora')
                    ->badge()
                    ->color(Color::Slate),

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
                        'pending' => 'Pendiente',
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
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'in_progress' => 'En Consulta',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no_show' => 'No Asistió',
                    ]),
                SelectFilter::make('dentist_id')
                    ->label('Especialista')
                    ->relationship('dentist', 'name'),
                SelectFilter::make('service_id')
                    ->label('Tratamiento')
                    ->relationship('service', 'name'),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color(Color::Emerald)
                    ->url(fn (Appointment $record): string => $record->whatsapp_url, shouldOpenInNewTab: true),

                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Appointment $record): bool => $record->status === 'pending')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed'])),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
