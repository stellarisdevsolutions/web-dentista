<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->recordClasses(fn (Post $record): string => "cursor-pointer post-row post-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->header(view('filament.resources.posts.table-header'))
            ->emptyStateHeading('No hay artículos registrados')
            ->emptyStateDescription('Comienza publicando artículos y consejos de salud bucal para tus pacientes.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Portada')
                    ->disk('public')
                    ->defaultImageUrl(fn (Post $record): string => $record->cover_image_url)
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-xs border border-slate-200/80'])
                    ->size(54),

                TextColumn::make('title')
                    ->label('Artículo')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Post $record): string => $record->excerpt ? Str::limit($record->excerpt, 60) : '—'),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('published_at')
                    ->label('Fecha de Publicación')
                    ->dateTime('d M, Y H:i')
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label('Publicado')
                    ->onColor('success')
                    ->offColor('gray')
                    ->afterStateUpdated(function (Post $record, bool $state): void {
                        Notification::make()
                            ->title($state ? 'Artículo publicado' : 'Artículo en borrador')
                            ->body($state ? "'{$record->title}' ahora es visible públicamente en el blog." : "'{$record->title}' se cambió a borrador y no es visible en la web.")
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle')
                            ->send();
                    }),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Estado')
                    ->trueLabel('Solo publicados')
                    ->falseLabel('Solo borradores'),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar artículo'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar artículo'),
            ]);
    }
}
