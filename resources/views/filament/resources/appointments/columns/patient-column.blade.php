@php
    $record = $getRecord();
    $name = trim((string) $record->patient_name);
    $words = preg_split('/\s+/', $name);
    $initials = '';
    if (! empty($words)) {
        $first = mb_substr($words[0], 0, 1, 'UTF-8');
        $last = count($words) > 1 ? mb_substr(end($words), 0, 1, 'UTF-8') : '';
        $initials = strtoupper($first . $last);
    }
    $phone = $record->patient_phone;
    $treatment = $record->service?->name;
@endphp

<div data-appointment-id="{{ $record->id }}"
     @click.stop="$dispatch('open-appointment-drawer', { id: {{ $record->id }} })"
     class="cursor-pointer"
     style="display: flex; align-items: center; gap: 12px; padding: 4px 0; min-width: 0; width: 100%;">
    <!-- Avatar cuadrado 44x44px con radio 14px, fondo #0EA5E9, texto #0F172A, peso 800, iniciales centradas -->
    <div style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; border-radius: 14px; background-color: #0EA5E9; color: #0F172A; font-weight: 800; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; user-select: none;">
        {{ $initials }}
    </div>

    <!-- Nombre a la derecha y teléfono debajo en dos líneas -->
    <div style="min-width: 0; flex: 1 1 auto; overflow: hidden;">
        <div style="font-weight: 800; font-size: 14px; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" class="text-slate-900 dark:text-white" title="{{ $name }}">
            {{ $name }}
        </div>
        @if($phone)
            <div class="agenda-patient-phone" style="font-size: 13px; font-weight: 500; line-height: 1.2; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                Tel. {{ $phone }}
            </div>
        @endif
    </div>
</div>
