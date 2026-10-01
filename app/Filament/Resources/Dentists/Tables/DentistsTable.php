<?php

namespace App\Filament\Resources\Dentists\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
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
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('name')
                    ->label('Especialista')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title_es')
                    ->label('Cargo / Título')
                    ->searchable(),

                TextColumn::make('specialty_es')
                    ->label('Especialidad')
                    ->badge()
                    ->searchable(),

                TextColumn::make('license_number')
                    ->label('Colegiatura / Registro')
                    ->searchable()
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
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
