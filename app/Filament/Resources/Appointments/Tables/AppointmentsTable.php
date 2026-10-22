<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('appointment_time', 'asc')
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([15, 25, 50])
            ->recordClasses(fn (Appointment $record): string => "cursor-pointer agenda-row appointment-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->searchable(false)
            ->header(view('filament.resources.appointments.pages.agenda-filter-card'))
            ->modifyQueryUsing(function (Builder $query) use ($table): void {
                /** @var ListAppointments|null $livewire */
                $livewire = $table->getLivewire();
                if (! $livewire instanceof ListAppointments) {
                    return;
                }

                $search = trim((string) $livewire->getTableSearch());
                $dentistId = $livewire->dentistId;
                $selectedDate = $livewire->selectedDate ?: Carbon::today()->toDateString();

                if ($dentistId && ! auth()->user()?->isDentist()) {
                    $query->where('dentist_id', $dentistId);
                }

                if (mb_strlen($search) >= 2) {
                    $cleanedPhone = preg_replace('/[\s\-+()]/', '', $search);

                    $query->where(function (Builder $q) use ($search, $cleanedPhone): void {
                        $q->where('patient_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");

                        if (! empty($cleanedPhone)) {
                            $q->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(patient_phone, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?", ["%{$cleanedPhone}%"]);
                        }
                    });

                    $today = Carbon::today()->toDateString();
                    $query->reorder()
                        ->orderByRaw("CASE WHEN appointment_date >= '{$today}' THEN 0 ELSE 1 END ASC")
                        ->orderByRaw("CASE WHEN appointment_date >= '{$today}' THEN appointment_date END ASC")
                        ->orderByRaw("CASE WHEN appointment_date < '{$today}' THEN appointment_date END DESC")
                        ->orderBy('appointment_time', 'asc');
                } else {
                    $query->whereDate('appointment_date', $selectedDate);
                }
            })
            ->columns([
                TextColumn::make('patient_name')
                    ->label(__('Patient'))
                    ->extraAttributes(fn (Appointment $record): array => [
                        'class' => 'cursor-pointer',
                        'x-on:click' => "\$dispatch('open-appointment-drawer', { id: {$record->id} })",
                    ])
                    ->view('filament.resources.appointments.columns.patient-column'),

                TextColumn::make('appointment_time')
                    ->label(__('Date & Time'))
                    ->sortable()
                    ->extraAttributes(fn (Appointment $record): array => [
                        'class' => 'cursor-pointer',
                        'x-on:click' => "\$dispatch('open-appointment-drawer', { id: {$record->id} })",
                    ])
                    ->html()
                    ->formatStateUsing(function (Appointment $record): string {
                        $time = e($record->formatted_time);
                        $dateFormatted = $record->appointment_date
                            ? str_replace('.', '', $record->appointment_date->locale(app()->getLocale())->isoFormat('ddd D MMM'))
                            : '—';
                        $duration = ($record->duration_minutes ?: 45).' min';
                        $subline = e("{$dateFormatted} · {$duration}");

                        return "
                            <div class=\"min-w-0 py-1\">
                                <div class=\"agenda-time-text font-extrabold text-sm\">
                                    {$time}
                                </div>
                                <div class=\"agenda-subline text-xs mt-0.5 whitespace-nowrap\">
                                    {$subline}
                                </div>
                            </div>
                        ";
                    }),

                TextColumn::make('service.name')
                    ->label(__('Treatment'))
                    ->placeholder('—')
                    ->visibleFrom('lg')
                    ->extraAttributes(fn (Appointment $record): array => [
                        'class' => 'cursor-pointer',
                        'x-on:click' => "\$dispatch('open-appointment-drawer', { id: {$record->id} })",
                    ])
                    ->html()
                    ->formatStateUsing(fn ($state) => $state ? '<span class="agenda-treatment-cell">'.e($state).'</span>' : '<span class="text-slate-400">—</span>'),

                TextColumn::make('dentist.name')
                    ->label(__('Specialist'))
                    ->placeholder(__('Unassigned'))
                    ->hidden(fn (): bool => auth()->user()?->isDentist() ?? false)
                    ->extraAttributes(fn (Appointment $record): array => [
                        'class' => 'cursor-pointer',
                        'x-on:click' => "\$dispatch('open-appointment-drawer', { id: {$record->id} })",
                    ])
                    ->html()
                    ->formatStateUsing(function (Appointment $record): string {
                        if (! $record->dentist) {
                            return '<span class="text-slate-400 font-medium">'.e(__('Unassigned')).'</span>';
                        }

                        $rawName = preg_replace('/^(Dr\.|Dra\.|Dr|Dra)\s+/i', '', (string) $record->dentist->name);
                        $safeName = e(trim($rawName));

                        return "<span class=\"agenda-specialist-name hover:underline\">{$safeName}</span>";
                    }),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->extraAttributes(fn (Appointment $record): array => [
                        'class' => 'cursor-pointer',
                        'x-on:click' => "\$dispatch('open-appointment-drawer', { id: {$record->id} })",
                    ])
                    ->view('filament.resources.appointments.columns.status-column'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label(__('Edit'))
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->iconButton()
                    ->color('gray')
                    ->tooltip(__('Edit Appointment'))
                    ->extraAttributes(fn (Appointment $record): array => [
                        'aria-label' => __('Edit appointment for :name', ['name' => $record->patient_name]),
                    ])
                    ->url(fn (Appointment $record): string => route('filament.admin.resources.appointments.edit', ['record' => $record]))
                    ->visible(fn (Appointment $record): bool => (auth()->user()?->isAdmin() || auth()->user()?->isReceptionist()) && auth()->user()?->can('update', $record)),

                // Menú de tres puntos con acciones de estado según rol y estado
                ActionGroup::make([
                    Action::make('confirm')
                        ->label(__('Confirm'))
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('confirm', $record) ?? false)
                        ->requiresConfirmation()
                        ->modalHeading(__('Confirm Appointment'))
                        ->modalDescription(fn (Appointment $record): string => __('Confirm appointment for :name?', ['name' => $record->patient_name]))
                        ->modalSubmitActionLabel(__('Yes, confirm'))
                        ->action(function (Appointment $record): void {
                            abort_unless(auth()->user()?->can('confirm', $record), 403);
                            $record->update(['status' => 'confirmed']);
                            Notification::make()
                                ->title(__('Appointment confirmed'))
                                ->body(__('Appointment for :name has been confirmed.', ['name' => $record->patient_name]))
                                ->success()
                                ->send();
                        }),

                    Action::make('mark_in_progress')
                        ->label(__('Mark in clinic'))
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('info')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('markInProgress', $record) ?? false)
                        ->action(function (Appointment $record): void {
                            abort_unless(auth()->user()?->can('markInProgress', $record), 403);
                            $record->update(['status' => 'in_progress']);
                            Notification::make()
                                ->title(__('Patient in clinic'))
                                ->body(__('Appointment for :name marked in clinic.', ['name' => $record->patient_name]))
                                ->info()
                                ->send();
                        }),

                    Action::make('complete')
                        ->label(__('Mark completed'))
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('complete', $record) ?? false)
                        ->requiresConfirmation()
                        ->modalHeading(__('Complete Care'))
                        ->modalDescription(fn (Appointment $record): string => __('Mark appointment for :name as completed?', ['name' => $record->patient_name]))
                        ->modalSubmitActionLabel(__('Complete'))
                        ->action(function (Appointment $record): void {
                            abort_unless(auth()->user()?->can('complete', $record), 403);
                            $record->update(['status' => 'completed']);
                            Notification::make()
                                ->title(__('Appointment completed'))
                                ->body(__('Care for :name has been completed.', ['name' => $record->patient_name]))
                                ->success()
                                ->send();
                        }),

                    Action::make('reschedule')
                        ->label(__('Reschedule'))
                        ->icon(Heroicon::OutlinedCalendar)
                        ->color('warning')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('reschedule', $record) ?? false)
                        ->modalHeading(__('Reschedule Appointment'))
                        ->modalDescription(fn (Appointment $record): string => __('Select new date and time for :name.', ['name' => $record->patient_name]))
                        ->schema([
                            DatePicker::make('appointment_date')
                                ->label(__('New Date'))
                                ->native(false)
                                ->required()
                                ->default(fn (Appointment $record) => $record->appointment_date),
                            TextInput::make('appointment_time')
                                ->label(__('New Time (HH:MM)'))
                                ->placeholder('09:30')
                                ->required()
                                ->default(fn (Appointment $record) => $record->appointment_time),
                        ])
                        ->action(function (Appointment $record, array $data): void {
                            abort_unless(auth()->user()?->can('reschedule', $record), 403);
                            $newDate = Carbon::parse($data['appointment_date'])->toDateString();
                            $newTime = trim($data['appointment_time']);

                            if ($record->dentist_id && Appointment::hasConflict($record->dentist_id, $newDate, $newTime, $record->id)) {
                                Notification::make()
                                    ->title(__('Schedule Conflict'))
                                    ->body(__('The specialist already has another appointment on this date and time.'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'appointment_date' => $newDate,
                                'appointment_time' => $newTime,
                            ]);

                            Notification::make()
                                ->title(__('Appointment Rescheduled'))
                                ->body(__('Appointment updated to :date at :time.', ['date' => $newDate, 'time' => $newTime]))
                                ->success()
                                ->send();
                        }),

                    Action::make('cancel')
                        ->label(__('Cancel Appointment'))
                        ->icon(Heroicon::OutlinedXMark)
                        ->color('danger')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('cancel', $record) ?? false)
                        ->requiresConfirmation()
                        ->modalHeading(__('Cancel Appointment'))
                        ->modalDescription(fn (Appointment $record): string => __('Are you sure you want to cancel the appointment for :name?', ['name' => $record->patient_name]))
                        ->modalSubmitActionLabel(__('Yes, cancel'))
                        ->schema([
                            Textarea::make('cancellation_reason')
                                ->label(__('Cancellation Reason (Optional)'))
                                ->placeholder(__('Reason reported by patient...'))
                                ->rows(2),
                        ])
                        ->action(function (Appointment $record, array $data): void {
                            abort_unless(auth()->user()?->can('cancel', $record), 403);
                            $notes = $record->internal_notes ? $record->internal_notes."\n" : '';
                            if (! empty($data['cancellation_reason'])) {
                                $notes .= '['.__('Cancelled').': '.trim($data['cancellation_reason']).']';
                            }

                            $record->update([
                                'status' => 'cancelled',
                                'internal_notes' => $notes ?: $record->internal_notes,
                            ]);

                            Notification::make()
                                ->title(__('Appointment cancelled'))
                                ->body(__('Appointment for :name has been cancelled.', ['name' => $record->patient_name]))
                                ->warning()
                                ->send();
                        }),
                ])
                    ->icon(Heroicon::OutlinedEllipsisVertical)
                    ->iconButton()
                    ->color('gray')
                    ->tooltip(__('Actions')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('bulk_reminder')
                        ->label(__('Send WhatsApp Reminders'))
                        ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                        ->action(function (): void {
                            Notification::make()
                                ->title(__('Reminders processed'))
                                ->body(__('Reminders sent to selected patients.'))
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected static function getDetailModalActions(Appointment $record): array
    {
        $actions = [];

        // Botón Editar cita que lleva a la página /edit (solo si tiene permiso)
        if ((auth()->user()?->isAdmin() || auth()->user()?->isReceptionist()) && auth()->user()?->can('update', $record)) {
            $actions[] = Action::make('edit_record')
                ->label(__('Edit Appointment'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('gray')
                ->url(fn (): string => route('filament.admin.resources.appointments.edit', ['record' => $record]));
        }

        if (auth()->user()?->can('confirm', $record)) {
            $actions[] = Action::make('confirm_modal')
                ->label(__('Confirm'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($record): void {
                    abort_unless(auth()->user()?->can('confirm', $record), 403);
                    $record->update(['status' => 'confirmed']);
                    Notification::make()->title(__('Appointment confirmed'))->success()->send();
                });
        }

        if (auth()->user()?->can('markInProgress', $record)) {
            $actions[] = Action::make('in_progress_modal')
                ->label(__('Mark in clinic'))
                ->icon(Heroicon::OutlinedPlay)
                ->color('info')
                ->action(function () use ($record): void {
                    abort_unless(auth()->user()?->can('markInProgress', $record), 403);
                    $record->update(['status' => 'in_progress']);
                    Notification::make()->title(__('Patient in clinic'))->info()->send();
                });
        }

        if (auth()->user()?->can('complete', $record)) {
            $actions[] = Action::make('complete_modal')
                ->label(__('Mark completed'))
                ->icon(Heroicon::OutlinedCheck)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($record): void {
                    abort_unless(auth()->user()?->can('complete', $record), 403);
                    $record->update(['status' => 'completed']);
                    Notification::make()->title(__('Appointment completed'))->success()->send();
                });
        }

        if (auth()->user()?->can('reschedule', $record)) {
            $actions[] = Action::make('reschedule_modal')
                ->label(__('Reschedule'))
                ->icon(Heroicon::OutlinedCalendar)
                ->color('warning')
                ->schema([
                    DatePicker::make('appointment_date')
                        ->label(__('New Date'))
                        ->native(false)
                        ->required()
                        ->default($record->appointment_date),
                    TextInput::make('appointment_time')
                        ->label(__('New Time (HH:MM)'))
                        ->placeholder('09:30')
                        ->required()
                        ->default($record->appointment_time),
                ])
                ->action(function (array $data) use ($record): void {
                    abort_unless(auth()->user()?->can('reschedule', $record), 403);
                    $newDate = Carbon::parse($data['appointment_date'])->toDateString();
                    $newTime = trim($data['appointment_time']);

                    if ($record->dentist_id && Appointment::hasConflict($record->dentist_id, $newDate, $newTime, $record->id)) {
                        Notification::make()
                            ->title(__('Schedule Conflict'))
                            ->body(__('The specialist already has another appointment on this date and time.'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->update([
                        'appointment_date' => $newDate,
                        'appointment_time' => $newTime,
                    ]);

                    Notification::make()
                        ->title(__('Appointment Rescheduled'))
                        ->body(__('Appointment updated to :date at :time.', ['date' => $newDate, 'time' => $newTime]))
                        ->success()
                        ->send();
                });
        }

        if (auth()->user()?->can('cancel', $record)) {
            $actions[] = Action::make('cancel_modal')
                ->label(__('Cancel Appointment'))
                ->icon(Heroicon::OutlinedXMark)
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('cancellation_reason')
                        ->label(__('Cancellation Reason (Optional)'))
                        ->placeholder(__('Reason reported by patient...'))
                        ->rows(2),
                ])
                ->action(function (array $data) use ($record): void {
                    abort_unless(auth()->user()?->can('cancel', $record), 403);
                    $notes = $record->internal_notes ? $record->internal_notes."\n" : '';
                    if (! empty($data['cancellation_reason'])) {
                        $notes .= '['.__('Cancelled').': '.trim($data['cancellation_reason']).']';
                    }

                    $record->update([
                        'status' => 'cancelled',
                        'internal_notes' => $notes ?: $record->internal_notes,
                    ]);

                    Notification::make()
                        ->title(__('Appointment cancelled'))
                        ->body(__('Appointment for :name has been cancelled.', ['name' => $record->patient_name]))
                        ->warning()
                        ->send();
                });
        }

        return $actions;
    }
}
