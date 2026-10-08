<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Contenido del Artículo' : 'Article Content')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Título del Artículo')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        if (filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->maxLength(255),
                                TextInput::make('category')
                                    ->label('Categoría')
                                    ->placeholder('ej. Prevención, Ortodoncia, Estética')
                                    ->maxLength(100),
                            ]),

                        Textarea::make('excerpt')
                            ->label('Resumen del Artículo')
                            ->placeholder('Breve adelanto para las tarjetas del blog...')
                            ->rows(2),

                        RichEditor::make('content')
                            ->label('Cuerpo Completo del Artículo'),
                    ]),

                Section::make($isSpanish ? 'Publicación e Imagen Destacada' : 'Publishing & Featured Media')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                DateTimePicker::make('published_at')
                                    ->label($isSpanish ? 'Fecha y Hora de Publicación' : 'Publication Date & Time')
                                    ->default(now()),
                                Toggle::make('is_published')
                                    ->label($isSpanish ? 'Publicado en Vivo' : 'Published Live')
                                    ->default(false),
                            ]),

                        FileUpload::make('cover_image')
                            ->label($isSpanish ? 'Imagen de Portada' : 'Cover Image')
                            ->image()
                            ->directory('blog')
                            ->disk('public'),
                    ]),
            ]);
    }
}
