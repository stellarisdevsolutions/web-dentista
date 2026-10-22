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
            ->emptyStateHeading(__('No articles registered'))
            ->emptyStateDescription(__('Start by publishing oral health articles and tips for your patients.'))
            ->emptyStateIcon('heroicon-o-document-text')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('Cover'))
                    ->disk('public')
                    ->defaultImageUrl(fn (Post $record): string => $record->cover_image_url)
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-xs border border-slate-200/80'])
                    ->size(54),

                TextColumn::make('title')
                    ->label(__('Article'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Post $record): string => $record->excerpt ? Str::limit($record->excerpt, 60) : '—'),

                TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('published_at')
                    ->label(__('Publication Date'))
                    ->dateTime('d M, Y H:i')
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label(__('Published'))
                    ->onColor('success')
                    ->offColor('gray')
                    ->afterStateUpdated(function (Post $record, bool $state): void {
                        Notification::make()
                            ->title($state ? __('Article published') : __('Article in draft'))
                            ->body($state ? __("':title' is now publicly visible on the blog.", ['title' => $record->title]) : __("':title' was moved to draft and is not visible on the website.", ['title' => $record->title]))
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle')
                            ->send();
                    }),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label(__('Status'))
                    ->trueLabel(__('Published only'))
                    ->falseLabel(__('Drafts only')),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('Edit article')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('Delete article')),
            ]);
    }
}
