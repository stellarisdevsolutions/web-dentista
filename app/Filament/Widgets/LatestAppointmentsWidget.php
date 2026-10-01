<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestAppointmentsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Últimas Citas y Solicitudes Web'))
            ->query(
                Appointment::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('patient_name')
                    ->label('Paciente')
                    ->description(fn (Appointment $record): string => (string) $record->patient_phone),

                TextColumn::make('service.name_es')
                    ->label('Tratamiento')
                    ->placeholder('General'),

                TextColumn::make('dentist.name')
                    ->label('Especialista')
                    ->placeholder('Por Asignar'),

                TextColumn::make('appointment_date')
                    ->label('Fecha')
                    ->date('d M, Y'),

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
            ]);
    }
}
