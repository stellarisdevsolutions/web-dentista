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
                Section::make(__('Article Main Information & Cover'))
                    ->description(__('Define title, permalink (slug), category, and main photography for the educational article.'))
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
                                            ->label(__('Cover Image (16:9)'))
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
                                            ->helperText(__('Required. Recommended format 16:9 (1280×720 px). Automatically optimized to WebP for maximum website speed.')),
                                    ]),

                                // Columna Derecha: Título, Categoría, Publicación y Slug Híbrido
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 8])
                                    ->schema([
                                        Hidden::make('is_slug_editable')
                                            ->default(false)
                                            ->dehydrated(false),

                                        TextInput::make('title')
                                            ->label(__('Article Title'))
                                            ->prefixIcon('heroicon-o-pencil-square')
                                            ->placeholder(__('e.g. 7 Essential Tips to Prevent Dental Cavities'))
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
                                            ->label(__('Category'))
                                            ->prefixIcon('heroicon-o-tag')
                                            ->placeholder(__('e.g. Prevention, Orthodontics, Cosmetic Dentistry'))
                                            ->datalist(fn (): array => Post::query()
                                                ->whereNotNull('category')
                                                ->where('category', '!=', '')
                                                ->distinct()
                                                ->pluck('category')
                                                ->toArray())
                                            ->helperText(__('Select from suggestions or type a new one.'))
                                            ->maxLength(100),

                                        DateTimePicker::make('published_at')
                                            ->label(__('Publication Date & Time'))
                                            ->prefixIcon('heroicon-o-calendar')
                                            ->default(now())
                                            ->required()
                                            ->helperText(__('Date visible to readers.')),

                                        TextInput::make('slug')
                                            ->label(__('Web Link / Slug (URL)'))
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
                                                    ->tooltip(fn (callable $get): string => $get('is_slug_editable') ? __('Lock link editing') : __('Customize web link'))
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
                                                ? __('Edit mode active: customize link to your liking (no accents or spaces; collisions resolve automatically).')
                                                : __('Automatically generated from title. Protected against accidental changes (click pencil to customize).')
                                            ),

                                        Toggle::make('is_published')
                                            ->label(__('Published on the Web'))
                                            ->helperText(__('Enables public visibility of this article in the blog section.'))
                                            ->default(true)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: RESUMEN Y CONTENIDO DEL ARTÍCULO
                // ==========================================
                Section::make(__('Article Body & Writing'))
                    ->description(__('Write featured summary for catalog cards and complete editorial body.'))
                    ->icon('heroicon-o-book-open')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('excerpt')
                            ->label(__('Article Excerpt'))
                            ->placeholder(__('Brief teaser or synopsis for blog cards and social previews...'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText(__('Optional. If left blank, first lines of content will be extracted automatically.')),

                        RichEditor::make('content')
                            ->label(__('Full Article Body'))
                            ->placeholder(__('Write editorial article content here with subheadings, lists, quotes, and clinical recommendations...'))
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
