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
        return $schema
            ->components([
                Section::make(__('Reviewer Profile'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('patient_name')
                                    ->label(__('Patient Full Name'))
                                    ->required()
                                    ->maxLength(255),
                                Select::make('rating')
                                    ->label(__('Rating (Stars)'))
                                    ->options([
                                        5 => '5 / 5 ('.__('5 stars').')',
                                        4 => '4 / 5 ('.__('4 stars').')',
                                        3 => '3 / 5 ('.__('3 stars').')',
                                        2 => '2 / 5 ('.__('2 stars').')',
                                        1 => '1 / 5 ('.__('1 star').')',
                                    ])
                                    ->default(5)
                                    ->required(),
                                TextInput::make('source')
                                    ->label(__('Review Source'))
                                    ->placeholder(__('Google Reviews, Verified Patient'))
                                    ->default('Google Reviews')
                                    ->maxLength(100),
                            ]),

                        FileUpload::make('patient_avatar')
                            ->label(__('Patient Photo (Optional)'))
                            ->image()
                            ->directory('testimonials')
                            ->disk('public'),
                    ]),

                Section::make(__('Review Content'))
                    ->schema([
                        TextInput::make('treatment')
                            ->label(__('Completed Treatment'))
                            ->placeholder(__('e.g. Laser Teeth Whitening'))
                            ->maxLength(150),
                        Textarea::make('comment')
                            ->label(__('Patient Review / Comment'))
                            ->required()
                            ->rows(4),
                    ]),

                Section::make(__('Status & Visibility'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_verified')
                                    ->label(__('Verified Patient'))
                                    ->default(true),
                                Toggle::make('is_active')
                                    ->label(__('Display on Website'))
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
