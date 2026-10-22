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
        return $schema
            ->components([
                Section::make(__('Case Details'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('Case Title'))
                            ->placeholder(__('e.g. Full Teeth Alignment with Clear Aligners'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__('Clinical Description'))
                            ->rows(3),
                        Textarea::make('patient_testimonial')
                            ->label(__('Patient Testimonial'))
                            ->rows(2),
                    ]),

                Section::make(__('Comparison Photos & Assignment'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('before_image')
                                    ->label(__('Before Photo (Initial State)'))
                                    ->image()
                                    ->directory('cases')
                                    ->disk('public')
                                    ->required(),
                                FileUpload::make('after_image')
                                    ->label(__('After Photo (Final Result)'))
                                    ->image()
                                    ->directory('cases')
                                    ->disk('public')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('service_id')
                                    ->label(__('Associated Service'))
                                    ->relationship('service', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('dentist_id')
                                    ->label(__('Specialist in Charge'))
                                    ->relationship('dentist', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label(__('Featured in Gallery'))
                                    ->default(true),
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
