<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        $appUrl = config('app.url', 'http://localhost:8000');
        $host = preg_replace('#^https?://#', '', rtrim((string) $appUrl, '/'));
        $urlPrefix = $host.'/blog/';

        return $schema
            ->components([
                // ==========================================
                // SECCIÓN 1: IDENTIDAD EDITORIAL Y PORTADA
                // ==========================================
                Section::make('Información Principal y Portada del Artículo')
                    ->description('Define el título, enlace permanente (slug), categoría y la fotografía principal del artículo educativo.')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'lg' => 12])
                            ->schema([
                                // Columna Izquierda: Fotografía de Portada (Card 16:9)
                                Grid::make(1)
                                    ->columnSpan(['default' => 12, 'lg' => 4, 'xl' => 4])
                                    ->schema([
                                        FileUpload::make('cover_image')
                                            ->label('Imagen de Portada (16:9)')
                                            ->image()
                                            ->required()
                                            ->imageCropAspectRatio('16:9')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('1280')
                                            ->imageResizeTargetHeight('720')
                                            ->imagePreviewHeight('220')
                                            ->directory('blog')
                                            ->disk('public')
                                            ->helperText('Obligatorio. Formato recomendado 16:9 (1280×720 px). Se optimiza automáticamente a WebP para máxima velocidad en la web.'),
                                    ]),

                                // Columna Derecha: Título, Categoría, Publicación y Slug Híbrido
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 8])
                                    ->schema([
                                        Hidden::make('is_slug_editable')
                                            ->default(false)
                                            ->dehydrated(false),

                                        TextInput::make('title')
                                            ->label('Título del Artículo')
                                            ->prefixIcon('heroicon-o-pencil-square')
                                            ->placeholder('ej. 7 Consejos Esenciales para Prevenir la Caries Dental')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->live(debounce: 300)
                                            ->afterStateUpdated(function (?string $state, callable $set, callable $get, ?string $operation, ?Post $record): void {
                                                if ($operation === 'create' && filled($state) && ! $get('is_slug_editable')) {
                                                    $set('slug', Post::generateUniqueSlug($state, $record?->id));
                                                }
                                            }),

                                        TextInput::make('category')
                                            ->label('Categoría')
                                            ->prefixIcon('heroicon-o-tag')
                                            ->placeholder('ej. Prevención, Ortodoncia, Estética Dental')
                                            ->datalist(fn (): array => Post::query()
                                                ->whereNotNull('category')
                                                ->where('category', '!=', '')
                                                ->distinct()
                                                ->pluck('category')
                                                ->toArray())
                                            ->helperText('Selecciona de las sugerencias o escribe una nueva.')
                                            ->maxLength(100),

                                        DateTimePicker::make('published_at')
                                            ->label('Fecha y Hora de Publicación')
                                            ->prefixIcon('heroicon-o-calendar')
                                            ->default(now())
                                            ->required()
                                            ->helperText('Fecha visible para los lectores.'),

                                        TextInput::make('slug')
                                            ->label('Enlace Web / Slug (URL)')
                                            ->prefixIcon('heroicon-o-link')
                                            ->prefix($urlPrefix)
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->readOnly(fn (callable $get): bool => ! (bool) $get('is_slug_editable'))
                                            ->extraInputAttributes(fn (callable $get): array => [
                                                'class' => $get('is_slug_editable')
                                                    ? 'bg-white dark:bg-slate-900 ring-2 ring-sky-500'
                                                    : 'bg-slate-100/70 dark:bg-slate-900/60 cursor-default opacity-90',
                                            ])
                                            ->suffixAction(
                                                Action::make('toggle_slug_edit')
                                                    ->icon(fn (callable $get): string => $get('is_slug_editable') ? 'heroicon-m-check' : 'heroicon-m-pencil-square')
                                                    ->tooltip(fn (callable $get): string => $get('is_slug_editable') ? 'Bloquear edición de enlace' : 'Personalizar enlace web')
                                                    ->color(fn (callable $get): string => $get('is_slug_editable') ? 'success' : 'gray')
                                                    ->action(function (callable $set, callable $get): void {
                                                        $set('is_slug_editable', ! $get('is_slug_editable'));
                                                    })
                                            )
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, callable $set, ?Post $record): void {
                                                if (filled($state)) {
                                                    $set('slug', Post::generateUniqueSlug($state, $record?->id));
                                                }
                                            })
                                            ->dehydrateStateUsing(fn (?string $state, ?Post $record) => blank($state) ? null : Post::generateUniqueSlug($state, $record?->id))
                                            ->helperText(fn (callable $get): string => $get('is_slug_editable')
                                                ? 'Modo edición activado: personaliza el enlace a tu gusto (sin acentos ni espacios; las colisiones se resuelven solas).'
                                                : 'Generado automáticamente según el título. Protegido contra cambios accidentales (pulsa el lápiz para personalizar).'
                                            ),

                                        Toggle::make('is_published')
                                            ->label('Publicado en la Web')
                                            ->helperText('Habilita la visibilidad pública de este artículo en la sección de blog.')
                                            ->default(true)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: RESUMEN Y CONTENIDO DEL ARTÍCULO
                // ==========================================
                Section::make('Cuerpo y Redacción del Artículo')
                    ->description('Escribe el resumen destacado para las tarjetas del catálogo y el desarrollo editorial completo.')
                    ->icon('heroicon-o-book-open')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('excerpt')
                            ->label('Resumen del Artículo (Extracto)')
                            ->placeholder('Breve adelanto o sinopsis para las tarjetas del blog y vistas previas en redes sociales...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Opcional. Si se deja en blanco, se extraerán automáticamente las primeras líneas del contenido.'),

                        RichEditor::make('content')
                            ->label('Cuerpo Completo del Artículo')
                            ->placeholder('Escribe aquí el contenido editorial del artículo con subtítulos, listas, citas y recomendaciones clínicas...')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
