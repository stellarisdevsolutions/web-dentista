@php
    $record = $getRecord();
    $status = $record->status instanceof \App\Enums\AppointmentStatus
        ? $record->status
        : \App\Enums\AppointmentStatus::tryFrom((string) $record->status);

    $label = $status?->label() ?? ucfirst((string) $record->status);
    $statusKey = $status?->value ?? (string) $record->status;
@endphp

<div style="display: inline-flex; align-items: center;">
    <span class="aura-status-badge aura-status-{{ $statusKey }}" style="user-select: none;">
        {{ $label }}
    </span>
</div>
