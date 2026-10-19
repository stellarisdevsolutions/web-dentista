@php
    $record = $getRecord();
    $photoUrl = $record?->photo_url;
    $initials = $record?->initials ?? 'DR';
@endphp

<div class="inline-flex items-center justify-center p-0.5" style="width: 48px; height: 48px;">
    @if($photoUrl)
        <img src="{{ $photoUrl }}"
             alt="{{ $record->name }}"
             class="w-11 h-11 rounded-xl object-cover aspect-square shadow-2xs border border-slate-200 dark:border-white/10"
             loading="lazy" />
    @else
        <!-- Avatar cuadrado 44x44px con radio 12px, fondo #0EA5E9, texto #0F172A, peso 800 (Estilo Agenda) -->
        <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 12px; background-color: #0EA5E9; color: #0F172A; font-weight: 800; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none;"
             class="shadow-2xs border border-sky-400/30"
             title="{{ $record->name }}">
            {{ $initials }}
        </div>
    @endif
</div>
