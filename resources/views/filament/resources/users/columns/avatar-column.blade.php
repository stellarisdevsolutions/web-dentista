@php
    $record = $getRecord();
    $photoUrl = $record?->dentist?->photo_url;
    $initials = $record?->initials ?? 'US';
    $role = $record?->role;

    $bgColor = match ($role) {
        \App\Enums\UserRole::Admin => '#6366F1', // Indigo
        \App\Enums\UserRole::Receptionist => '#0EA5E9', // Sky
        \App\Enums\UserRole::Dentist => '#10B981', // Emerald
        default => '#64748B',
    };
@endphp

<div class="inline-flex items-center justify-center p-0.5" style="width: 44px; height: 44px;">
    @if($photoUrl)
        <img src="{{ $photoUrl }}"
             alt="{{ $record->name }}"
             class="w-10 h-10 rounded-xl object-cover aspect-square shadow-2xs border border-slate-200 dark:border-white/10"
             loading="lazy" />
    @else
        <div style="width: 38px; height: 38px; min-width: 38px; max-width: 38px; border-radius: 10px; background-color: {{ $bgColor }}; color: #FFFFFF; font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none;"
             class="shadow-2xs border border-white/20"
             title="{{ $record->name }}">
            {{ $initials }}
        </div>
    @endif
</div>
