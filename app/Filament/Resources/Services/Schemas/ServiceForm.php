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
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Detalles del Servicio / Tratamiento' : 'Service Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make($isSpanish ? 'name_es' : 'name_en')
                                    ->label($isSpanish ? 'Nombre del Tratamiento' : 'Treatment Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        if (filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->maxLength(255),
                                TextInput::make($isSpanish ? 'category_es' : 'category_en')
                                    ->label($isSpanish ? 'Categoría' : 'Category')
                                    ->placeholder($isSpanish ? 'ej. Odontología Estética, Ortodoncia' : 'e.g. Cosmetic Dentistry, Orthodontics')
                                    ->maxLength(100),
                            ]),

                        TextInput::make($isSpanish ? 'short_description_es' : 'short_description_en')
                            ->label($isSpanish ? 'Descripción Breve' : 'Short Description')
                            ->placeholder($isSpanish ? 'Resumen para las tarjetas de la web...' : 'Summary for homepage cards...')
                            ->maxLength(255),

                        Textarea::make($isSpanish ? 'description_es' : 'description_en')
                            ->label($isSpanish ? 'Descripción Completa y Protocolo Clínico' : 'Full Description & Clinical Protocol')
                            ->rows(4),
                    ]),

                Section::make($isSpanish ? 'Precios, Duración y Multimedia' : 'Pricing, Duration & Media')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('price')
                                    ->label($isSpanish ? 'Precio Base ($)' : 'Starting Price ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('85.00'),
                                TextInput::make('duration_minutes')
                                    ->label($isSpanish ? 'Duración Estándar (Minutos)' : 'Standard Duration (Minutes)')
                                    ->numeric()
                                    ->default(45),
                            ]),

                        FileUpload::make('image')
                            ->label($isSpanish ? 'Imagen del Tratamiento' : 'Treatment Image')
                            ->image()
                            ->directory('services')
                            ->disk('public'),

                        Select::make('dentists')
                            ->label($isSpanish ? 'Doctores / Especialistas Habilitados' : 'Authorized Dentists')
                            ->multiple()
                            ->relationship('dentists', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText($isSpanish ? 'Especialistas asignados a este tratamiento.' : 'Dentists authorized to perform this treatment.'),

                        Grid::make(4)
                            ->schema([
                                Toggle::make('allows_online_booking')
                                    ->label($isSpanish ? 'Permitir Reserva Online' : 'Allow Online Booking')
                                    ->default(true),
                                Toggle::make('is_featured')
                                    ->label($isSpanish ? 'Destacar en Inicio' : 'Feature on Homepage')
                                    ->default(false),
                                Toggle::make('is_active')
                                    ->label($isSpanish ? 'Servicio Activo' : 'Active Service')
                                    ->default(true),
                                TextInput::make('order')
                                    ->label($isSpanish ? 'Orden' : 'Sort Order')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
