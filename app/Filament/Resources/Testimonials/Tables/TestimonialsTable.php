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
                    ->label(__('Photo'))
                    ->circular()
                    ->disk('public'),

                TextColumn::make('patient_name')
                    ->label(__('Patient'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 1).' / 5.0')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('treatment')
                    ->label(__('Completed Treatment'))
                    ->searchable(),

                TextColumn::make('source')
                    ->label(__('Source'))
                    ->badge(),

                ToggleColumn::make('is_verified')
                    ->label(__('Verified')),

                ToggleColumn::make('is_active')
                    ->label(__('Active')),

                TextColumn::make('order')
                    ->label(__('Sort Order'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_verified')
                    ->label(__('Only verified')),
                TernaryFilter::make('is_active')
                    ->label(__('Only active')),
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
