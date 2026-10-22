<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Hero Banner Texts'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('badge')
                                    ->label(__('Top Badge'))
                                    ->placeholder(__('e.g. Spring Special'))
                                    ->maxLength(100),
                                TextInput::make('button_text')
                                    ->label(__('Button CTA Text'))
                                    ->placeholder(__('e.g. Book your Consultation'))
                                    ->maxLength(100),
                            ]),

                        TextInput::make('title')
                            ->label(__('Hero Main Title'))
                            ->required()
                            ->maxLength(255),

                        Textarea::make('subtitle')
                            ->label(__('Subtitle / Tagline'))
                            ->rows(3),
                    ]),

                Section::make(__('Display & Action Settings'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('button_url')
                                    ->label(__('Button Target URL / Anchor'))
                                    ->placeholder('#booking '.__('or').' https://...')
                                    ->maxLength(255),
                                FileUpload::make('background_image')
                                    ->label(__('Hero Background Image'))
                                    ->image()
                                    ->directory('banners')
                                    ->disk('public'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label(__('Active / Visible'))
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
