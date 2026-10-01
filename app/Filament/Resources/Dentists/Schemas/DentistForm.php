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
        $isSpanish = app()->getLocale() === 'es';

        $days = [
            'monday' => $isSpanish ? 'Lunes' : 'Monday',
            'tuesday' => $isSpanish ? 'Martes' : 'Tuesday',
            'wednesday' => $isSpanish ? 'Miércoles' : 'Wednesday',
            'thursday' => $isSpanish ? 'Jueves' : 'Thursday',
            'friday' => $isSpanish ? 'Viernes' : 'Friday',
            'saturday' => $isSpanish ? 'Sábado' : 'Saturday',
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
                        ->label($isSpanish ? 'Mañana Inicio' : 'Morning Start')
                        ->placeholder('09:00'),
                    TextInput::make("weekly_schedule.{$dayKey}.morning_end")
                        ->label($isSpanish ? 'Mañana Fin' : 'Morning End')
                        ->placeholder('13:00'),
                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_start")
                        ->label($isSpanish ? 'Tarde Inicio' : 'Afternoon Start')
                        ->placeholder('14:00'),
                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_end")
                        ->label($isSpanish ? 'Tarde Fin' : 'Afternoon End')
                        ->placeholder('18:00'),
                ]);
        }

        return $schema
            ->components([
                Section::make($isSpanish ? 'Perfil General del Especialista' : 'General Profile')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label($isSpanish ? 'Nombre Completo Profesional' : 'Full Professional Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('license_number')
                                    ->label($isSpanish ? 'Nº Colegiatura / Registro Médico' : 'Medical License')
                                    ->placeholder('ej. DDS-94821')
                                    ->maxLength(100),
                            ]),

                        Grid::make(3)
                            ->schema([
                                FileUpload::make('photo')
                                    ->label($isSpanish ? 'Fotografía del Doctor/a' : 'Doctor Photo')
                                    ->image()
                                    ->directory('dentists')
                                    ->disk('public'),
                                TextInput::make('email')
                                    ->label($isSpanish ? 'Correo Electrónico' : 'Email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label($isSpanish ? 'Teléfono Directo' : 'Phone')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                    ])
                                    ->nullable()
                                    ->maxLength(50),
                            ]),
                    ]),

                Section::make($isSpanish ? 'Especialidad y Tratamientos Asignados' : 'Specialty & Assigned Treatments')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make($isSpanish ? 'title_es' : 'title_en')
                                    ->label($isSpanish ? 'Título Profesional' : 'Professional Title')
                                    ->placeholder($isSpanish ? 'ej. Director de Implantología y Cirugía Oral' : 'e.g. Chief Implantologist & Oral Surgeon')
                                    ->required(),
                                TextInput::make($isSpanish ? 'specialty_es' : 'specialty_en')
                                    ->label($isSpanish ? 'Especialidad' : 'Specialty')
                                    ->placeholder($isSpanish ? 'ej. Implantología y Cirugía Reconstructiva' : 'e.g. Implantology & Reconstructive Surgery')
                                    ->required(),
                            ]),

                        Textarea::make($isSpanish ? 'bio_es' : 'bio_en')
                            ->label($isSpanish ? 'Biografía y Formación Académica' : 'Biography & Education')
                            ->rows(3),

                        Select::make('services')
                            ->label($isSpanish ? 'Tratamientos que Realiza este Especialista' : 'Assigned Treatments')
                            ->multiple()
                            ->relationship('services', $isSpanish ? 'name_es' : 'name_en')
                            ->preload()
                            ->searchable()
                            ->helperText($isSpanish ? 'Selecciona los tratamientos clínicos que este profesional atiende.' : 'Select the treatments this dentist performs.'),
                    ]),

                Section::make($isSpanish ? 'Horarios y Turnos de Atención (Mañana y Tarde)' : 'Schedule & Shifts (Morning & Afternoon)')
                    ->description($isSpanish ? 'Configura los turnos de atención por día. El espacio entre el fin de la mañana y el inicio de la tarde actúa automáticamente como receso de almuerzo.' : 'Configure daily morning and afternoon shifts. The gap automatically serves as lunch break.')
                    ->schema([
                        ...$shiftComponents,
                        Grid::make(2)
                            ->schema([
                                TextInput::make('order')
                                    ->label($isSpanish ? 'Orden' : 'Sort Order')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label($isSpanish ? 'Disponible para Citas' : 'Available for Booking')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
