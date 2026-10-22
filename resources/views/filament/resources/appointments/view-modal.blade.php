<div class="space-y-4 text-sm text-slate-700 dark:text-slate-200">
    <!-- Fila Resumen Superior -->
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xs uppercase font-bold tracking-wider text-slate-400">{{ __('Appointment code') }}:</span>
            <span class="font-mono font-bold text-sky-600 dark:text-sky-400 text-base">{{ $record->code }}</span>
        </div>
        
        @php
            $statusKey = $record->status instanceof \App\Enums\AppointmentStatus ? $record->status->value : (string) $record->status;
            $statusBadges = [
                'pending' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800', 'label' => __('Pending confirmation')],
                'confirmed' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800', 'label' => __('Confirmed')],
                'in_progress' => ['bg' => 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800', 'label' => __('In consultation')],
                'completed' => ['bg' => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700', 'label' => __('Completed')],
                'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800', 'label' => __('Cancelled')],
                'no_show' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800', 'label' => __('No show')],
            ];
            $currentStatus = $statusBadges[$statusKey] ?? ['bg' => 'bg-slate-100 text-slate-800 border-slate-300', 'label' => ucfirst($statusKey)];
        @endphp

        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $currentStatus['bg'] }}">
            {{ $currentStatus['label'] }}
        </span>
    </div>

    <!-- Bloque 2 Columnas: Paciente y Programación -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Tarjeta Paciente -->
        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-2.5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                {{ __('Patient details') }}
            </h4>
            
            <div class="pt-1">
                <p class="font-bold text-slate-900 dark:text-white text-base">{{ $record->patient_name }}</p>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    @if($record->is_first_visit)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                            {{ __('First visit') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            {{ __('Returning patient') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">{{ __('Phone') }}:</span>
                    @if(app()->getLocale() === 'en')
                        <a href="tel:{{ $record->patient_phone }}" class="font-medium text-slate-800 dark:text-slate-200 hover:text-sky-600 dark:hover:text-sky-400 hover:underline inline-flex items-center gap-1">
                            {{ $record->patient_phone }}
                        </a>
                    @else
                        <a href="{{ $record->whatsapp_url }}" target="_blank" class="font-medium text-emerald-600 dark:text-emerald-400 hover:underline inline-flex items-center gap-1">
                            {{ $record->patient_phone }}
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    @endif
                </div>
                @if($record->patient_email)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">{{ __('Email') }}:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $record->patient_email }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta Programación Clínica -->
        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-2.5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                {{ __('Schedule & service') }}
            </h4>

            <div class="pt-1">
                <p class="font-bold text-slate-900 dark:text-white">{{ $record->service?->name ?? __('General consultation') }}</p>
                <p class="text-xs text-sky-600 dark:text-sky-400 font-medium">
                    Dr(a). {{ $record->dentist?->name ?? __('To assign') }}
                    @if($record->dentist?->specialty)
                        <span class="text-slate-400">({{ $record->dentist->specialty }})</span>
                    @endif
                </p>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">{{ __('Date') }}:</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        {{ $record->appointment_date ? \Carbon\Carbon::parse($record->appointment_date)->locale(app()->getLocale())->isoFormat(app()->getLocale() === 'es' ? 'dddd D [de] MMMM, YYYY' : 'dddd, MMMM D, YYYY') : '-' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">{{ __('Time') }}:</span>
                    <span class="font-semibold text-slate-900 dark:text-white">
                        {{ $record->formatted_time }} <span class="font-normal text-slate-400">({{ $record->duration_minutes }} {{ __('minutes') }})</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Motivo / Síntomas y Notas Internas -->
    <div class="space-y-3">
        <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60">
            <h5 class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                {{ __('Reason for appointment / Reported symptoms') }}
            </h5>
            <p class="text-xs leading-relaxed text-slate-700 dark:text-slate-300">
                {{ $record->symptoms ?: __('No specific observations recorded by the patient.') }}
            </p>
        </div>

        @if($record->internal_notes)
            <div class="p-3.5 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-950/20">
                <h5 class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Internal notes of clinical team') }}
                </h5>
                <p class="text-xs leading-relaxed text-amber-900 dark:text-amber-200">
                    {{ $record->internal_notes }}
                </p>
            </div>
        @endif
    </div>

    @if(app()->getLocale() !== 'en')
        <!-- Acceso Directo a WhatsApp -->
        <div class="pt-2 flex items-center justify-end">
            <a href="{{ $record->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                {{ __('Contact patient via WhatsApp') }}
            </a>
        </div>
    @endif
</div>
