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
                Section::make(__('Role & System Privileges'))
                    ->description(__('Select access role first. If Dentist, link to their medical profile to automatically associate their name.'))
                    ->icon('heroicon-o-shield-check')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                Select::make('role')
                                    ->label(__('Access Role'))
                                    ->prefixIcon('heroicon-o-shield-check')
                                    ->options([
                                        UserRole::Admin->value => __('Administrator (Full Access & Settings)'),
                                        UserRole::Receptionist->value => __('Receptionist / Assistant (Appointments & Patient Management)'),
                                        UserRole::Dentist->value => __('Dentist / Specialist (Own Medical Schedule)'),
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
                                    ->helperText(__('Determines which sections and permissions this user will access.')),

                                Select::make('dentist_id')
                                    ->label(__('Associated Medical Specialist'))
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
                                    ->helperText(__('Select the team doctor. The name field will be linked automatically.'))
                                    ->columnSpan(1),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: IDENTIDAD Y CREDENCIALES
                // ==========================================
                Section::make(__('Identity & Access Credentials'))
                    ->description(__('Official name, login email, and admin panel password.'))
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Full Name'))
                                    ->prefixIcon('heroicon-o-user')
                                    ->placeholder('Carlos Méndez / Dra. Ana Morales')
                                    ->required()
                                    ->maxLength(255)
                                    ->readOnly(fn (callable $get): bool => $get('role') === UserRole::Dentist->value)
                                    ->extraInputAttributes(fn (callable $get): array => [
                                        'class' => $get('role') === UserRole::Dentist->value
                                            ? 'bg-slate-100/80 dark:bg-slate-900/60 cursor-default opacity-90'
                                            : '',
                                    ])
                                    ->helperText(fn (callable $get): string => $get('role') === UserRole::Dentist->value
                                        ? __('Directly linked to selected medical specialist (read-only).')
                                        : __('User name displayed in the system and audit logs.')
                                    ),

                                TextInput::make('email')
                                    ->label(__('Email Address'))
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->placeholder('usuario@auradental.com')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText(__('Email used to log in to the admin panel.')),
                            ]),

                        TextInput::make('password')
                            ->label(__('Access Password'))
                            ->prefixIcon('heroicon-o-key')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText(fn (string $operation): string => $operation === 'create'
                                ? __('Minimum 8 characters recommended. Securely encrypted.')
                                : __('Leave blank if you do not want to modify current password.')
                            ),
                    ]),
            ]);
    }
}
