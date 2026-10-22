<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // ==========================================
                // SECCIÓN 1: INFORMACIÓN DEL PACIENTE (100% ANCHO COMPLETO)
                // ==========================================
                Section::make(__('Patient Information'))
                    ->description(__('Contact details for clinical record, reminders, and WhatsApp confirmation.'))
                    ->icon('heroicon-o-user')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 3])
                            ->schema([
                                TextInput::make('patient_name')
                                    ->label(__('Full Patient Name'))
                                    ->placeholder('ej. María Rodríguez')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('patient_phone')
                                    ->label(__('Phone / WhatsApp'))
                                    ->tel()
                                    ->telRegex('/^[+0-9\s()\-.]+$/')
                                    ->validationMessages([
                                        'regex' => __('Invalid format (numbers, spaces, +, - only).'),
                                    ])
                                    ->required()
                                    ->placeholder('+591 70012345')
                                    ->maxLength(50),

                                TextInput::make('patient_email')
                                    ->label(__('Email Address (Optional)'))
                                    ->email()
                                    ->placeholder('paciente@ejemplo.com')
                                    ->maxLength(255),
                            ]),

                        Toggle::make('is_first_visit')
                            ->label(__('Is this the first visit to our clinic?'))
                            ->helperText(__('Indicates whether a new clinical file and initial dental chart are required.'))
                            ->default(false),
                    ]),

                // ==========================================
                // SECCIÓN 2: DETALLES DE LA CITA (100% ANCHO COMPLETO)
                // ==========================================
                Section::make(__('Appointment Details'))
                    ->description(__('Select dental procedure and qualified specialist.'))
                    ->icon('heroicon-o-sparkles')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 12])
                            ->schema([
                                Select::make('service_id')
                                    ->label(__('Treatment / Service'))
                                    ->options(fn () => Service::query()->where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if ($state) {
                                            $service = Service::find($state);
                                            if ($service && $service->duration_minutes) {
                                                $set('duration_minutes', (int) $service->duration_minutes);
                                            }

                                            // If selected dentist doesn't perform this service, clear dentist
                                            $currentDentistId = $get('dentist_id');
                                            if ($currentDentistId && $service && $service->dentists()->exists()) {
                                                if (! $service->dentists()->where('dentists.id', $currentDentistId)->exists()) {
                                                    $set('dentist_id', null);
                                                }
                                            }
                                        }
                                        $set('appointment_time', null);
                                    })
                                    ->columnSpan(['default' => 12, 'md' => 5])
                                    ->helperText(__('Defines duration and filters qualified doctors.')),

                                Select::make('dentist_id')
                                    ->label(__('Assigned Specialist'))
                                    ->placeholder(__('First Available Specialist (Auto balancing)'))
                                    ->options(function (Get $get) {
                                        /** @var User|null $user */
                                        $user = auth()->user();
                                        if ($user && $user->isDentist() && $user->dentist_id) {
                                            return Dentist::query()
                                                ->where('id', $user->dentist_id)
                                                ->pluck('name', 'id')
                                                ->map(fn ($name) => preg_replace('/^(Dr\.|Dra\.|Dr|Dra)\s+/i', '', $name));
                                        }

                                        $serviceId = $get('service_id');
                                        $query = Dentist::query()->where('is_active', true);

                                        if ($serviceId) {
                                            $service = Service::find($serviceId);
                                            if ($service && $service->dentists()->exists()) {
                                                $query->whereIn('id', $service->dentists()->pluck('dentists.id'));
                                            }
                                        }

                                        return $query->orderBy('name')->pluck('name', 'id')->map(function ($name) {
                                            return preg_replace('/^(Dr\.|Dra\.|Dr|Dra)\s+/i', '', $name);
                                        });
                                    })
                                    ->disabled(fn () => (bool) (auth()->user()?->isDentist() && auth()->user()?->dentist_id))
                                    ->default(fn () => auth()->user()?->isDentist() ? auth()->user()?->dentist_id : null)
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('appointment_time', null);
                                    })
                                    ->columnSpan(['default' => 12, 'md' => 5])
                                    ->helperText(__('Leave blank for automatic assignment based on availability.')),

                                TextInput::make('duration_minutes')
                                    ->label(__('Duration (Minutes)'))
                                    ->numeric()
                                    ->default(45)
                                    ->minValue(15)
                                    ->maxValue(240)
                                    ->step(15)
                                    ->suffix('min')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('appointment_time', null);
                                    })
                                    ->required()
                                    ->columnSpan(['default' => 12, 'md' => 2])
                                    ->helperText(__('Clinical adjustment.')),
                            ]),
                    ]),

                // ==========================================
                // FILA 3: HORARIO Y CONTROL CLÍNICO (2 COLUMNAS EN UNA SOLA FILA)
                // ==========================================
                Grid::make(['default' => 1, 'lg' => 12])
                    ->columnSpanFull()
                    ->schema([
                        // COLUMNA 1: FECHA Y HORARIO DE ATENCIÓN (7 de 12 cols en desktop)
                        Section::make(__('Date & Appointment Time'))
                            ->description(__('Select date to check available time slots in real time according to medical schedule.'))
                            ->icon('heroicon-o-calendar-days')
                            ->columnSpan(['default' => 12, 'lg' => 7])
                            ->schema([
                                DatePicker::make('appointment_date')
                                    ->label(__('Appointment Date'))
                                    ->native(false)
                                    ->minDate(today())
                                    ->default(now()->isSunday() ? today()->addDay() : today())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('appointment_time', null);
                                    })
                                    ->required(),

                                ViewField::make('appointment_time')
                                    ->label(__('Shift & Time Slot'))
                                    ->view('filament.forms.components.time-slot-picker')
                                    ->formatStateUsing(fn ($state) => $state ? substr((string) $state, 0, 5) : null)
                                    ->dehydrateStateUsing(fn ($state) => $state ? substr((string) $state, 0, 5) : null)
                                    ->required()
                                    ->viewData(function (Get $get, ?Appointment $record = null): array {
                                        $date = $get('appointment_date');
                                        $dentistId = $get('dentist_id');
                                        $serviceId = $get('service_id');
                                        $durationMinutes = (int) ($get('duration_minutes') ?: 45);

                                        if (! $date) {
                                            return [
                                                'hasDate' => false,
                                                'hasSlots' => false,
                                                'morningSlots' => [],
                                                'afternoonSlots' => [],
                                            ];
                                        }

                                        $carbonDate = Carbon::parse($date);
                                        $date = $carbonDate->toDateString();
                                        $excludeId = $record?->id;

                                        /** @var User|null $user */
                                        $user = auth()->user();
                                        if ($user && $user->isDentist() && $user->dentist_id) {
                                            $dentistId = $user->dentist_id;
                                        }

                                        $service = $serviceId ? Service::find($serviceId) : null;

                                        if ($dentistId) {
                                            $dentist = Dentist::find($dentistId);
                                            $slots = $dentist ? $dentist->getSlotsForDate($date, $durationMinutes, $excludeId) : [];
                                        } else {
                                            // Unified across qualified specialists
                                            if ($service && $service->dentists()->exists()) {
                                                $dentists = $service->dentists()->where('is_active', true)->get();
                                            } else {
                                                $dentists = Dentist::where('is_active', true)->get();
                                            }

                                            $aggregatedSlots = [];
                                            foreach ($dentists as $doc) {
                                                $docSlots = $doc->getSlotsForDate($date, $durationMinutes, $excludeId);
                                                foreach ($docSlots as $slot) {
                                                    $time = $slot['time'];
                                                    if (! isset($aggregatedSlots[$time])) {
                                                        $aggregatedSlots[$time] = $slot;
                                                    } elseif ($slot['available']) {
                                                        $aggregatedSlots[$time]['available'] = true;
                                                    }
                                                }
                                            }
                                            ksort($aggregatedSlots);
                                            $slots = array_values($aggregatedSlots);
                                        }

                                        $formattedSlots = array_map(function ($slot) {
                                            $time = $slot['time'];

                                            return [
                                                'time' => $time,
                                                'label' => Appointment::formatTime($time),
                                                'shift' => $slot['shift'] ?? 'morning',
                                                'available' => (bool) ($slot['available'] ?? false),
                                                'reason' => $slot['reason'] ?? null,
                                            ];
                                        }, $slots);

                                        $morningSlots = array_values(array_filter($formattedSlots, fn ($s) => $s['shift'] === 'morning'));
                                        $afternoonSlots = array_values(array_filter($formattedSlots, fn ($s) => $s['shift'] === 'afternoon'));

                                        return [
                                            'hasDate' => true,
                                            'hasSlots' => collect($formattedSlots)->contains('available', true),
                                            'morningSlots' => $morningSlots,
                                            'afternoonSlots' => $afternoonSlots,
                                            'durationMinutes' => $durationMinutes,
                                        ];
                                    }),
                            ]),

                        // COLUMNA 2: CONTROL CLÍNICO Y ESTADO (5 de 12 cols en desktop)
                        Section::make(__('Clinical Control & Status'))
                            ->description(__('Administrative status, consultation reason, and internal notes.'))
                            ->icon('heroicon-o-clipboard-document-check')
                            ->columnSpan(['default' => 12, 'lg' => 5])
                            ->schema([
                                Select::make('status')
                                    ->label(__('Appointment Status'))
                                    ->options([
                                        'pending' => __('Pending Confirmation'),
                                        'confirmed' => __('Confirmed'),
                                        'in_progress' => __('In Consultation'),
                                        'completed' => __('Completed'),
                                        'cancelled' => __('Cancelled'),
                                        'no_show' => __('No Show'),
                                    ])
                                    ->default('pending')
                                    ->required(),

                                Textarea::make('symptoms')
                                    ->label(__('Notes or Chief Complaint (Optional)'))
                                    ->placeholder(__('Dental discomfort, scheduled cleaning, checkup...'))
                                    ->rows(3)
                                    ->nullable(),

                                Textarea::make('internal_notes')
                                    ->label(__('Internal Clinic Team Notes (Optional)'))
                                    ->placeholder(__('Private notes for reception or doctors...'))
                                    ->rows(3)
                                    ->nullable(),

                                Hidden::make('preferred_locale')
                                    ->default(fn () => app()->getLocale()),
                            ]),
                    ]),
            ]);
    }
}
