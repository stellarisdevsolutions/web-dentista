<?php

namespace App\Filament\Resources\Dentists\Tables;

use App\Models\Dentist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DentistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->recordClasses(fn (Dentist $record): string => "cursor-pointer dentist-row dentist-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->header(view('filament.resources.dentists.table-header'))
            ->emptyStateHeading('No hay especialistas registrados')
            ->emptyStateDescription('Comienza añadiendo los doctores y cirujanos de la clínica.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->columns([
                TextColumn::make('photo')
                    ->label('Foto')
                    ->view('filament.resources.dentists.columns.photo-column'),

                TextColumn::make('name')
                    ->label('Especialista')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record): string => $record->title ?: ($record->email ?? '—')),

                TextColumn::make('specialty')
                    ->label('Especialidad')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-academic-cap')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Contacto')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Teléfono copiado al portapapeles')
                    ->placeholder('Sin teléfono'),

                TextColumn::make('license_number')
                    ->label('Colegiatura')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('services_count')
                    ->counts('services')
                    ->label('Tratamientos')
                    ->badge()
                    ->color('primary')
                    ->suffix(' serv.')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Disponible')
                    ->onColor('success')
                    ->offColor('gray'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Disponibilidad')
                    ->trueLabel('Solo disponibles')
                    ->falseLabel('No disponibles'),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar especialista'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar especialista'),
            ]);
    }
}
