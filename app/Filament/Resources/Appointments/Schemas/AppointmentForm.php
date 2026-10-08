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
        $isSpanish = app()->getLocale() === 'es';

        return $schema
            ->components([
                Section::make($isSpanish ? 'Información del Paciente' : 'Patient Information')
                    ->description($isSpanish ? 'Datos de contacto y personales para la reserva.' : 'Contact and personal data for the booking.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('patient_name')
                                    ->label($isSpanish ? 'Nombre Completo del Paciente' : 'Patient Full Name')
                                    ->placeholder($isSpanish ? 'ej. María Rodríguez' : 'e.g. John Doe')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('patient_phone')
                                    ->label($isSpanish ? 'Teléfono / WhatsApp' : 'Phone / WhatsApp')
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                    ])
                                    ->required()
                                    ->helperText($isSpanish ? 'Incluir código de país, ej. +15552345678' : 'Include country code, e.g. +15552345678')
                                    ->maxLength(50),
                                TextInput::make('patient_email')
                                    ->label($isSpanish ? 'Correo Electrónico' : 'Email Address')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        Toggle::make('is_first_visit')
                            ->label($isSpanish ? '¿Es su primera visita a nuestra clínica?' : 'First time visiting our clinic?')
                            ->default(true),
                    ]),

                Section::make($isSpanish ? 'Detalles de la Cita' : 'Appointment Details')
                    ->description($isSpanish ? 'Selección de tratamiento, especialista asignado, fecha y notas.' : 'Service selection, assigned specialist, schedule, and notes.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('service_id')
                                    ->label($isSpanish ? 'Tratamiento / Servicio' : 'Treatment / Service')
                                    ->relationship('service', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('dentist_id')
                                    ->label($isSpanish ? 'Especialista Asignado' : 'Assigned Specialist')
                                    ->relationship('dentist', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('status')
                                    ->label($isSpanish ? 'Estado de la Cita' : 'Status')
                                    ->options($isSpanish ? [
                                        'pending' => 'Pendiente de Confirmación',
                                        'confirmed' => 'Confirmada',
                                        'in_progress' => 'En Consulta',
                                        'completed' => 'Completada',
                                        'cancelled' => 'Cancelada',
                                        'no_show' => 'No Asistió',
                                    ] : [
                                        'pending' => 'Pending Confirmation',
                                        'confirmed' => 'Confirmed',
                                        'in_progress' => 'In Consultation',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                        'no_show' => 'No Show',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('appointment_date')
                                    ->label($isSpanish ? 'Fecha' : 'Date')
                                    ->required(),
                                TextInput::make('appointment_time')
                                    ->label($isSpanish ? 'Horario (Hora)' : 'Time Slot')
                                    ->placeholder('10:00')
                                    ->required(),
                                TextInput::make('duration_minutes')
                                    ->label($isSpanish ? 'Duración (Minutos)' : 'Duration (Min)')
                                    ->numeric()
                                    ->default(45),
                            ]),

                        Textarea::make('symptoms')
                            ->label($isSpanish ? 'Notas o Motivo de la Cita (Opcional)' : 'Notes or Reason for Visit (Optional)')
                            ->placeholder($isSpanish ? 'Motivo de la consulta, molestias, indicaciones previas u observaciones...' : 'Reason for visit, discomfort, or special observations...')
                            ->rows(3)
                            ->nullable(),

                        Hidden::make('preferred_locale')
                            ->default(fn () => app()->getLocale()),
                    ]),
            ]);
    }
}
