<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Pregunta y Respuesta' : 'FAQ Content')
                    ->schema([
                        TextInput::make($isSpanish ? 'category_es' : 'category_en')
                            ->label($isSpanish ? 'Categoría' : 'Category')
                            ->placeholder($isSpanish ? 'ej. Citas y Reservas, Precios y Seguros' : 'e.g. Appointments & Booking, Pricing & Insurance')
                            ->maxLength(100),
                        TextInput::make($isSpanish ? 'question_es' : 'question_en')
                            ->label($isSpanish ? 'Pregunta' : 'Question')
                            ->required()
                            ->maxLength(255),
                        Textarea::make($isSpanish ? 'answer_es' : 'answer_en')
                            ->label($isSpanish ? 'Respuesta' : 'Answer')
                            ->required()
                            ->rows(4),
                    ]),

                Section::make($isSpanish ? 'Orden y Visibilidad' : 'Display Order & Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
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
