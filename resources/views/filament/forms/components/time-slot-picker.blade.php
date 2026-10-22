<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $statePath = $getStatePath();
        $is12h = config('app.time_format', '12h') === '12h';
    @endphp

    <div x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            duration: {{ (int) ($durationMinutes ?? 45) }},
            is12h: {{ $is12h ? 'true' : 'false' }},
            formatSelectedRange() {
                if (!this.state) return '{{ __('None') }}';
                const clean = String(this.state).substring(0, 5);
                const parts = clean.split(':');
                if (parts.length !== 2) return clean;
                const h = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10);
                const totalStart = h * 60 + m;
                const totalEnd = totalStart + (this.duration || 45);

                const formatH = (min) => {
                    const hour24 = Math.floor(min / 60) % 24;
                    const minute = min % 60;
                    const padM = String(minute).padStart(2, '0');
                    if (!this.is12h) {
                        const padH = String(hour24).padStart(2, '0');
                        return `${padH}:${padM}`;
                    }
                    const period = hour24 >= 12 ? 'PM' : 'AM';
                    const hour12 = hour24 % 12 || 12;
                    const padH = String(hour12).padStart(2, '0');
                    return `${padH}:${padM} ${period}`;
                };

                return `${formatH(totalStart)} {{ __('to') }} ${formatH(totalEnd)} (${this.duration || 45} min)`;
            }
        }"
        class="w-full space-y-3.5 pt-1">

        {{-- Header de Horarios con Selección Actual y Botón de Limpiar --}}
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-white/10 pb-2.5">
            <div>
                <span class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">
                    {{ __('Available Shifts') }}
                </span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ __('Selected Time:') }} 
                    <strong class="text-sky-600 dark:text-sky-400 font-extrabold" x-text="formatSelectedRange()"></strong>
                </span>
            </div>

            <template x-if="state">
                <button type="button"
                        @click="state = null"
                        class="text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors cursor-pointer flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ __('Clear selection') }}</span>
                </button>
            </template>
        </div>

        {{-- Estado 1: No ha seleccionado fecha aún --}}
        @if (! $hasDate)
            <div class="p-4 rounded-2xl bg-sky-50/70 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-900/40 text-center space-y-1">
                <p class="text-xs font-bold text-sky-900 dark:text-sky-200 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>{{ __('Select appointment date') }}</span>
                </p>
                <p class="text-[11px] text-sky-700 dark:text-sky-300/80">
                    {{ __('Available morning and afternoon slots will be calculated in real time according to specialist and treatment.') }}
                </p>
            </div>
        {{-- Estado 2: Fecha seleccionada pero no hay cupos --}}
        @elseif (! $hasSlots)
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-center space-y-1">
                <p class="text-xs font-bold text-amber-900 dark:text-amber-200 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span>{{ __('No available slots for this date') }}</span>
                </p>
                <p class="text-[11px] text-amber-700 dark:text-amber-400">
                    {{ __('The selected dentist does not practice on this day or is fully booked. You can try another date or select "First Available Specialist".') }}
                </p>
            </div>
        {{-- Estado 3: Turnos cargados con éxito en bloques Mañana y Tarde --}}
        @else
            <div class="space-y-4 pt-1">
                {{-- Turno Mañana --}}
                @if (count($morningSlots) > 0)
                    <div>
                        <div class="flex items-center gap-1.5 mb-2 text-slate-700 dark:text-slate-300 font-extrabold text-[11px] uppercase tracking-wider">
                            <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v3"></path>
                                <path d="M4.93 4.93l2.12 2.12"></path>
                                <path d="M2 12h3"></path>
                                <path d="M19.07 4.93l-2.12 2.12"></path>
                                <path d="M22 12h-3"></path>
                                <path d="M5 16a7 7 0 0 1 14 0"></path>
                                <path d="M2 20h20"></path>
                            </svg>
                            <span>{{ __('Morning Shift') }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($morningSlots as $slot)
                                <button type="button"
                                        @if ($slot['available'])
                                            @click="state = '{{ $slot['time'] }}'"
                                        @endif
                                        @disabled(! $slot['available'])
                                        title="{{ $slot['available'] ? __('Select :time', ['time' => $slot['label']]) : ($slot['reason'] ?? __('Time slot occupied or outside schedule')) }}"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5 cursor-pointer"
                                        :class="{
                                            'bg-[#0A1730] text-white shadow-md ring-2 ring-sky-500 dark:bg-sky-500 dark:text-white': state === '{{ $slot['time'] }}' || (state && String(state).startsWith('{{ $slot['time'] }}')),
                                            'bg-white dark:bg-[#16294D] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/10 hover:border-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/50': (state !== '{{ $slot['time'] }}' && (!state || !String(state).startsWith('{{ $slot['time'] }}'))) && {{ $slot['available'] ? 'true' : 'false' }},
                                            'bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 line-through !cursor-not-allowed opacity-50 border border-transparent': {{ ! $slot['available'] ? 'true' : 'false' }}
                                        }">
                                    <template x-if="state === '{{ $slot['time'] }}' || (state && String(state).startsWith('{{ $slot['time'] }}'))">
                                        <svg class="w-3.5 h-3.5 text-sky-400 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </template>
                                    <span>{{ $slot['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Turno Tarde --}}
                @if (count($afternoonSlots) > 0)
                    <div>
                        <div class="flex items-center gap-1.5 mb-2 text-slate-700 dark:text-slate-300 font-extrabold text-[11px] uppercase tracking-wider">
                            <svg class="w-4 h-4 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13a7 7 0 0 1 14 0"></path>
                                <path d="M2 17h20"></path>
                                <path d="M4 21h16"></path>
                                <path d="M12 2v3"></path>
                                <path d="m4.93 4.93 2.12 2.12"></path>
                                <path d="m19.07 4.93-2.12 2.12"></path>
                            </svg>
                            <span>{{ __('Afternoon Shift') }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($afternoonSlots as $slot)
                                <button type="button"
                                        @if ($slot['available'])
                                            @click="state = '{{ $slot['time'] }}'"
                                        @endif
                                        @disabled(! $slot['available'])
                                        title="{{ $slot['available'] ? 'Seleccionar ' . $slot['label'] : ($slot['reason'] ?? 'Horario ocupado o fuera de turno') }}"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5 cursor-pointer"
                                        :class="{
                                            'bg-[#0A1730] text-white shadow-md ring-2 ring-sky-500 dark:bg-sky-500 dark:text-white': state === '{{ $slot['time'] }}' || (state && String(state).startsWith('{{ $slot['time'] }}')),
                                            'bg-white dark:bg-[#16294D] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-white/10 hover:border-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/50': (state !== '{{ $slot['time'] }}' && (!state || !String(state).startsWith('{{ $slot['time'] }}'))) && {{ $slot['available'] ? 'true' : 'false' }},
                                            'bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-500 line-through !cursor-not-allowed opacity-50 border border-transparent': {{ ! $slot['available'] ? 'true' : 'false' }}
                                        }">
                                    <template x-if="state === '{{ $slot['time'] }}' || (state && String(state).startsWith('{{ $slot['time'] }}'))">
                                        <svg class="w-3.5 h-3.5 text-sky-400 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </template>
                                    <span>{{ $slot['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-dynamic-component>
