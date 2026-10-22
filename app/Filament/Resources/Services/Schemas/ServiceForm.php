<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        $appUrl = config('app.url', 'http://localhost:8000');
        $host = preg_replace('#^https?://#', '', rtrim((string) $appUrl, '/'));
        $urlPrefix = $host.'/servicios/';

        return $schema
            ->components([
                // ==========================================
                // SECCIÓN 1: INFORMACIÓN PRINCIPAL Y MULTIMEDIA
                // ==========================================
                Section::make(__('Main Information & Media'))
                    ->description(__('Define title, permalink (slug), category, and official photography for the treatment.'))
                    ->icon('heroicon-o-sparkles')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'lg' => 12])
                            ->schema([
                                // Columna Izquierda: Fotografía Oficial (Card 4:3)
                                Grid::make(1)
                                    ->columnSpan(['default' => 12, 'lg' => 4, 'xl' => 4])
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label(__('Treatment Photo (4:3)'))
                                            ->image()
                                            ->required()
                                            ->imageCropAspectRatio('4:3')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '4:3',
                                                '16:9',
                                                '1:1',
                                            ])
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('1200')
                                            ->imageResizeTargetHeight('900')
                                            ->imagePreviewHeight('220')
                                            ->directory('services')
                                            ->disk('public')
                                            ->helperText(__('Required. Recommended format 4:3 (1200×900 px). Automatically optimized to WebP for maximum website speed.')),
                                    ]),

                                // Columna Derecha: Campos de Identidad, Categoría y Slug Híbrido
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 8])
                                    ->schema([
                                        Hidden::make('is_slug_editable')
                                            ->default(false)
                                            ->dehydrated(false),

                                        TextInput::make('name')
                                            ->label(__('Treatment Name'))
                                            ->prefixIcon('heroicon-o-sparkles')
                                            ->placeholder('ej. Blanqueamiento Dental Láser')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->live(debounce: 300)
                                            ->afterStateUpdated(function (?string $state, callable $set, callable $get, ?string $operation, ?Service $record): void {
                                                if ($operation === 'create' && filled($state) && ! $get('is_slug_editable')) {
                                                    $set('slug', Service::generateUniqueSlug($state, $record?->id));
                                                }
                                            }),

                                        TextInput::make('category')
                                            ->label(__('Category'))
                                            ->prefixIcon('heroicon-o-tag')
                                            ->placeholder('ej. Odontología Estética, Ortodoncia')
                                            ->datalist(fn (): array => Service::query()
                                                ->whereNotNull('category')
                                                ->where('category', '!=', '')
                                                ->distinct()
                                                ->pluck('category')
                                                ->toArray())
                                            ->helperText(__('Select from suggestions or type a new one.'))
                                            ->maxLength(100),

                                        TextInput::make('short_description')
                                            ->label(__('Short Description'))
                                            ->prefixIcon('heroicon-o-document-text')
                                            ->placeholder(__('Summary for website cards and catalog...'))
                                            ->maxLength(255),

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
                                            ->afterStateUpdated(function (?string $state, callable $set, ?Service $record): void {
                                                if (filled($state)) {
                                                    $set('slug', Service::generateUniqueSlug($state, $record?->id));
                                                }
                                            })
                                            ->dehydrateStateUsing(fn (?string $state, ?Service $record) => blank($state) ? null : Service::generateUniqueSlug($state, $record?->id))
                                            ->helperText(fn (callable $get): string => $get('is_slug_editable')
                                                ? __('Edit mode active: customize link to your liking (no accents or spaces; collisions resolve automatically).')
                                                : __('Automatically generated from name. Protected against accidental changes (click pencil to customize).')
                                            ),
                                    ]),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: PROTOCOLO CLÍNICO Y ESPECIALISTAS
                // ==========================================
                Section::make(__('Clinical Protocol & Assigned Specialists'))
                    ->description(__('Detail of clinical procedure and which team doctors are qualified to perform it.'))
                    ->icon('heroicon-o-academic-cap')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('dentists')
                            ->label(__('Authorized Doctors / Specialists'))
                            ->prefixIcon('heroicon-o-user-group')
                            ->multiple()
                            ->relationship('dentists', 'name')
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->helperText(__('Specialists authorized to perform this procedure in medical schedule and online booking.')),

                        Textarea::make('description')
                            ->label(__('Full Clinical Description & Protocol'))
                            ->placeholder(__('Detail treatment stages, benefits, and clinical recommendations...'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                // ==========================================
                // SECCIÓN 3: TARIFAS, DURACIÓN Y AGENDA
                // ==========================================
                Section::make(__('Pricing, Duration & Web Visibility'))
                    ->description(__('Economic pricing, time blocked in medical schedule, and public availability.'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 3])
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('Base Price ($)'))
                                    ->prefixIcon('heroicon-o-currency-dollar')
                                    ->prefix('$')
                                    ->numeric()
                                    ->placeholder('85.00')
                                    ->helperText(__('Standard reference rate.')),

                                TextInput::make('duration_minutes')
                                    ->label(__('Standard Duration'))
                                    ->prefixIcon('heroicon-o-clock')
                                    ->numeric()
                                    ->suffix('min')
                                    ->required()
                                    ->minValue(30)
                                    ->step(15)
                                    ->rule('multiple_of:15')
                                    ->validationMessages([
                                        'required' => __('Treatment duration is required.'),
                                        'min' => __('Minimum duration must be at least 30 minutes.'),
                                        'multiple_of' => __('Duration must be in 15-minute intervals (30, 45, 60, 75, 90, etc.).'),
                                    ])
                                    ->default(45)
                                    ->helperText(__('Required. Minimum 30 min, in 15 min intervals (30, 45, 60, 75, 90...).')),

                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->prefixIcon('heroicon-o-arrows-up-down')
                                    ->numeric()
                                    ->nullable()
                                    ->placeholder('0')
                                    ->default(0)
                                    ->dehydrateStateUsing(fn ($state) => (int) ($state ?? 0))
                                    ->helperText(__('Optional. Lower numbers appear first (e.g. 1 before 2). If left blank, defaults to 0.')),
                            ]),

                        Grid::make(['default' => 1, 'sm' => 3])
                            ->schema([
                                Toggle::make('allows_online_booking')
                                    ->label(__('Allow Online Booking'))
                                    ->helperText(__('Allows patients to book this service online.'))
                                    ->default(true),

                                Toggle::make('is_featured')
                                    ->label(__('Feature on Home'))
                                    ->helperText(__('Shows this treatment on the website homepage.'))
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->label(__('Active Service'))
                                    ->helperText(__('Enables this treatment in catalog and system.'))
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
