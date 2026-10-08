<?php

namespace App\Filament\Resources\Dentists\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DentistForm
{
    public static function configure(Schema $schema): Schema
    {
        $days = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
        ];

        $shiftComponents = [];
        foreach ($days as $dayKey => $dayLabel) {
            $shiftComponents[] = Grid::make(5)
                ->schema([
                    Toggle::make("weekly_schedule.{$dayKey}.is_working")
                        ->label($dayLabel)
                        ->inline(false)
                        ->default(false),
                    TextInput::make("weekly_schedule.{$dayKey}.morning_start")
                        ->label('Mañana Inicio')
                        ->placeholder('09:00'),
                    TextInput::make("weekly_schedule.{$dayKey}.morning_end")
                        ->label('Mañana Fin')
                        ->placeholder('13:00'),
                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_start")
                        ->label('Tarde Inicio')
                        ->placeholder('14:00'),
                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_end")
                        ->label('Tarde Fin')
                        ->placeholder('18:00'),
                ]);
        }

        return $schema
            ->components([
                Section::make('Perfil General del Especialista')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre Completo Profesional')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('license_number')
                                    ->label('Nº Colegiatura / Registro Médico')
                                    ->placeholder('ej. DDS-94821')
                                    ->maxLength(100),
                            ]),

                        Grid::make(3)
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Fotografía del Doctor/a')
                                    ->image()
                                    ->directory('dentists')
                                    ->disk('public'),
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Teléfono Directo')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                    ])
                                    ->nullable()
                                    ->maxLength(50),
                            ]),
                    ]),

                Section::make('Especialidad y Tratamientos Asignados')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Título Profesional')
                                    ->placeholder('ej. Director de Implantología y Cirugía Oral')
                                    ->required(),
                                TextInput::make('specialty')
                                    ->label('Especialidad')
                                    ->placeholder('ej. Implantología y Cirugía Reconstructiva')
                                    ->required(),
                            ]),

                        Textarea::make('bio')
                            ->label('Biografía y Formación Académica')
                            ->rows(3),

                        Select::make('services')
                            ->label('Tratamientos que Realiza este Especialista')
                            ->multiple()
                            ->relationship('services', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Selecciona los tratamientos clínicos que este profesional atiende.'),
                    ]),

                Section::make('Horarios y Turnos de Atención (Mañana y Tarde)')
                    ->description('Configura los turnos de atención por día. El espacio entre el fin de la mañana y el inicio de la tarde actúa automáticamente como receso de almuerzo.')
                    ->schema([
                        ...$shiftComponents,
                        Grid::make(2)
                            ->schema([
                                TextInput::make('order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label('Disponible para Citas')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
