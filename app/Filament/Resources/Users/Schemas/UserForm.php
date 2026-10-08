<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Cuenta')
                    ->description('Información de acceso y credenciales de usuario.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->maxLength(255)
                                    ->helperText('Dejar en blanco si no deseas cambiar la contraseña existente.'),
                                Select::make('role')
                                    ->label('Rol de Acceso')
                                    ->options([
                                        UserRole::Admin->value => '👑 Administrador (Acceso Total)',
                                        UserRole::Receptionist->value => '📋 Recepción / Asistente (Gestión de Citas)',
                                        UserRole::Dentist->value => '🩺 Odontólogo / Especialista (Agenda Propia)',
                                    ])
                                    ->default(UserRole::Admin->value)
                                    ->required()
                                    ->live(),
                            ]),

                        Select::make('dentist_id')
                            ->label('Especialista Asociado')
                            ->relationship('dentist', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get): bool => $get('role') === UserRole::Dentist->value)
                            ->required(fn ($get): bool => $get('role') === UserRole::Dentist->value)
                            ->helperText('Vincula esta cuenta con el perfil del doctor para que solo vea sus pacientes y citas.'),
                    ]),
            ]);
    }
}
