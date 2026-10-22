<?php

namespace App\Filament\Resources\Dentists\Tables;

use App\Models\Dentist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
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
            ->recordClasses(fn (Dentist $record): string => "cursor-pointer dentist-row dentist-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->header(view('filament.resources.dentists.table-header'))
            ->emptyStateHeading(__('No specialists registered'))
            ->emptyStateDescription(__('Start by adding clinic doctors and surgeons.'))
            ->emptyStateIcon('heroicon-o-user-group')
            ->columns([
                TextColumn::make('photo')
                    ->label(__('Photo'))
                    ->view('filament.resources.dentists.columns.photo-column'),

                TextColumn::make('name')
                    ->label(__('Specialist'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record): string => $record->title ?: ($record->email ?? '—')),

                TextColumn::make('specialty')
                    ->label(__('Specialty'))
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-academic-cap')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label(__('Contact'))
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage(__('Phone copied to clipboard'))
                    ->placeholder(__('No phone')),

                TextColumn::make('license_number')
                    ->label(__('Medical License'))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('services_count')
                    ->counts('services')
                    ->label(__('Treatments'))
                    ->badge()
                    ->color('primary')
                    ->suffix(' '.__('serv.'))
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label(__('Available'))
                    ->onColor('success')
                    ->offColor('gray')
                    ->afterStateUpdated(function (Dentist $record, bool $state): void {
                        Notification::make()
                            ->title($state ? __('Availability activated') : __('Availability paused'))
                            ->body($state ? __(':name is now available for appointments.', ['name' => $record->name]) : __(':name will no longer receive new appointments.', ['name' => $record->name]))
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle')
                            ->send();
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Availability'))
                    ->trueLabel(__('Available only'))
                    ->falseLabel(__('Unavailable')),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('Edit specialist')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('Delete specialist')),
            ]);
    }
}
