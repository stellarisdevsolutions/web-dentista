@php
    $record = $getRecord();
    $status = $record->status instanceof \App\Enums\AppointmentStatus
        ? $record->status
        : \App\Enums\AppointmentStatus::tryFrom((string) $record->status);

    $label = $status?->label() ?? ucfirst((string) $record->status);
    $statusKey = $status?->value ?? (string) $record->status;
@endphp

<div data-appointment-id="{{ $record->id }}"
     @click.stop="$dispatch('open-appointment-drawer', { id: {{ $record->id }} })"
     class="cursor-pointer"
     style="display: flex; align-items: center; width: 100%;">
    <span class="aura-status-badge aura-status-{{ $statusKey }}" style="user-select: none;">
        {{ $label }}
    </span>
</div>
