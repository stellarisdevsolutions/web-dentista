<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public'),

                TextColumn::make('name_es')
                    ->label('Tratamiento (ES)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name_en')
                    ->label('Treatment (EN)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category_es')
                    ->label('Categoría')
                    ->badge()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Precio Base')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->suffix(' min')
                    ->sortable(),

                ToggleColumn::make('allows_online_booking')
                    ->label('Reserva Online'),

                ToggleColumn::make('is_featured')
                    ->label('Destacado'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
                TernaryFilter::make('allows_online_booking')
                    ->label('Reserva Online Permitida'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
