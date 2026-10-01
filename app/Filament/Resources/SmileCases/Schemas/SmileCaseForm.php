<?php

namespace App\Filament\Resources\SmileCases\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SmileCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Detalles del Caso Clínico' : 'Case Details')
                    ->schema([
                        TextInput::make($isSpanish ? 'title_es' : 'title_en')
                            ->label($isSpanish ? 'Título del Caso' : 'Case Title')
                            ->placeholder($isSpanish ? 'ej. Alineación Dental Completa con Alineadores' : 'e.g. Full Smile Alignment with Clear Aligners')
                            ->required()
                            ->maxLength(255),
                        Textarea::make($isSpanish ? 'description_es' : 'description_en')
                            ->label($isSpanish ? 'Descripción Clínica del Caso' : 'Clinical Case Description')
                            ->rows(3),
                        Textarea::make($isSpanish ? 'patient_testimonial_es' : 'patient_testimonial_en')
                            ->label($isSpanish ? 'Testimonio del Paciente' : 'Patient Testimonial')
                            ->rows(2),
                    ]),

                Section::make($isSpanish ? 'Fotografías Antes/Después y Asignación' : 'Comparison Photos & Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('before_image')
                                    ->label($isSpanish ? 'Foto Antes (Estado Inicial)' : 'Before Photo')
                                    ->image()
                                    ->directory('cases')
                                    ->disk('public')
                                    ->required(),
                                FileUpload::make('after_image')
                                    ->label($isSpanish ? 'Foto Después (Resultado Final)' : 'After Photo')
                                    ->image()
                                    ->directory('cases')
                                    ->disk('public')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('service_id')
                                    ->label($isSpanish ? 'Tratamiento Asociado' : 'Associated Treatment')
                                    ->relationship('service', $isSpanish ? 'name_es' : 'name_en')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('dentist_id')
                                    ->label($isSpanish ? 'Especialista a Cargo' : 'Specialist in Charge')
                                    ->relationship('dentist', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label($isSpanish ? 'Destacar en Galería' : 'Featured in Gallery')
                                    ->default(true),
                                Toggle::make('is_active')
                                    ->label($isSpanish ? 'Visible en la Web' : 'Visible on Website')
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
