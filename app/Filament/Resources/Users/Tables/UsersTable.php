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
            ->emptyStateHeading('No hay usuarios registrados')
            ->emptyStateDescription('Comienza añadiendo los miembros del equipo clínico y asignando sus roles.')
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                TextColumn::make('avatar')
                    ->label('')
                    ->view('filament.resources.users.columns.avatar-column'),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Correo copiado al portapapeles')
                    ->icon('heroicon-o-envelope')
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Rol de Acceso')
                    ->badge()
                    ->icon(fn (UserRole $state): string => $state->icon())
                    ->color(fn (UserRole $state): string => $state->color())
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('dentist.name')
                    ->label('Especialista Vinculado')
                    ->badge()
                    ->icon(fn (?string $state): ?string => $state ? 'heroicon-m-academic-cap' : null)
                    ->color('gray')
                    ->placeholder('Sin vincular')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha Alta')
                    ->dateTime('d M, Y')
                    ->sortable(),
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
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar usuario'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar usuario')
                    ->visible(fn (User $record): bool => auth()->id() !== $record->id),
            ]);
    }
}
