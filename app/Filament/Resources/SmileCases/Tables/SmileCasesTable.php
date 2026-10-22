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
                    ->label(__('Before'))
                    ->disk('public'),

                ImageColumn::make('after_image')
                    ->label(__('After'))
                    ->disk('public'),

                TextColumn::make('title')
                    ->label(__('Case Title'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('service.name')
                    ->label(__('Treatment'))
                    ->placeholder(__('General'))
                    ->sortable(),

                TextColumn::make('dentist.name')
                    ->label(__('Specialist'))
                    ->placeholder(__('To assign'))
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label(__('Featured')),

                ToggleColumn::make('is_active')
                    ->label(__('Active')),

                TextColumn::make('order')
                    ->label(__('Sort Order'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label(__('Featured in gallery')),
                TernaryFilter::make('is_active')
                    ->label(__('Active')),
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
