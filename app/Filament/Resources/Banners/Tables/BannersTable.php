<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->columns([
                ImageColumn::make('background_image')
                    ->label('Imagen')
                    ->disk('public'),

                TextColumn::make('badge')
                    ->label('Etiqueta')
                    ->badge(),

                TextColumn::make('title')
                    ->label('Título Principal')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('button_text')
                    ->label('Texto Botón (CTA)'),

                ToggleColumn::make('is_active')
                    ->label('Activo'),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Banners Activos'),
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
