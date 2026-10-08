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
                        TextInput::make('category')
                            ->label('Categoría')
                            ->placeholder('ej. Citas y Reservas, Precios y Seguros')
                            ->maxLength(100),
                        TextInput::make('question')
                            ->label('Pregunta')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('answer')
                            ->label('Respuesta')
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
