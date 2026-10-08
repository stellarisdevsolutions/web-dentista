<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Servicio / Tratamiento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Tratamiento')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        if (filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->maxLength(255),
                                TextInput::make('category')
                                    ->label('Categoría')
                                    ->placeholder('ej. Odontología Estética, Ortodoncia')
                                    ->maxLength(100),
                            ]),

                        TextInput::make('short_description')
                            ->label('Descripción Breve')
                            ->placeholder('Resumen para las tarjetas de la web...')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descripción Completa y Protocolo Clínico')
                            ->rows(4),
                    ]),

                Section::make('Precios, Duración y Multimedia')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('price')
                                    ->label('Precio Base ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('85.00'),
                                TextInput::make('duration_minutes')
                                    ->label('Duración Estándar (Minutos)')
                                    ->numeric()
                                    ->default(45),
                            ]),

                        FileUpload::make('image')
                            ->label('Imagen del Tratamiento')
                            ->image()
                            ->directory('services')
                            ->disk('public'),

                        Select::make('dentists')
                            ->label('Doctores / Especialistas Habilitados')
                            ->multiple()
                            ->relationship('dentists', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Especialistas asignados a este tratamiento.'),

                        Grid::make(4)
                            ->schema([
                                Toggle::make('allows_online_booking')
                                    ->label('Permitir Reserva Online')
                                    ->default(true),
                                Toggle::make('is_featured')
                                    ->label('Destacar en Inicio')
                                    ->default(false),
                                Toggle::make('is_active')
                                    ->label('Servicio Activo')
                                    ->default(true),
                                TextInput::make('order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
