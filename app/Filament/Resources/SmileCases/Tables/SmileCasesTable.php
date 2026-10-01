<?php

namespace App\Filament\Resources\SmileCases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SmileCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->columns([
                ImageColumn::make('before_image')
                    ->label('Antes')
                    ->disk('public'),

                ImageColumn::make('after_image')
                    ->label('Después')
                    ->disk('public'),

                TextColumn::make('title_es')
                    ->label('Título del Caso')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('service.name_es')
                    ->label('Tratamiento')
                    ->placeholder('General')
                    ->sortable(),

                TextColumn::make('dentist.name')
                    ->label('Odontólogo')
                    ->placeholder('Por Asignar')
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Destacado'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Destacados en Galería'),
                TernaryFilter::make('is_active')
                    ->label('Activos'),
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
