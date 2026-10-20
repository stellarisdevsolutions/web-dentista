<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\Dentist;
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
                // ==========================================
                // SECCIÓN 1: ROL Y PRIVILEGIOS DE ACCESO
                // ==========================================
                Section::make('Rol y Privilegios en el Sistema')
                    ->description('Selecciona primero el rol de acceso. Si es Odontólogo, vincúlalo con su perfil médico para asociar automáticamente su nombre.')
                    ->icon('heroicon-o-shield-check')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                Select::make('role')
                                    ->label('Rol de Acceso')
                                    ->prefixIcon('heroicon-o-shield-check')
                                    ->options([
                                        UserRole::Admin->value => 'Administrador (Acceso Total y Ajustes)',
                                        UserRole::Receptionist->value => 'Recepción / Asistente (Gestión de Citas y Pacientes)',
                                        UserRole::Dentist->value => 'Odontólogo / Especialista (Agenda Médica Propia)',
                                    ])
                                    ->default(UserRole::Admin->value)
                                    ->required()
                                    ->live()
                                    ->columnSpan(fn (callable $get): int => $get('role') === UserRole::Dentist->value ? 1 : 2)
                                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                                        if ($state === UserRole::Dentist->value) {
                                            $dentistId = $get('dentist_id');
                                            if ($dentistId) {
                                                $dentist = Dentist::find($dentistId);
                                                if ($dentist) {
                                                    $set('name', $dentist->name);
                                                }
                                            }
                                        } else {
                                            $set('dentist_id', null);
                                        }
                                    })
                                    ->helperText('Determina a qué secciones y permisos tendrá acceso este usuario.'),

                                Select::make('dentist_id')
                                    ->label('Especialista Médico Asociado')
                                    ->prefixIcon('heroicon-o-academic-cap')
                                    ->relationship('dentist', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (callable $get): bool => $get('role') === UserRole::Dentist->value)
                                    ->required(fn (callable $get): bool => $get('role') === UserRole::Dentist->value)
                                    ->live()
                                    ->afterStateUpdated(function (?string $state, callable $set): void {
                                        if ($state) {
                                            $dentist = Dentist::find($state);
                                            if ($dentist) {
                                                $set('name', $dentist->name);
                                            }
                                        }
                                    })
                                    ->helperText('Selecciona el doctor del equipo. El campo de nombre se vinculará de forma automática.')
                                    ->columnSpan(1),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: IDENTIDAD Y CREDENCIALES
                // ==========================================
                Section::make('Identidad y Credenciales de Acceso')
                    ->description('Nombre oficial, correo de inicio de sesión y contraseña del panel administrativo.')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->prefixIcon('heroicon-o-user')
                                    ->placeholder('ej. Carlos Méndez / Lic. Ana Morales')
                                    ->required()
                                    ->maxLength(255)
                                    ->readOnly(fn (callable $get): bool => $get('role') === UserRole::Dentist->value)
                                    ->extraInputAttributes(fn (callable $get): array => [
                                        'class' => $get('role') === UserRole::Dentist->value
                                            ? 'bg-slate-100/80 dark:bg-slate-900/60 cursor-default opacity-90'
                                            : '',
                                    ])
                                    ->helperText(fn (callable $get): string => $get('role') === UserRole::Dentist->value
                                        ? 'Vinculado directamente con el especialista médico seleccionado (solo lectura).'
                                        : 'Nombre del usuario que aparecerá en el sistema y auditorías.'
                                    ),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->placeholder('usuario@auradental.com')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Correo utilizado para iniciar sesión en el panel.'),
                            ]),

                        TextInput::make('password')
                            ->label('Contraseña de Acceso')
                            ->prefixIcon('heroicon-o-key')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText(fn (string $operation): string => $operation === 'create'
                                ? 'Mínimo 8 caracteres recomendado. Se encriptará de forma segura.'
                                : 'Dejar este campo vacío si no deseas modificar la contraseña actual.'
                            ),
                    ]),
            ]);
    }
}
