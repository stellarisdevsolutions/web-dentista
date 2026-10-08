<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (UserRole $state): string => $state->color())
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('dentist.name')
                    ->label('Especialista Vinculado')
                    ->placeholder('N/A')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->color(Color::Slate),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filtrar por Rol')
                    ->options([
                        UserRole::Admin->value => 'Administradores',
                        UserRole::Receptionist->value => 'Recepción',
                        UserRole::Dentist->value => 'Odontólogos',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (User $record): bool => auth()->id() !== $record->id),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
