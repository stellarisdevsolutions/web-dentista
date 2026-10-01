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
                                TextInput::make($isSpanish ? 'badge_es' : 'badge_en')
                                    ->label($isSpanish ? 'Etiqueta Superior' : 'Top Badge')
                                    ->placeholder($isSpanish ? 'ej. Especial de Primavera' : 'e.g. Spring Smile Makeover')
                                    ->maxLength(100),
                                TextInput::make($isSpanish ? 'button_text_es' : 'button_text_en')
                                    ->label($isSpanish ? 'Texto del Botón CTA' : 'Button CTA Text')
                                    ->placeholder($isSpanish ? 'ej. Agenda tu Consulta' : 'e.g. Book Your Consultation')
                                    ->maxLength(100),
                            ]),

                        TextInput::make($isSpanish ? 'title_es' : 'title_en')
                            ->label($isSpanish ? 'Título Principal del Hero' : 'Hero Main Title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make($isSpanish ? 'subtitle_es' : 'subtitle_en')
                            ->label($isSpanish ? 'Subtítulo / Bajada' : 'Subtitle / Tagline')
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
