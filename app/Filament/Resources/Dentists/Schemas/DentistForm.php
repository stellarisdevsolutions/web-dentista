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
            'monday' => __('Monday'),
            'tuesday' => __('Tuesday'),
            'wednesday' => __('Wednesday'),
            'thursday' => __('Thursday'),
            'friday' => __('Friday'),
            'saturday' => __('Saturday'),
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
                        ->label(__('Morning Start'))
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
                                    $fail(__('Morning start time is required if end time is specified.'));
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),

                    TextInput::make("weekly_schedule.{$dayKey}.morning_end")
                        ->label(__('Morning End'))
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
                                    $fail(__('Morning end time is required if start time is specified.'));

                                    return;
                                }

                                if ($isWorking && filled($value) && filled($mStart)) {
                                    $sMins = self::parseTimeToMinutes($mStart, 'morning');
                                    $eMins = self::parseTimeToMinutes($value, 'morning');

                                    if ($sMins !== null && $eMins !== null && $eMins <= $sMins) {
                                        $fail(__('Morning end time must be after start time.'));
                                    }
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),

                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_start")
                        ->label(__('Afternoon Start'))
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
                                    $fail(__('Afternoon start time is required if end time is specified.'));
                                }

                                $mEnd = $get("weekly_schedule.{$dayKey}.morning_end");
                                if ($isWorking && filled($value) && filled($mEnd)) {
                                    $mEndMins = self::parseTimeToMinutes($mEnd, 'morning');
                                    $aStartMins = self::parseTimeToMinutes($value, 'afternoon');

                                    if ($mEndMins !== null && $aStartMins !== null && $aStartMins < $mEndMins) {
                                        $fail(__('Afternoon shift cannot start before morning shift ends.'));
                                    }
                                }
                            },
                        ])
                        ->columnSpan(['default' => 1, 'sm' => 1]),

                    TextInput::make("weekly_schedule.{$dayKey}.afternoon_end")
                        ->label(__('Afternoon End'))
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
                                    $fail(__('Afternoon end time is required if start time is specified.'));

                                    return;
                                }

                                if ($isWorking && filled($value) && filled($aStart)) {
                                    $sMins = self::parseTimeToMinutes($aStart, 'afternoon');
                                    $eMins = self::parseTimeToMinutes($value, 'afternoon');

                                    if ($sMins !== null && $eMins !== null && $eMins <= $sMins) {
                                        $fail(__('Afternoon end time must be after start time.'));
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
                Section::make(__('Professional Profile & Identity'))
                    ->description(__('Official photography, medical credentials, and direct contact details for the specialist.'))
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
                                            ->label(__('Official Photo (1:1)'))
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
                                            ->helperText(__('Square 1:1 format (800×800 px). If left empty, initials will be shown with a light blue background as in Agenda. Automatically optimized to WebP.')),
                                    ]),

                                // Columna Derecha: Campos de Identidad, Contacto y Disponibilidad
                                Grid::make(['default' => 1, 'sm' => 2])
                                    ->columnSpan(['default' => 12, 'lg' => 8, 'xl' => 9])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Full Professional Name'))
                                            ->prefixIcon('heroicon-o-user')
                                            ->placeholder('Dr. Marcus Sterling')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('title')
                                            ->label(__('Job Title / Professional Degree'))
                                            ->prefixIcon('heroicon-o-briefcase')
                                            ->placeholder(__('e.g. Endodontist & Restorative Dentistry'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('specialty')
                                            ->label(__('Main Medical Specialty'))
                                            ->prefixIcon('heroicon-o-sparkles')
                                            ->placeholder(__('e.g. Root Canal & Microsurgery'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('license_number')
                                            ->label(__('Medical License / Registration No.'))
                                            ->prefixIcon('heroicon-o-identification')
                                            ->placeholder('DDS-73419')
                                            ->maxLength(100),

                                        TextInput::make('email')
                                            ->label(__('Direct Email'))
                                            ->prefixIcon('heroicon-o-envelope')
                                            ->email()
                                            ->placeholder('dr.sterling@auradental.com')
                                            ->maxLength(255),

                                        TextInput::make('phone')
                                            ->label(__('Direct Phone / WhatsApp'))
                                            ->prefixIcon('heroicon-o-phone')
                                            ->tel()
                                            ->telRegex('/^[+0-9\s()\-.]+$/')
                                            ->validationMessages([
                                                'regex' => __('Invalid phone format (numbers, spaces, +, -, () only).'),
                                            ])
                                            ->placeholder('+1 (555) 234-5678')
                                            ->nullable()
                                            ->maxLength(50),

                                        TextInput::make('order')
                                            ->label(__('Display Order'))
                                            ->prefixIcon('heroicon-o-arrows-up-down')
                                            ->numeric()
                                            ->nullable()
                                            ->placeholder('0')
                                            ->default(0)
                                            ->dehydrateStateUsing(fn ($state) => (int) ($state ?? 0))
                                            ->helperText(__('Optional. Lower numbers appear first (e.g. 1 goes before 2). Defaults to 0.')),

                                        Toggle::make('is_active')
                                            ->label(__('Available for Appointments Online'))
                                            ->helperText(__('Enables this doctor in the clinic\'s public booking selector.'))
                                            ->default(true),
                                    ]),
                            ]),
                    ]),

                // ==========================================
                // SECCIÓN 2: ÁREA CLÍNICA Y TRATAMIENTOS ASIGNADOS
                // ==========================================
                Section::make(__('Clinical Area & Assigned Treatments'))
                    ->description(__('Dental procedures handled and curricular overview for patients.'))
                    ->icon('heroicon-o-academic-cap')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('services')
                            ->label(__('Treatments Enabled for this Specialist'))
                            ->multiple()
                            ->relationship('services', 'name')
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->helperText(__('Select the treatments handled by this professional. The agenda and public booking wizard will only offer this specialist for these procedures.')),

                        Textarea::make('bio')
                            ->label(__('Biography & Academic Background'))
                            ->placeholder(__('Curricular overview, specialized studies, university and clinical experience for the clinic profile...'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText(__('This information will be displayed to patients when selecting a specialist.')),
                    ]),

                // ==========================================
                // SECCIÓN 3: HORARIOS Y TURNOS DE ATENCIÓN SEMANAL (ANCHO COMPLETO)
                // ==========================================
                Section::make(__('Weekly Schedule & Working Shifts (Morning & Afternoon)'))
                    ->description($is12h
                        ? __('Configure daily shifts in 12-hour format (AM/PM). Empty shifts are considered non-working.')
                        : __('Configure daily shifts in 24-hour format. Empty shifts are considered non-working.'))
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
