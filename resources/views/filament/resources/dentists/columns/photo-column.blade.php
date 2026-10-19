@php
    $record = $getRecord();
    $photoUrl = $record?->photo_url;
    $initials = $record?->initials ?? 'DR';
@endphp

<div class="relative group/photo inline-flex items-center justify-center p-0.5" style="width: 48px; height: 48px;">
    @if($photoUrl)
        <img src="{{ $photoUrl }}"
             alt="{{ $record->name }}"
             class="w-11 h-11 rounded-xl object-cover aspect-square shadow-2xs border border-slate-200 dark:border-white/10 transition-transform duration-200 group-hover/photo:scale-105"
             loading="lazy" />
    @else
        <!-- Avatar cuadrado 44x44px con radio 12px, fondo #0EA5E9, texto #0F172A, peso 800 (Estilo Agenda) -->
        <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 12px; background-color: #0EA5E9; color: #0F172A; font-weight: 800; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none;"
             class="shadow-2xs border border-sky-400/30 transition-transform duration-200 group-hover/photo:scale-105"
             title="{{ $record->name }}">
            {{ $initials }}
        </div>
    @endif

    <!-- Icono de cambiar imagen al pasar el cursor o hacer clic -->
    <button type="button"
            wire:click="mountTableAction('change_photo', '{{ $record->id }}')"
            @click.stop
            title="Cambiar fotografía de {{ $record->name }}"
            class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-slate-900/90 hover:bg-sky-600 text-white flex items-center justify-center shadow-md transition-all duration-150 opacity-80 group-hover/photo:opacity-100 hover:scale-110 cursor-pointer border-2 border-white dark:border-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </button>
</div>
