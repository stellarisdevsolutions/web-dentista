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
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Textos del Banner Principal' : 'Hero Banner Texts')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('badge')
                                    ->label('Etiqueta Superior')
                                    ->placeholder('ej. Especial de Primavera')
                                    ->maxLength(100),
                                TextInput::make('button_text')
                                    ->label('Texto del Botón CTA')
                                    ->placeholder('ej. Agenda tu Consulta')
                                    ->maxLength(100),
                            ]),

                        TextInput::make('title')
                            ->label('Título Principal del Hero')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('subtitle')
                            ->label('Subtítulo / Bajada')
                            ->rows(3),
                    ]),

                Section::make($isSpanish ? 'Configuración de Enlace y Fondo' : 'Display & Action Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('button_url')
                                    ->label($isSpanish ? 'Enlace de Destino del Botón' : 'Button Target URL / Anchor')
                                    ->placeholder('#booking o https://...')
                                    ->maxLength(255),
                                FileUpload::make('background_image')
                                    ->label($isSpanish ? 'Imagen de Fondo' : 'Hero Background Image')
                                    ->image()
                                    ->directory('banners')
                                    ->disk('public'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label($isSpanish ? 'Activo en la Web' : 'Active / Visible')
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
