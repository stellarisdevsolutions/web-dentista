<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->columns([
                ImageColumn::make('patient_avatar')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('patient_name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('rating')
                    ->label('Calificación')
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state))
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('treatment')
                    ->label('Tratamiento Realizado')
                    ->searchable(),

                TextColumn::make('source')
                    ->label('Origen')
                    ->badge(),

                ToggleColumn::make('is_verified')
                    ->label('Verificado'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label('Solo Verificados'),
                TernaryFilter::make('is_active')
                    ->label('Solo Activos'),
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
