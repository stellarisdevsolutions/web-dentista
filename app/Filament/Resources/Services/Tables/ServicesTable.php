<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
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

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->recordClasses(fn (Service $record): string => "cursor-pointer service-row service-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->header(view('filament.resources.services.table-header'))
            ->emptyStateHeading(__('No treatments registered'))
            ->emptyStateDescription(__('Start by adding clinic services and dental procedures.'))
            ->emptyStateIcon('heroicon-o-sparkles')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Image'))
                    ->disk('public')
                    ->defaultImageUrl(fn (Service $record): string => $record->image_url)
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-xs border border-slate-200/80'])
                    ->size(46),

                TextColumn::make('name')
                    ->label(__('Treatment'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Service $record): string => $record->short_description ? Str::limit($record->short_description, 50) : '—'),

                TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('price')
                    ->label(__('Base Price'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label(__('Duration'))
                    ->suffix(' min')
                    ->icon('heroicon-o-clock')
                    ->sortable(),

                TextColumn::make('dentists_count')
                    ->counts('dentists')
                    ->label(__('Specialists'))
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-user-group')
                    ->sortable(),

                ToggleColumn::make('allows_online_booking')
                    ->label(__('Online Booking'))
                    ->afterStateUpdated(function (Service $record, bool $state): void {
                        Notification::make()
                            ->title($state ? __('Online booking enabled') : __('Online booking paused'))
                            ->body($state ? __("Patients can book ':name' on the website.", ['name' => $record->name]) : __("Online booking paused for ':name'.", ['name' => $record->name]))
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-globe-alt' : 'heroicon-o-pause-circle')
                            ->send();
                    }),

                ToggleColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->afterStateUpdated(function (Service $record, bool $state): void {
                        Notification::make()
                            ->title($state ? __('Featured treatment') : __('Not featured treatment'))
                            ->body($state ? __("':name' is now featured on the website homepage.", ['name' => $record->name]) : __("':name' removed from website homepage.", ['name' => $record->name]))
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-star' : 'heroicon-o-minus-circle')
                            ->send();
                    }),

                ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->afterStateUpdated(function (Service $record, bool $state): void {
                        Notification::make()
                            ->title($state ? __('Treatment activated') : __('Treatment deactivated'))
                            ->body($state ? __("':name' is active in catalog and system.", ['name' => $record->name]) : __("':name' deactivated from catalog.", ['name' => $record->name]))
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle')
                            ->send();
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
                TernaryFilter::make('allows_online_booking')
                    ->label(__('Online Booking Allowed')),
                TernaryFilter::make('is_featured')
                    ->label(__('Featured on Home')),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('Edit treatment')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('Delete treatment')),
            ]);
    }
}
