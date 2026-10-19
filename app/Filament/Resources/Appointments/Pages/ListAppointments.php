<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    #[Url(as: 'fecha')]
    public string $selectedDate = '';

    #[Url(as: 'especialista')]
    public ?int $dentistId = null;

    #[Url(as: 'estado')]
    public ?string $activeTab = 'all';

    public function mount(): void
    {
        parent::mount();

        if (empty($this->selectedDate)) {
            $this->selectedDate = Carbon::today()->toDateString();
        }

        if (empty($this->activeTab)) {
            $this->activeTab = 'all';
        }
    }

    public function updatedSelectedDate(): void
    {
        $this->resetPage();
        $this->resetTable();
    }

    public function updatedDentistId(): void
    {
        $this->resetPage();
        $this->resetTable();
    }

    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate ?: Carbon::today())->subDay()->toDateString();
        $this->resetPage();
        $this->resetTable();
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate ?: Carbon::today())->addDay()->toDateString();
        $this->resetPage();
        $this->resetTable();
    }

    public function goToToday(): void
    {
        $this->selectedDate = Carbon::today()->toDateString();
        $this->resetPage();
        $this->resetTable();
    }

    public function hasActiveFilters(): bool
    {
        $hasDentist = ! empty($this->dentistId) && ! auth()->user()?->isDentist();
        $hasTab = ! empty($this->activeTab) && $this->activeTab !== 'all';
        $hasSearch = mb_strlen(trim((string) $this->getTableSearch())) >= 2;
        $isNotToday = ! empty($this->selectedDate) && $this->selectedDate !== Carbon::today()->toDateString();

        return $hasDentist || $hasTab || $hasSearch || $isNotToday;
    }

    public function clearAllFilters(): void
    {
        $this->dentistId = null;
        $this->activeTab = 'all';
        $this->selectedDate = Carbon::today()->toDateString();
        $this->resetTableSearch();
        $this->resetPage();
        $this->resetTable();

        $this->dispatch('agenda-filters-cleared', date: Carbon::today()->toDateString());
    }

    public function clearSearch(): void
    {
        $this->resetTableSearch();
    }

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->resetTable();
    }

    public function getTitle(): string
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->isDentist()) {
            return 'Mis citas';
        }

        return 'Agenda';
    }

    public function getSubheading(): ?string
    {
        $dateCarbon = Carbon::parse($this->selectedDate ?: Carbon::today());
        $dateText = str_replace('.', '', $dateCarbon->locale('es')->isoFormat('dddd D [de] MMMM'));

        $counts = $this->getTabCounts();
        $count = $counts['all'] ?? 0;

        $search = trim((string) $this->getTableSearch());
        if (mb_strlen($search) >= 2) {
            return "{$count} ".($count === 1 ? 'cita encontrada' : 'citas encontradas').' en la búsqueda';
        }

        return "{$count} ".($count === 1 ? 'cita' : 'citas')." el {$dateText}";
    }

    public function getFormattedDateLabel(): string
    {
        $dateCarbon = Carbon::parse($this->selectedDate ?: Carbon::today());
        $isToday = $dateCarbon->isToday();
        $formatted = str_replace('.', '', $dateCarbon->locale('es')->isoFormat('ddd D MMM'));

        return $isToday ? "Hoy · {$formatted}" : $formatted;
    }

    /**
     * @return Collection<int, Dentist>
     */
    public function getDentists(): Collection
    {
        return Dentist::query()->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * @return array<string, int>
     */
    public function getTabCounts(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();

        if ($this->dentistId && ! auth()->user()?->isDentist()) {
            $baseQuery->where('dentist_id', $this->dentistId);
        }

        $search = trim((string) $this->getTableSearch());
        if (mb_strlen($search) >= 2) {
            $cleanedPhone = preg_replace('/[\s\-+()]/', '', $search);
            $baseQuery->where(function (Builder $q) use ($search, $cleanedPhone): void {
                $q->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
                if (! empty($cleanedPhone)) {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(patient_phone, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?", ["%{$cleanedPhone}%"]);
                }
            });
        } else {
            $baseQuery->whereDate('appointment_date', $this->selectedDate ?: Carbon::today()->toDateString());
        }

        return [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'cancelled' => (clone $baseQuery)->whereIn('status', ['cancelled', 'no_show'])->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Nueva cita')
                ->extraAttributes([
                    'class' => 'agenda-create-action-btn',
                    'style' => 'border-radius: 9999px !important; background-color: #0EA5E9 !important; color: #0F172A !important; font-weight: 800 !important; min-height: 48px !important; padding: 0 24px !important; box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40) !important; border: none !important; font-size: 14px !important;',
                ])
                ->visible(fn (): bool => ! auth()->user()?->isDentist()),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas'),
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'confirmed' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['confirmed', 'in_progress'])),
            'cancelled' => Tab::make('Canceladas')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['cancelled', 'no_show'])),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    /**
     * Fetch appointment details for the instant slide-over drawer.
     *
     * @return array<string, mixed>|null
     */
    #[Renderless]
    public function getAppointmentDetails(int $id): ?array
    {
        try {
            /** @var User|null $user */
            $user = auth()->user();

            $query = Appointment::with(['service', 'dentist']);

            if ($user && $user->isDentist() && $user->dentist_id) {
                $query->where('dentist_id', $user->dentist_id);
            }

            $app = $query->find($id);
            if (! $app) {
                return null;
            }

            $words = preg_split('/\s+/', trim((string) $app->patient_name));
            $initials = '';
            if (! empty($words)) {
                $first = mb_substr($words[0], 0, 1, 'UTF-8');
                $last = count($words) > 1 ? mb_substr(end($words), 0, 1, 'UTF-8') : '';
                $initials = strtoupper($first.$last);
            }

            $status = $app->status instanceof AppointmentStatus
                ? $app->status
                : AppointmentStatus::tryFrom((string) $app->status);

            return [
                'id' => $app->id,
                'code' => $app->code,
                'patient_name' => $app->patient_name,
                'patient_initials' => $initials,
                'patient_phone' => $app->patient_phone,
                'patient_email' => $app->patient_email,
                'whatsapp_url' => $app->whats_app_url,
                'appointment_date' => $app->appointment_date ? $app->appointment_date->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') : '—',
                'appointment_date_raw' => $app->appointment_date ? $app->appointment_date->toDateString() : '',
                'appointment_time' => $app->formatted_time,
                'appointment_time_raw' => $app->appointment_time,
                'duration' => ($app->duration_minutes ?: 45).' min',
                'duration_minutes' => $app->duration_minutes ?: 45,
                'status' => $status?->value ?? (string) $app->status,
                'status_label' => $status?->label() ?? ucfirst((string) $app->status),
                'status_bg' => $status?->badgeBackground() ?? '#E6ECF4',
                'status_color' => $status?->badgeColor() ?? '#5B6B86',
                'service_id' => $app->service_id,
                'service_name' => $app->service?->name ?? '—',
                'service_price' => $app->service?->price_from ? '$'.number_format($app->service->price_from, 0) : null,
                'dentist_id' => $app->dentist_id,
                'dentist_name' => $app->dentist?->name ?? 'Por asignar',
                'dentist_specialty' => $app->dentist?->specialty,
                'internal_notes' => $app->internal_notes,
                'symptoms' => $app->symptoms,
                'is_first_visit' => (bool) $app->is_first_visit,
                'is_first_visit_label' => $app->is_first_visit ? 'Primera visita (Apertura de ficha)' : 'Paciente recurrente',
                'created_at_formatted' => $app->created_at ? $app->created_at->locale('es')->isoFormat('D MMM YYYY, h:mm a') : '—',
                'edit_url' => route('filament.admin.resources.appointments.edit', ['record' => $app]),
                'can_edit' => ($user?->isAdmin() || $user?->isReceptionist()) && $user?->can('update', $app),
                'can_confirm' => $user?->can('confirm', $app),
                'can_mark_in_progress' => $user?->can('markInProgress', $app),
                'can_complete' => $user?->can('complete', $app),
                'can_reschedule' => $user?->can('reschedule', $app),
                'can_cancel' => $user?->can('cancel', $app),
            ];
        } catch (\Throwable $e) {
            Log::error('Error fetching appointment details in drawer: '.$e->getMessage(), [
                'exception' => $e,
                'id' => $id,
            ]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function drawerConfirm(int $id): ?array
    {
        $appointment = Appointment::findOrFail($id);
        abort_unless(auth()->user()?->can('confirm', $appointment), 403);
        $appointment->update(['status' => 'confirmed']);
        Notification::make()
            ->title('Cita confirmada')
            ->body("La cita de {$appointment->patient_name} ha sido confirmada.")
            ->success()
            ->send();

        $this->resetTable();

        return $this->getAppointmentDetails($id);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function drawerMarkInProgress(int $id): ?array
    {
        $appointment = Appointment::findOrFail($id);
        abort_unless(auth()->user()?->can('markInProgress', $appointment), 403);
        $appointment->update(['status' => 'in_progress']);
        Notification::make()
            ->title('Paciente en sala')
            ->body("La cita de {$appointment->patient_name} se marcó en sala.")
            ->info()
            ->send();

        $this->resetTable();

        return $this->getAppointmentDetails($id);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function drawerComplete(int $id): ?array
    {
        $appointment = Appointment::findOrFail($id);
        abort_unless(auth()->user()?->can('complete', $appointment), 403);
        $appointment->update(['status' => 'completed']);
        Notification::make()
            ->title('Cita completada')
            ->body("La atención de {$appointment->patient_name} ha finalizado.")
            ->success()
            ->send();

        $this->resetTable();

        return $this->getAppointmentDetails($id);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function drawerCancel(int $id, ?string $reason = null): ?array
    {
        $appointment = Appointment::findOrFail($id);
        abort_unless(auth()->user()?->can('cancel', $appointment), 403);
        $notes = $appointment->internal_notes ? $appointment->internal_notes."\n" : '';
        if (! empty($reason)) {
            $notes .= '[Cancelada]: '.trim($reason);
        }
        $appointment->update([
            'status' => 'cancelled',
            'internal_notes' => $notes ?: $appointment->internal_notes,
        ]);
        Notification::make()
            ->title('Cita cancelada')
            ->body("La cita de {$appointment->patient_name} ha sido cancelada.")
            ->warning()
            ->send();

        $this->resetTable();

        return $this->getAppointmentDetails($id);
    }

    /**
     * Get initial catalogs for rescheduling modal (services, specialists).
     *
     * @return array{services: array<int, array{id: int, name: string, duration_minutes: int, dentist_ids: array<int>}>, dentists: array<int, array{id: int, name: string, specialty: ?string}>, is_dentist_user: bool, user_dentist_id: ?int}
     */
    #[Renderless]
    public function getRescheduleInitialData(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $isDentist = (bool) ($user && $user->isDentist());
        $userDentistId = $isDentist ? $user->dentist_id : null;

        $services = Service::query()
            ->where('is_active', true)
            ->with('dentists:id')
            ->orderBy('order')
            ->get()
            ->map(fn (Service $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'duration_minutes' => (int) ($s->duration_minutes ?: 45),
                'dentist_ids' => $s->dentists->pluck('id')->all(),
            ])
            ->all();

        $dentists = Dentist::query()
            ->where('is_active', true)
            ->when($isDentist && $userDentistId, fn ($q) => $q->where('id', $userDentistId))
            ->orderBy('name')
            ->get()
            ->map(fn (Dentist $d) => [
                'id' => $d->id,
                'name' => preg_replace('/^(Dr\.|Dra\.|Dr|Dra)\s+/i', '', $d->name),
                'specialty' => $d->specialty,
            ])
            ->all();

        return [
            'services' => $services,
            'dentists' => $dentists,
            'is_dentist_user' => $isDentist,
            'user_dentist_id' => $userDentistId,
        ];
    }

    /**
     * Calculate available morning and afternoon slots for rescheduling.
     *
     * @return array{morning_slots: array<int, array{time: string, label: string, shift: string, available: bool}>, afternoon_slots: array<int, array{time: string, label: string, shift: string, available: bool}>, has_available_slots: bool, error?: string}
     */
    #[Renderless]
    public function getRescheduleAvailableSlots(string $date, ?int $dentistId = null, ?int $serviceId = null, ?int $appointmentId = null): array
    {
        try {
            $carbonDate = Carbon::parse($date);
            if ($carbonDate->isPast() && ! $carbonDate->isToday()) {
                return [
                    'morning_slots' => [],
                    'afternoon_slots' => [],
                    'has_available_slots' => false,
                    'error' => 'No se pueden programar citas en fechas pasadas.',
                ];
            }

            $service = $serviceId ? Service::find($serviceId) : null;
            $durationMinutes = (int) ($service?->duration_minutes ?: 45);

            /** @var User|null $user */
            $user = auth()->user();
            if ($user && $user->isDentist() && $user->dentist_id) {
                $dentistId = $user->dentist_id;
            }

            if ($dentistId) {
                // If service is specified and has specialists assigned, verify dentist performs it
                if ($service && $service->dentists()->exists() && ! $service->dentists()->where('dentists.id', $dentistId)->exists()) {
                    return [
                        'morning_slots' => [],
                        'afternoon_slots' => [],
                        'has_available_slots' => false,
                        'error' => 'El especialista seleccionado no realiza este tratamiento.',
                    ];
                }

                $dentist = Dentist::find($dentistId);
                $slots = $dentist ? $dentist->getSlotsForDate($date, $durationMinutes, $appointmentId) : [];
            } else {
                // Aggregate slots strictly across active specialists qualified for this service
                if ($service && $service->dentists()->exists()) {
                    $dentists = $service->dentists()->where('is_active', true)->get();
                } else {
                    $dentists = Dentist::where('is_active', true)->get();
                }

                if ($dentists->isEmpty()) {
                    return [
                        'morning_slots' => [],
                        'afternoon_slots' => [],
                        'has_available_slots' => false,
                    ];
                }

                $aggregatedSlots = [];
                foreach ($dentists as $doc) {
                    $docSlots = $doc->getSlotsForDate($date, $durationMinutes, $appointmentId);
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
                ];
            }, $slots);

            $morningSlots = array_values(array_filter($formattedSlots, fn ($s) => $s['shift'] === 'morning'));
            $afternoonSlots = array_values(array_filter($formattedSlots, fn ($s) => $s['shift'] === 'afternoon'));

            return [
                'morning_slots' => $morningSlots,
                'afternoon_slots' => $afternoonSlots,
                'has_available_slots' => collect($formattedSlots)->contains('available', true),
            ];
        } catch (\Throwable $e) {
            return [
                'morning_slots' => [],
                'afternoon_slots' => [],
                'has_available_slots' => false,
                'error' => 'Error al calcular horarios disponibles.',
            ];
        }
    }

    /**
     * @return array{success: bool, error?: string, data?: array<string, mixed>|null}
     */
    public function drawerReschedule(int $id, string $date, string $time, ?int $serviceId = null, ?int $dentistId = null): array
    {
        $appointment = Appointment::findOrFail($id);
        abort_unless(auth()->user()?->can('reschedule', $appointment), 403);
        $newDate = Carbon::parse($date)->toDateString();
        $newTime = trim($time);

        $serviceId = $serviceId ?: $appointment->service_id;
        $service = $serviceId ? Service::find($serviceId) : null;
        $durationMinutes = (int) ($service?->duration_minutes ?: ($appointment->duration_minutes ?: 45));

        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->isDentist() && $user->dentist_id) {
            $dentistId = $user->dentist_id;
        }

        if ($dentistId) {
            // Verify qualification if service has specialists
            if ($service && $service->dentists()->exists() && ! $service->dentists()->where('dentists.id', $dentistId)->exists()) {
                Notification::make()
                    ->title('Especialista no calificado')
                    ->body('El especialista seleccionado no realiza este tratamiento.')
                    ->danger()
                    ->send();

                return [
                    'success' => false,
                    'error' => 'El especialista seleccionado no realiza este tratamiento.',
                ];
            }

            $dentist = Dentist::find($dentistId);
            if ($dentist && ! $dentist->isTimeSlotAvailable($newDate, $newTime, $durationMinutes, $appointment->id)) {
                Notification::make()
                    ->title('Conflicto de horario')
                    ->body('El especialista no tiene disponibilidad en este horario.')
                    ->danger()
                    ->send();

                return [
                    'success' => false,
                    'error' => 'El especialista no tiene disponibilidad en este horario.',
                ];
            }
        } else {
            // Unified Rescheduling ("Primer especialista disponible"):
            // 1. Get qualified active dentists for this service
            $candidateQuery = $service && $service->dentists()->exists()
                ? $service->dentists()->where('is_active', true)
                : Dentist::where('is_active', true);

            $candidates = $candidateQuery
                ->withCount(['appointments' => fn ($q) => $q->whereDate('appointment_date', $newDate)->whereIn('status', ['confirmed', 'pending', 'in_progress'])])
                ->get();

            // 2. Filter dentists who are available at this slot
            $availableCandidates = $candidates->filter(function (Dentist $doc) use ($newDate, $newTime, $durationMinutes, $appointment) {
                return $doc->isTimeSlotAvailable($newDate, $newTime, $durationMinutes, $appointment->id);
            });

            if ($availableCandidates->isEmpty()) {
                Notification::make()
                    ->title('Horario no disponible')
                    ->body('Ningún especialista calificado para este tratamiento tiene disponibilidad en este horario.')
                    ->danger()
                    ->send();

                return [
                    'success' => false,
                    'error' => 'Ningún especialista calificado para este tratamiento tiene disponibilidad en este horario.',
                ];
            }

            // 3. Intelligent allocation: prefer original dentist if qualified and available, else least loaded
            $assignedDentist = $availableCandidates->firstWhere('id', $appointment->dentist_id)
                ?? $availableCandidates->sortBy('appointments_count')->first();

            $dentistId = $assignedDentist->id;
        }

        $appointment->update([
            'appointment_date' => $newDate,
            'appointment_time' => $newTime,
            'service_id' => $serviceId,
            'dentist_id' => $dentistId,
            'duration_minutes' => $durationMinutes,
        ]);

        Notification::make()
            ->title('Cita reprogramada')
            ->body("Cita actualizada al {$newDate} a las {$newTime}.")
            ->success()
            ->send();

        $this->resetTable();

        return [
            'success' => true,
            'data' => $this->getAppointmentDetails($id),
        ];
    }
}
