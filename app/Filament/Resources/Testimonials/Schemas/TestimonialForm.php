<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Perfil del Paciente' : 'Reviewer Profile')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('patient_name')
                                    ->label($isSpanish ? 'Nombre Completo del Paciente' : 'Patient Full Name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('rating')
                                    ->label($isSpanish ? 'Calificación (Estrellas)' : 'Rating (Stars)')
                                    ->options([
                                        5 => '★★★★★ (5 Estrellas)',
                                        4 => '★★★★☆ (4 Estrellas)',
                                        3 => '★★★☆☆ (3 Estrellas)',
                                        2 => '★★☆☆☆ (2 Estrellas)',
                                        1 => '★☆☆☆☆ (1 Estrella)',
                                    ])
                                    ->default(5)
                                    ->required(),
                                TextInput::make('source')
                                    ->label($isSpanish ? 'Fuente de la Reseña' : 'Review Source')
                                    ->placeholder('Google Reviews, Paciente Verificado')
                                    ->default('Google Reviews')
                                    ->maxLength(100),
                            ]),

                        FileUpload::make('patient_avatar')
                            ->label($isSpanish ? 'Foto del Paciente (Opcional)' : 'Patient Photo (Optional)')
                            ->image()
                            ->directory('testimonials')
                            ->disk('public'),
                    ]),

                Section::make($isSpanish ? 'Opinión del Tratamiento' : 'Review Content')
                    ->schema([
                        TextInput::make('treatment')
                            ->label('Tratamiento Realizado')
                            ->placeholder('ej. Blanqueamiento Dental Láser')
                            ->maxLength(150),
                        Textarea::make('comment')
                            ->label('Testimonio / Comentario del Paciente')
                            ->required()
                            ->rows(4),
                    ]),

                Section::make($isSpanish ? 'Estado y Visibilidad' : 'Status & Visibility')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_verified')
                                    ->label($isSpanish ? 'Paciente Verificado' : 'Verified Patient')
                                    ->default(true),
                                Toggle::make('is_active')
                                    ->label($isSpanish ? 'Mostrar en la Web' : 'Display on Website')
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
