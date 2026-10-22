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
        return $schema
            ->components([
                Section::make(__('FAQ Content'))
                    ->schema([
                        TextInput::make('category')
                            ->label(__('Category'))
                            ->placeholder(__('e.g. Appointments & Bookings, Pricing & Insurance'))
                            ->maxLength(100),
                        TextInput::make('question')
                            ->label(__('Question'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('answer')
                            ->label(__('Answer'))
                            ->required()
                            ->rows(4),
                    ]),

                Section::make(__('Display Order & Status'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label(__('Visible on Website'))
                                    ->default(true),
                                TextInput::make('order')
                                    ->label(__('Sort Order'))
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
