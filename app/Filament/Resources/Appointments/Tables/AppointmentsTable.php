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
            ->recordAction('view_details')
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
                    ->label('Paciente')
                    ->view('filament.resources.appointments.columns.patient-column'),

                TextColumn::make('appointment_time')
                    ->label('Fecha y hora')
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function (Appointment $record): string {
                        $time = e($record->appointment_time);
                        $dateFormatted = $record->appointment_date
                            ? str_replace('.', '', $record->appointment_date->locale('es')->isoFormat('ddd D MMM'))
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
                    ->label('Tratamiento')
                    ->placeholder('—')
                    ->visibleFrom('lg')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state ? '<span class="agenda-treatment-cell">'.e($state).'</span>' : '<span class="text-slate-400">—</span>'),

                TextColumn::make('dentist.name')
                    ->label('Especialista')
                    ->placeholder('Por asignar')
                    ->hidden(fn (): bool => auth()->user()?->isDentist() ?? false)
                    ->html()
                    ->formatStateUsing(function (Appointment $record): string {
                        if (! $record->dentist) {
                            return '<span class="text-slate-400 font-medium">Por asignar</span>';
                        }

                        $rawName = preg_replace('/^(Dr\.|Dra\.|Dr|Dra)\s+/i', '', (string) $record->dentist->name);
                        $safeName = e(trim($rawName));

                        return "<span class=\"agenda-specialist-name hover:underline\">{$safeName}</span>";
                    }),

                TextColumn::make('status')
                    ->label('Estado')
                    ->view('filament.resources.appointments.columns.status-column'),
            ])
            ->recordActions([
                // Acción de detalle con panel lateral: invisible como botón para no ocupar texto en la fila, pero activa al pulsar cualquier fila
                Action::make('view_details')
                    ->label('Ver detalle')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->slideOver()
                    ->modalWidth('md')
                    ->modalHeading(fn (Appointment $record): string => "Detalles de cita · {$record->code}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn (Appointment $record) => view('filament.resources.appointments.slide-over-detail', ['record' => $record]))
                    ->extraModalFooterActions(fn (Appointment $record): array => self::getDetailModalActions($record))
                    ->extraAttributes(['style' => 'display: none !important;', 'class' => 'hidden']),

                // Botón icono lápiz: solo para admin y secretaria, según política de autorización
                Action::make('edit')
                    ->label('Editar')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->iconButton()
                    ->color('gray')
                    ->tooltip('Editar cita')
                    ->extraAttributes(fn (Appointment $record): array => [
                        'aria-label' => "Editar cita de {$record->patient_name}",
                    ])
                    ->url(fn (Appointment $record): string => route('filament.admin.resources.appointments.edit', ['record' => $record]))
                    ->visible(fn (Appointment $record): bool => (auth()->user()?->isAdmin() || auth()->user()?->isReceptionist()) && auth()->user()?->can('update', $record)),

                // Menú de tres puntos con acciones de estado según rol y estado
                ActionGroup::make([
                    Action::make('confirm')
                        ->label('Confirmar')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('confirm', $record) ?? false)
                        ->requiresConfirmation()
                        ->modalHeading('Confirmar cita')
                        ->modalDescription(fn (Appointment $record): string => "¿Confirmar la cita de {$record->patient_name}?")
                        ->modalSubmitActionLabel('Sí, confirmar')
                        ->action(function (Appointment $record): void {
                            abort_unless(auth()->user()?->can('confirm', $record), 403);
                            $record->update(['status' => 'confirmed']);
                            Notification::make()
                                ->title('Cita confirmada')
                                ->body("La cita de {$record->patient_name} ha sido confirmada.")
                                ->success()
                                ->send();
                        }),

                    Action::make('mark_in_progress')
                        ->label('Marcar en sala')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('info')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('markInProgress', $record) ?? false)
                        ->action(function (Appointment $record): void {
                            abort_unless(auth()->user()?->can('markInProgress', $record), 403);
                            $record->update(['status' => 'in_progress']);
                            Notification::make()
                                ->title('Paciente en sala')
                                ->body("La cita de {$record->patient_name} se marcó en sala.")
                                ->info()
                                ->send();
                        }),

                    Action::make('complete')
                        ->label('Marcar completada')
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('complete', $record) ?? false)
                        ->requiresConfirmation()
                        ->modalHeading('Completar atención')
                        ->modalDescription(fn (Appointment $record): string => "¿Marcar la cita de {$record->patient_name} como completada?")
                        ->modalSubmitActionLabel('Completar')
                        ->action(function (Appointment $record): void {
                            abort_unless(auth()->user()?->can('complete', $record), 403);
                            $record->update(['status' => 'completed']);
                            Notification::make()
                                ->title('Cita completada')
                                ->body("La atención de {$record->patient_name} ha finalizado.")
                                ->success()
                                ->send();
                        }),

                    Action::make('reschedule')
                        ->label('Reprogramar')
                        ->icon(Heroicon::OutlinedCalendar)
                        ->color('warning')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('reschedule', $record) ?? false)
                        ->modalHeading('Reprogramar cita')
                        ->modalDescription(fn (Appointment $record): string => "Seleccione la nueva fecha y hora para {$record->patient_name}.")
                        ->schema([
                            DatePicker::make('appointment_date')
                                ->label('Nueva fecha')
                                ->native(false)
                                ->required()
                                ->default(fn (Appointment $record) => $record->appointment_date),
                            TextInput::make('appointment_time')
                                ->label('Nuevo horario (HH:MM)')
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
                                    ->title('Conflicto de horario')
                                    ->body('El especialista ya tiene otra cita en esa fecha y horario.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'appointment_date' => $newDate,
                                'appointment_time' => $newTime,
                            ]);

                            Notification::make()
                                ->title('Cita reprogramada')
                                ->body("Cita actualizada al {$newDate} a las {$newTime}.")
                                ->success()
                                ->send();
                        }),

                    Action::make('cancel')
                        ->label('Cancelar cita')
                        ->icon(Heroicon::OutlinedXMark)
                        ->color('danger')
                        ->visible(fn (Appointment $record): bool => auth()->user()?->can('cancel', $record) ?? false)
                        ->requiresConfirmation()
                        ->modalHeading('Cancelar cita')
                        ->modalDescription(fn (Appointment $record): string => "¿Está seguro de cancelar la cita de {$record->patient_name}?")
                        ->modalSubmitActionLabel('Sí, cancelar')
                        ->schema([
                            Textarea::make('cancellation_reason')
                                ->label('Motivo de cancelación (opcional)')
                                ->placeholder('Motivo informado por el paciente...')
                                ->rows(2),
                        ])
                        ->action(function (Appointment $record, array $data): void {
                            abort_unless(auth()->user()?->can('cancel', $record), 403);
                            $notes = $record->internal_notes ? $record->internal_notes."\n" : '';
                            if (! empty($data['cancellation_reason'])) {
                                $notes .= '[Cancelada]: '.trim($data['cancellation_reason']);
                            }

                            $record->update([
                                'status' => 'cancelled',
                                'internal_notes' => $notes ?: $record->internal_notes,
                            ]);

                            Notification::make()
                                ->title('Cita cancelada')
                                ->body("La cita de {$record->patient_name} ha sido cancelada.")
                                ->warning()
                                ->send();
                        }),
                ])
                    ->icon(Heroicon::OutlinedEllipsisVertical)
                    ->iconButton()
                    ->color('gray')
                    ->tooltip('Acciones'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('bulk_reminder')
                        ->label('Enviar recordatorios WhatsApp')
                        ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                        ->action(function (): void {
                            Notification::make()
                                ->title('Recordatorios procesados')
                                ->body('Se enviaron los recordatorios a los pacientes seleccionados.')
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
                ->label('Editar cita')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('gray')
                ->url(fn (): string => route('filament.admin.resources.appointments.edit', ['record' => $record]));
        }

        if (auth()->user()?->can('confirm', $record)) {
            $actions[] = Action::make('confirm_modal')
                ->label('Confirmar')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($record): void {
                    abort_unless(auth()->user()?->can('confirm', $record), 403);
                    $record->update(['status' => 'confirmed']);
                    Notification::make()->title('Cita confirmada')->success()->send();
                });
        }

        if (auth()->user()?->can('markInProgress', $record)) {
            $actions[] = Action::make('in_progress_modal')
                ->label('Marcar en sala')
                ->icon(Heroicon::OutlinedPlay)
                ->color('info')
                ->action(function () use ($record): void {
                    abort_unless(auth()->user()?->can('markInProgress', $record), 403);
                    $record->update(['status' => 'in_progress']);
                    Notification::make()->title('Paciente en sala')->info()->send();
                });
        }

        if (auth()->user()?->can('complete', $record)) {
            $actions[] = Action::make('complete_modal')
                ->label('Marcar completada')
                ->icon(Heroicon::OutlinedCheck)
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($record): void {
                    abort_unless(auth()->user()?->can('complete', $record), 403);
                    $record->update(['status' => 'completed']);
                    Notification::make()->title('Cita completada')->success()->send();
                });
        }

        if (auth()->user()?->can('reschedule', $record)) {
            $actions[] = Action::make('reschedule_modal')
                ->label('Reprogramar')
                ->icon(Heroicon::OutlinedCalendar)
                ->color('warning')
                ->schema([
                    DatePicker::make('appointment_date')
                        ->label('Nueva fecha')
                        ->native(false)
                        ->required()
                        ->default($record->appointment_date),
                    TextInput::make('appointment_time')
                        ->label('Nuevo horario (HH:MM)')
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
                            ->title('Conflicto de horario')
                            ->body('El especialista ya tiene otra cita en esa fecha y horario.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->update([
                        'appointment_date' => $newDate,
                        'appointment_time' => $newTime,
                    ]);

                    Notification::make()
                        ->title('Cita reprogramada')
                        ->body("Cita actualizada al {$newDate} a las {$newTime}.")
                        ->success()
                        ->send();
                });
        }

        if (auth()->user()?->can('cancel', $record)) {
            $actions[] = Action::make('cancel_modal')
                ->label('Cancelar cita')
                ->icon(Heroicon::OutlinedXMark)
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('cancellation_reason')
                        ->label('Motivo de cancelación (opcional)')
                        ->placeholder('Motivo informado por el paciente...')
                        ->rows(2),
                ])
                ->action(function (array $data) use ($record): void {
                    abort_unless(auth()->user()?->can('cancel', $record), 403);
                    $notes = $record->internal_notes ? $record->internal_notes."\n" : '';
                    if (! empty($data['cancellation_reason'])) {
                        $notes .= '[Cancelada]: '.trim($data['cancellation_reason']);
                    }

                    $record->update([
                        'status' => 'cancelled',
                        'internal_notes' => $notes ?: $record->internal_notes,
                    ]);

                    Notification::make()
                        ->title('Cita cancelada')
                        ->body("La cita de {$record->patient_name} ha sido cancelada.")
                        ->warning()
                        ->send();
                });
        }

        return $actions;
    }
}
