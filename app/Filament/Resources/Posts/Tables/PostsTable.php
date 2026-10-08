<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Portada')
                    ->disk('public'),

                TextColumn::make('title')
                    ->label('Título del Artículo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->searchable(),

                TextColumn::make('published_at')
                    ->label('Fecha de Publicación')
                    ->dateTime('d M, Y')
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label('Publicado'),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Artículos Publicados'),
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
