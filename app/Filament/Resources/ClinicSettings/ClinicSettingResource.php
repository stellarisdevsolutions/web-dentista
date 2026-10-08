<?php

namespace App\Filament\Resources\ClinicSettings;

use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use App\Models\ClinicSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ClinicSettingResource extends Resource
{
    protected static ?string $model = ClinicSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'clinic_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['clinic_name', 'phone', 'whatsapp', 'email', 'address'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var ClinicSetting $record */
        return $record->clinic_name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var ClinicSetting $record */
        $isSpanish = app()->getLocale() === 'es';

        return [
            $isSpanish ? 'Teléfono' : 'Phone' => $record->phone,
            $isSpanish ? 'WhatsApp' : 'WhatsApp' => $record->whatsapp,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Clinic Profile & Settings');
    }

    public static function getModelLabel(): string
    {
        return __('Clinic Setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Clinic Settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Perfil General de la Clínica')
                    ->description('Información principal de contacto y datos de la clínica.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('clinic_name')
                                    ->label('Nombre de la Clínica')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Teléfono Principal')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                    ])
                                    ->required(),
                                TextInput::make('whatsapp')
                                    ->label('Número de WhatsApp')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del WhatsApp no es válido (ej. +15552345678).',
                                    ])
                                    ->required()
                                    ->helperText('Usado para el botón flotante y confirmaciones de citas (ej. +15552345678)'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo Electrónico de Contacto')
                                    ->email()
                                    ->required(),
                                TextInput::make('emergency_phone')
                                    ->label('Teléfono de Urgencias (Opcional)')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                    ])
                                    ->nullable(),
                            ]),
                    ]),

                Section::make('Lema y Horarios de Atención')
                    ->schema([
                        TextInput::make('tagline')
                            ->label('Lema / Eslogan')
                            ->placeholder('Odontología moderna, integral y sin dolor...'),
                        Textarea::make('opening_hours')
                            ->label('Horario de Atención')
                            ->rows(3)
                            ->placeholder("Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM"),
                    ]),

                Section::make('Ubicación y Controles')
                    ->schema([
                        TextInput::make('address')
                            ->label('Dirección Física')
                            ->required(),
                        Textarea::make('google_maps_url')
                            ->label('Enlace de Google Maps (URL)')
                            ->rows(2),
                        Toggle::make('show_language_switcher')
                            ->label('Mostrar Selector de Idioma [EN | ES] en la Web')
                            ->helperText('Activar o desactivar el botón selector de idioma en la cabecera de la web. Por defecto desactivado (solo español).')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic_name')
                    ->label('Clínica')
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label('Teléfono'),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->badge()
                    ->color(Color::Emerald),

                TextColumn::make('email')
                    ->label('Email'),

                ToggleColumn::make('show_language_switcher')
                    ->label('Selector Activo'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClinicSettings::route('/'),
        ];
    }
}
