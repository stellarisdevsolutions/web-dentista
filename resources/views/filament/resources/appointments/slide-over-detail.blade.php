@php
    $names = preg_split('/\s+/', trim((string) $record->patient_name));
    $initials = '';
    if (! empty($names)) {
        $first = mb_substr($names[0], 0, 1, 'UTF-8');
        $last = count($names) > 1 ? mb_substr(end($names), 0, 1, 'UTF-8') : '';
        $initials = strtoupper($first . $last);
    }

    $status = $record->status instanceof \App\Enums\AppointmentStatus
        ? $record->status
        : \App\Enums\AppointmentStatus::tryFrom((string) $record->status);

    $statusLabel = $status?->label() ?? ucfirst((string) $record->status);
    $statusBg = $status?->badgeBackground() ?? '#E6ECF4';
    $statusColor = $status?->badgeColor() ?? '#5B6B86';

    $formattedDate = $record->appointment_date
        ? \Carbon\Carbon::parse($record->appointment_date)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY')
        : '—';
    $duration = ($record->duration_minutes ?: 45) . ' min';
@endphp

<div class="space-y-5 text-sm text-slate-700 dark:text-slate-200">
    <!-- Header: Código y Estado -->
    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Código de cita</span>
            <div class="font-mono font-extrabold text-sky-600 dark:text-sky-400 text-base mt-0.5">
                {{ $record->code }}
            </div>
        </div>
        <div>
            <span style="background-color: {{ $statusBg }}; color: {{ $statusColor }};" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold tracking-wide">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <!-- Ficha del Paciente -->
    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
            <svg width="16" height="16" style="width: 16px !important; height: 16px !important; min-width: 16px !important; max-width: 16px !important; display: block;" class="text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Paciente
        </h4>

        <div class="flex items-center gap-3 pt-1">
            <div style="background-color: #0EA5E9; color: #0F172A; border-radius: 14px; width: 44px; height: 44px;" class="shrink-0 flex items-center justify-center font-extrabold text-sm select-none">
                {{ $initials }}
            </div>
            <div class="min-w-0">
                <div class="font-extrabold text-slate-900 dark:text-white text-base truncate">
                    {{ $record->patient_name }}
                </div>
                @if($record->patient_phone)
                    <div class="mt-0.5">
                        <a href="{{ $record->whatsapp_url }}" target="_blank" class="text-[13px] text-emerald-600 dark:text-emerald-400 hover:underline inline-flex items-center gap-1 font-semibold">
                            <span>{{ $record->patient_phone }}</span>
                            <svg width="14" height="14" style="width: 14px !important; height: 14px !important; min-width: 14px !important; max-width: 14px !important; display: block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if($record->patient_email)
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-xs flex items-center justify-between">
                <span class="text-slate-400 font-medium">Correo:</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->patient_email }}</span>
            </div>
        @endif
    </div>

    <!-- Fecha, Horario y Duración -->
    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
            <svg width="16" height="16" style="width: 16px !important; height: 16px !important; min-width: 16px !important; max-width: 16px !important; display: block;" class="text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Fecha y horario
        </h4>

        <div class="grid grid-cols-2 gap-3 pt-1">
            <div>
                <span class="text-xs text-slate-400">Fecha</span>
                <p class="font-bold text-slate-900 dark:text-white capitalize text-sm mt-0.5">
                    {{ $formattedDate }}
                </p>
            </div>
            <div>
                <span class="text-xs text-slate-400">Horario y duración</span>
                <p class="font-extrabold text-slate-900 dark:text-white text-sm mt-0.5">
                    {{ $record->formatted_time }} <span class="font-normal text-slate-400">· {{ $duration }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Tratamiento y Especialista -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Tratamiento -->
        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-1.5">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tratamiento</span>
            <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                {{ $record->service?->name ?? '—' }}
            </div>
            @if($record->service?->price_from)
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    Desde ${{ number_format($record->service->price_from, 0) }}
                </div>
            @endif
        </div>

        <!-- Especialista -->
        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-1.5">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Especialista</span>
            <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                {{ $record->dentist?->name ?? 'Por asignar' }}
            </div>
            @if($record->dentist?->specialty)
                <div class="text-xs text-sky-600 dark:text-sky-400 font-medium">
                    {{ $record->dentist->specialty }}
                </div>
            @endif
        </div>
    </div>

    <!-- Datos Operativos de Recepción -->
    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-2.5 text-xs">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Detalles operativos</h4>
        
        <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-400">Sillón</span>
            <span class="font-medium text-slate-700 dark:text-slate-300">No asignado</span>
        </div>

        <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-400">Recordatorio</span>
            <span class="font-medium text-slate-700 dark:text-slate-300">Sin enviar</span>
        </div>

        <div class="flex items-center justify-between py-1 border-b border-slate-100 dark:border-slate-800">
            <span class="text-slate-400">Origen de la cita</span>
            <span class="font-medium text-slate-700 dark:text-slate-300">Recepción</span>
        </div>
    </div>

    <!-- Notas de Recepción (sin notas clínicas ni diagnósticos) -->
    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-2">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Notas de recepción</h4>
        <p class="text-xs leading-relaxed text-slate-700 dark:text-slate-300">
            {{ $record->internal_notes ?: 'Sin notas de recepción registradas.' }}
        </p>
    </div>
</div>
