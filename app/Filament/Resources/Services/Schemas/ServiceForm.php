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
                Section::make('Información Principal y Multimedia')
                    ->description('Define el título, enlace permanente (slug), categoría y fotografía oficial del tratamiento.')
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
                                            ->label('Fotografía del Tratamiento (4:3)')
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
                                            ->helperText('Obligatorio. Formato recomendado 4:3 (1200×900 px). Se optimiza automáticamente a WebP para máxima velocidad en la web.'),
                                    ]),

                                // Columna Derecha: Campos de Identidad, Categoría y Slug Híbrido
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 8])
                                    ->schema([
                                        Hidden::make('is_slug_editable')
                                            ->default(false)
                                            ->dehydrated(false),

                                        TextInput::make('name')
                                            ->label('Nombre del Tratamiento')
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
                                            ->afterStateUpdated(function (?string $state, callable $set, ?Service $record): void {
                                                if (filled($state)) {
                                                    $set('slug', Service::generateUniqueSlug($state, $record?->id));
                                                }
                                            })
                                            ->dehydrateStateUsing(fn (?string $state, ?Service $record) => blank($state) ? null : Service::generateUniqueSlug($state, $record?->id))
                                            ->helperText(fn (callable $get): string => $get('is_slug_editable')
                                                ? 'Modo edición activado: personaliza el enlace a tu gusto (sin acentos ni espacios; las colisiones se resuelven solas).'
                                                : 'Generado automáticamente según el nombre. Protegido contra cambios accidentales (pulsa el lápiz para personalizar).'
                                            ),

                                        TextInput::make('category')
                                            ->label('Categoría')
                                            ->prefixIcon('heroicon-o-tag')
                                            ->placeholder('ej. Odontología Estética, Ortodoncia')
                                            ->maxLength(100),

                                        TextInput::make('short_description')
                                            ->label('Descripción Breve')
                                            ->prefixIcon('heroicon-o-document-text')
                                            ->placeholder('Resumen para las tarjetas del sitio web y catálogo...')
                                            ->maxLength(255),
                                    ]),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: PROTOCOLO CLÍNICO Y ESPECIALISTAS
                // ==========================================
                Section::make('Protocolo Clínico y Especialistas Asignados')
                    ->description('Detalle del procedimiento clínico y qué doctores del equipo están habilitados para atenderlo.')
                    ->icon('heroicon-o-academic-cap')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('dentists')
                            ->label('Doctores / Especialistas Habilitados')
                            ->prefixIcon('heroicon-o-user-group')
                            ->multiple()
                            ->relationship('dentists', 'name')
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->helperText('Especialistas autorizados para atender este procedimiento en la agenda médica y reservas online.'),

                        Textarea::make('description')
                            ->label('Descripción Completa y Protocolo Clínico')
                            ->placeholder('Detalla las etapas del tratamiento, beneficios y recomendaciones clínicas...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                // ==========================================
                // SECCIÓN 3: TARIFAS, DURACIÓN Y AGENDA
                // ==========================================
                Section::make('Tarifas, Tiempos y Visibilidad Web')
                    ->description('Valores económicos, tiempo de bloqueo en agenda médica y disponibilidad pública.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 3])
                            ->schema([
                                TextInput::make('price')
                                    ->label('Precio Base ($)')
                                    ->prefixIcon('heroicon-o-currency-dollar')
                                    ->prefix('$')
                                    ->numeric()
                                    ->placeholder('85.00')
                                    ->helperText('Tarifa estándar de referencia.'),

                                TextInput::make('duration_minutes')
                                    ->label('Duración Estándar (Minutos)')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->numeric()
                                    ->default(45)
                                    ->helperText('Tiempo bloqueado en la agenda.'),

                                TextInput::make('order')
                                    ->label('Orden de Visualización')
                                    ->prefixIcon('heroicon-o-arrows-up-down')
                                    ->numeric()
                                    ->nullable()
                                    ->placeholder('0')
                                    ->default(0)
                                    ->dehydrateStateUsing(fn ($state) => (int) ($state ?? 0))
                                    ->helperText('Opcional. Entre más bajo el número, primero aparecerá (ej. 1 va antes que 2). Si se deja vacío, será 0.'),
                            ]),

                        Grid::make(['default' => 1, 'sm' => 3])
                            ->schema([
                                Toggle::make('allows_online_booking')
                                    ->label('Permitir Reserva Online')
                                    ->helperText('Habilita que los pacientes reserven este servicio por la web.')
                                    ->default(true),

                                Toggle::make('is_featured')
                                    ->label('Destacar en Inicio')
                                    ->helperText('Muestra este tratamiento en la portada de la web.')
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->label('Servicio Activo')
                                    ->helperText('Habilita este tratamiento en el catálogo y sistema.')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
