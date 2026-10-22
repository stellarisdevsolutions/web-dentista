<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (User $record): string => "cursor-pointer user-row user-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->header(view('filament.resources.users.table-header'))
            ->emptyStateHeading(__('No users registered'))
            ->emptyStateDescription(__('Start by adding clinic team members and assigning their roles.'))
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                TextColumn::make('avatar')
                    ->label('')
                    ->view('filament.resources.users.columns.avatar-column'),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('email')
                    ->label(__('Email Address'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('Email copied to clipboard'))
                    ->icon('heroicon-o-envelope')
                    ->sortable(),

                TextColumn::make('role')
                    ->label(__('Access Role'))
                    ->badge()
                    ->icon(fn (UserRole $state): string => $state->icon())
                    ->color(fn (UserRole $state): string => $state->color())
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('dentist.name')
                    ->label(__('Linked Specialist'))
                    ->badge()
                    ->icon(fn (?string $state): ?string => $state ? 'heroicon-m-academic-cap' : null)
                    ->color('gray')
                    ->placeholder(__('Unlinked'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Registration Date'))
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Filter by Role'))
                    ->options([
                        UserRole::Admin->value => __('Administrators'),
                        UserRole::Receptionist->value => __('Receptionists'),
                        UserRole::Dentist->value => __('Dentists'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('Edit user')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip(__('Delete user'))
                    ->visible(fn (User $record): bool => auth()->id() !== $record->id),
            ]);
    }
}
