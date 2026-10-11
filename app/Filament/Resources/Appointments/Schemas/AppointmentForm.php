<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Paciente')
                    ->description('Datos de contacto y personales para la gestión de la cita.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('patient_name')
                                    ->label('Nombre Completo del Paciente')
                                    ->placeholder('ej. María Rodríguez')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('patient_phone')
                                    ->label('Teléfono / WhatsApp')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                    ])
                                    ->required()
                                    ->helperText('Incluir código de país, ej. +15552345678')
                                    ->maxLength(50),
                                TextInput::make('patient_email')
                                    ->label('Correo Electrónico (Opcional)')
                                    ->email()
                                    ->placeholder('paciente@ejemplo.com')
                                    ->maxLength(255),
                            ]),
                        Toggle::make('is_first_visit')
                            ->label('¿Es su primera visita a nuestra clínica?')
                            ->helperText('Indica si requiere apertura de ficha clínica dental.')
                            ->default(true),
                    ]),

                Section::make('Detalles de la Cita')
                    ->description('Selección del servicio odontológico, especialista, fecha y hora.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('service_id')
                                    ->label('Tratamiento / Servicio')
                                    ->relationship('service', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('dentist_id')
                                    ->label('Especialista Asignado')
                                    ->relationship('dentist', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('status')
                                    ->label('Estado de la Cita')
                                    ->options([
                                        'pending' => 'Por Confirmar',
                                        'confirmed' => 'Confirmada',
                                        'in_progress' => 'En Consulta',
                                        'completed' => 'Completada',
                                        'cancelled' => 'Cancelada',
                                        'no_show' => 'No Asistió',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('appointment_date')
                                    ->label('Fecha de la Cita')
                                    ->native(false)
                                    ->required(),
                                TextInput::make('appointment_time')
                                    ->label('Horario')
                                    ->placeholder('09:30')
                                    ->helperText('Formato HH:MM (ej. 10:00, 15:30)')
                                    ->required(),
                                TextInput::make('duration_minutes')
                                    ->label('Duración (Minutos)')
                                    ->numeric()
                                    ->default(45)
                                    ->minValue(15)
                                    ->maxValue(240),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Textarea::make('symptoms')
                                    ->label('Notas o Motivo de la Cita (Opcional)')
                                    ->placeholder('Molestias dentales, limpieza programada, revisión...')
                                    ->rows(3)
                                    ->nullable(),
                                Textarea::make('internal_notes')
                                    ->label('Notas Internas del Equipo Clínico')
                                    ->placeholder('Observaciones privadas para recepción o doctores...')
                                    ->rows(3)
                                    ->nullable(),
                            ]),

                        Hidden::make('preferred_locale')
                            ->default(fn () => app()->getLocale()),
                    ]),
            ]);
    }
}
