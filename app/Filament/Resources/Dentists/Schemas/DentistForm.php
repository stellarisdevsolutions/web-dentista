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

class DentistForm
{
    public static function configure(Schema $schema): Schema
    {
        $is12h = config('app.time_format', '12h') === '12h';

        $days = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
        ];

        $timeRegexRule = fn () => function (string $attribute, $value, \Closure $fail) use ($is12h) {
            if (blank($value)) {
                return;
            }

            $pattern = $is12h
                ? '/^(0[1-9]|1[0-2]):[0-5][0-9]$/'
                : '/^([01][0-9]|2[0-3]):[0-5][0-9]$/';

            if (! preg_match($pattern, (string) $value)) {
                $expected = $is12h ? 'hh:mm (ej. 09:00, 02:00)' : 'HH:mm (ej. 09:00, 14:00)';
                $fail("El formato debe ser {$expected}.");
            }
        };

        $shiftComponents = [];
        foreach ($days as $dayKey => $dayLabel) {
            $shiftComponents[] = Grid::make(['default' => 2, 'sm' => 5])
                ->schema([
                    Toggle::make("weekly_schedule.{$dayKey}.is_working")
                        ->label($dayLabel)
                        ->inline(false)
                        ->default(false)
                        ->columnSpan(['default' => 2, 'sm' => 1])
                        ->rules([
                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get, $dayKey, $dayLabel) {
                                if (! $value) {
                                    return;
                                }

                                $mStart = $get("weekly_schedule.{$dayKey}.morning_start");
                                $mEnd = $get("weekly_schedule.{$dayKey}.morning_end");
                                $aStart = $get("weekly_schedule.{$dayKey}.afternoon_start");
                                $aEnd = $get("weekly_schedule.{$dayKey}.afternoon_end");

                                $hasMorning = filled($mStart) && filled($mEnd);
                                $hasAfternoon = filled($aStart) && filled($aEnd);

                                if (! $hasMorning && ! $hasAfternoon) {
                                    $fail("Debes configurar al menos un turno completo (mañana o tarde) para el día {$dayLabel}.");
                                }
                            },
                        ]),

                    TextInput::make("weekly_schedule.{$dayKey}.morning_start")
                        ->label('Mañana Inicio')
                        ->mask('99:99')
                        ->placeholder('--:--')
                        ->suffix($is12h ? 'AM' : null)
                        ->extraInputAttributes(['inputmode' => 'numeric'])
                        ->nullable()
                        ->required(fn (callable $get) => (bool) $get("weekly_schedule.{$dayKey}.is_working") && filled($get("weekly_schedule.{$dayKey}.morning_end")))
                        ->formatStateUsing(fn ($state) => self::formatTimeForInput($state, 'morning'))
                        ->dehydrateStateUsing(fn ($state, callable $get) => self::formatTimeForDatabase($state, 'morning', (bool) $get("weekly_schedule.{$dayKey}.is_working")))
                        ->rules([
                            $timeRegexRule,
                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get, $dayKey) {
                                $isWorking = (bool) $get("weekly_schedule.{$dayKey}.is_working");
                                $mEnd = $get("weekly_schedule.{$dayKey}.morning_end");

                                if ($isWorking && blank($value) && filled($mEnd)) {
                                    $fail('La hora de inicio de la mañana es obligatoria si se indica la hora de fin.');
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),

                    TextInput::make("weekly_schedule.{$dayKey}.morning_end")
                        ->label('Mañana Fin')
                        ->mask('99:99')
                        ->placeholder('--:--')
                        ->suffix($is12h ? 'AM' : null)
                        ->extraInputAttributes(['inputmode' => 'numeric'])
                        ->nullable()
                        ->required(fn (callable $get) => (bool) $get("weekly_schedule.{$dayKey}.is_working") && filled($get("weekly_schedule.{$dayKey}.morning_start")))
                        ->formatStateUsing(fn ($state) => self::formatTimeForInput($state, 'morning'))
                        ->dehydrateStateUsing(fn ($state, callable $get) => self::formatTimeForDatabase($state, 'morning', (bool) $get("weekly_schedule.{$dayKey}.is_working")))
                        ->rules([
                            $timeRegexRule,
                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get, $dayKey) {
                                $isWorking = (bool) $get("weekly_schedule.{$dayKey}.is_working");
                                $mStart = $get("weekly_schedule.{$dayKey}.morning_start");

                                if ($isWorking && blank($value) && filled($mStart)) {
                                    $fail('La hora de fin de la mañana es obligatoria si se indica la hora de inicio.');

                                    return;
                                }

                                if ($isWorking && filled($value) && filled($mStart)) {
                                    $sMins = self::parseTimeToMinutes($mStart, 'morning');
                                    $eMins = self::parseTimeToMinutes($value, 'morning');

                                    if ($sMins !== null && $eMins !== null && $eMins <= $sMins) {
                                        $fail('La hora de fin de la mañana debe ser posterior a la hora de inicio.');
                                    }
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),

                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_start")
                        ->label('Tarde Inicio')
                        ->mask('99:99')
                        ->placeholder('--:--')
                        ->suffix($is12h ? 'PM' : null)
                        ->extraInputAttributes(['inputmode' => 'numeric'])
                        ->nullable()
                        ->required(fn (callable $get) => (bool) $get("weekly_schedule.{$dayKey}.is_working") && filled($get("weekly_schedule.{$dayKey}.afternoon_end")))
                        ->formatStateUsing(fn ($state) => self::formatTimeForInput($state, 'afternoon'))
                        ->dehydrateStateUsing(fn ($state, callable $get) => self::formatTimeForDatabase($state, 'afternoon', (bool) $get("weekly_schedule.{$dayKey}.is_working")))
                        ->rules([
                            $timeRegexRule,
                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get, $dayKey) {
                                $isWorking = (bool) $get("weekly_schedule.{$dayKey}.is_working");
                                $aEnd = $get("weekly_schedule.{$dayKey}.afternoon_end");

                                if ($isWorking && blank($value) && filled($aEnd)) {
                                    $fail('La hora de inicio de la tarde es obligatoria si se indica la hora de fin.');
                                }

                                $mEnd = $get("weekly_schedule.{$dayKey}.morning_end");
                                if ($isWorking && filled($value) && filled($mEnd)) {
                                    $mEndMins = self::parseTimeToMinutes($mEnd, 'morning');
                                    $aStartMins = self::parseTimeToMinutes($value, 'afternoon');

                                    if ($mEndMins !== null && $aStartMins !== null && $aStartMins < $mEndMins) {
                                        $fail('El turno de la tarde no puede iniciar antes de finalizar el turno de la mañana.');
                                    }
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),

                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_end")
                        ->label('Tarde Fin')
                        ->mask('99:99')
                        ->placeholder('--:--')
                        ->suffix($is12h ? 'PM' : null)
                        ->extraInputAttributes(['inputmode' => 'numeric'])
                        ->nullable()
                        ->required(fn (callable $get) => (bool) $get("weekly_schedule.{$dayKey}.is_working") && filled($get("weekly_schedule.{$dayKey}.afternoon_start")))
                        ->formatStateUsing(fn ($state) => self::formatTimeForInput($state, 'afternoon'))
                        ->dehydrateStateUsing(fn ($state, callable $get) => self::formatTimeForDatabase($state, 'afternoon', (bool) $get("weekly_schedule.{$dayKey}.is_working")))
                        ->rules([
                            $timeRegexRule,
                            fn (callable $get) => function (string $attribute, $value, \Closure $fail) use ($get, $dayKey) {
                                $isWorking = (bool) $get("weekly_schedule.{$dayKey}.is_working");
                                $aStart = $get("weekly_schedule.{$dayKey}.afternoon_start");

                                if ($isWorking && blank($value) && filled($aStart)) {
                                    $fail('La hora de fin de la tarde es obligatoria si se indica la hora de inicio.');

                                    return;
                                }

                                if ($isWorking && filled($value) && filled($aStart)) {
                                    $sMins = self::parseTimeToMinutes($aStart, 'afternoon');
                                    $eMins = self::parseTimeToMinutes($value, 'afternoon');

                                    if ($sMins !== null && $eMins !== null && $eMins <= $sMins) {
                                        $fail('La hora de fin de la tarde debe ser posterior a la hora de inicio.');
                                    }
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),
                ]);
        }

        return $schema
            ->columns(1)
            ->components([
                // ==========================================
                // SECCIÓN 1: PERFIL PROFESIONAL E IDENTIDAD
                // ==========================================
                Section::make('Perfil Profesional e Identidad')
                    ->description('Fotografía oficial, credenciales médicas y datos de contacto directo del especialista.')
                    ->icon('heroicon-o-user')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'lg' => 12])
                            ->schema([
                                // Columna Izquierda: Fotografía Oficial (Avatar Card SaaS)
                                Grid::make(1)
                                    ->columnSpan(['default' => 12, 'lg' => 4, 'xl' => 3])
                                    ->schema([
                                        FileUpload::make('photo')
                                            ->label('Fotografía Oficial (1:1)')
                                            ->image()
                                            ->imageCropAspectRatio('1:1')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '1:1',
                                            ])
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('800')
                                            ->imageResizeTargetHeight('800')
                                            ->imagePreviewHeight('220')
                                            ->directory('dentists')
                                            ->disk('public')
                                            ->helperText('Formato 1:1 cuadrado (800×800 px). Si se deja vacío, se mostrarán las iniciales con fondo celeste como en Agenda. Se optimiza automáticamente a WebP.'),
                                    ]),

                                // Columna Derecha: Campos de Identidad, Contacto y Disponibilidad
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 9])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre Completo Profesional')
                                            ->prefixIcon('heroicon-o-user')
                                            ->placeholder('ej. Dr. Marcus Sterling')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('title')
                                            ->label('Cargo / Título Profesional')
                                            ->prefixIcon('heroicon-o-briefcase')
                                            ->placeholder('ej. Endodoncista y Odontología Restauradora')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('specialty')
                                            ->label('Especialidad Médica Principal')
                                            ->prefixIcon('heroicon-o-sparkles')
                                            ->placeholder('ej. Tratamientos de Conducto y Microcirugía')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('license_number')
                                            ->label('Nº Colegiatura / Registro Médico')
                                            ->prefixIcon('heroicon-o-identification')
                                            ->placeholder('ej. DDS-73419')
                                            ->maxLength(100),

                                        TextInput::make('email')
                                            ->label('Correo Electrónico Directo')
                                            ->prefixIcon('heroicon-o-envelope')
                                            ->email()
                                            ->placeholder('dr.sterling@auradental.com')
                                            ->maxLength(255),

                                        TextInput::make('phone')
                                            ->label('Teléfono Directo / WhatsApp')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->tel()
                                            ->telRegex('/^[+0-9\s()\-.]+$/')
                                            ->validationMessages([
                                                'regex' => 'El formato del teléfono no es válido (solo números, espacios, +, -, ()).',
                                            ])
                                            ->placeholder('+1 (555) 234-5678')
                                            ->nullable()
                                            ->maxLength(50),

                                        TextInput::make('order')
                                            ->label('Orden de Visualización')
                                            ->prefixIcon('heroicon-o-arrows-up-down')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Prioridad en el listado público del equipo médico.'),

                                        Toggle::make('is_active')
                                            ->label('Disponible para Citas en la Web')
                                            ->helperText('Habilita este doctor en el selector de reservas públicas de la clínica.')
                                            ->default(true),
                                    ]),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: ÁREA CLÍNICA Y TRATAMIENTOS ASIGNADOS
                // ==========================================
                Section::make('Área Clínica y Tratamientos Asignados')
                    ->description('Procedimientos odontológicos que atiende y reseña curricular para los pacientes.')
                    ->icon('heroicon-o-academic-cap')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('services')
                            ->label('Tratamientos Habilitados para este Especialista')
                            ->multiple()
                            ->relationship('services', 'name')
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->helperText('Selecciona los tratamientos que este profesional atiende. La agenda y el wizard público solo ofrecerán este especialista para estos procedimientos.'),

                        Textarea::make('bio')
                            ->label('Biografía y Formación Académica')
                            ->placeholder('Resumen curricular, estudios de especialización, universidad de egreso y experiencia para la ficha pública de la clínica...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Esta información se mostrará a los pacientes cuando elijan especialista al agendar su cita.'),
                    ]),

                // ==========================================
                // SECCIÓN 3: HORARIOS Y TURNOS DE ATENCIÓN SEMANAL (ANCHO COMPLETO)
                // ==========================================
                Section::make('Horarios y Turnos de Atención Semanal (Mañana y Tarde)')
                    ->description($is12h
                        ? 'Configura los turnos por día en formato 12 horas (AM/PM). Los turnos vacíos se consideran no laborables.'
                        : 'Configura los turnos por día en formato 24 horas. Los turnos vacíos se consideran no laborables.')
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull()
                    ->schema($shiftComponents),
            ]);
    }

    public static function formatTimeForInput(?string $time, string $shift): ?string
    {
        if (blank($time)) {
            return null;
        }

        $time = trim(substr((string) $time, 0, 5));
        $parts = explode(':', $time);
        if (count($parts) < 2) {
            return null;
        }

        $hour = (int) $parts[0];
        $minute = (int) $parts[1];

        if (config('app.time_format', '12h') === '12h') {
            if ($shift === 'afternoon' && $hour > 12) {
                $hour -= 12;
            } elseif ($shift === 'morning' && $hour > 12) {
                $hour -= 12;
            }
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    public static function formatTimeForDatabase(?string $time, string $shift, bool $isWorking = true): ?string
    {
        if (! $isWorking || blank($time)) {
            return null;
        }

        $time = trim((string) $time);
        $parts = explode(':', $time);
        if (count($parts) < 2) {
            return null;
        }

        $hour = (int) $parts[0];
        $minute = (int) $parts[1];

        if (config('app.time_format', '12h') === '12h') {
            if ($shift === 'afternoon') {
                if ($hour < 12) {
                    $hour += 12;
                }
            } elseif ($shift === 'morning') {
                if ($hour >= 1 && $hour <= 3) {
                    $hour += 12;
                }
            }
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    public static function parseTimeToMinutes(?string $time, string $shift): ?int
    {
        if (blank($time)) {
            return null;
        }

        $time = trim((string) $time);
        $parts = explode(':', $time);
        if (count($parts) < 2) {
            return null;
        }

        $hour = (int) $parts[0];
        $minute = (int) $parts[1];

        if (config('app.time_format', '12h') === '12h') {
            if ($shift === 'afternoon') {
                if ($hour < 12) {
                    $hour += 12;
                }
            } elseif ($shift === 'morning') {
                if ($hour >= 1 && $hour <= 3) {
                    $hour += 12;
                }
            }
        }

        return ($hour * 60) + $minute;
    }
}
