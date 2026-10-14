<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Confirmed => 'Confirmada',
            self::InProgress => 'En sala',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
            self::NoShow => 'No asistió',
        };
    }

    public function badgeBackground(): string
    {
        return match ($this) {
            self::Pending => '#FEF3C7',
            self::Confirmed => '#DCFCE7',
            self::InProgress => '#E0F2FE',
            self::Completed => '#E6ECF4',
            self::Cancelled, self::NoShow => '#FEE2E2',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => '#B45309',
            self::Confirmed => '#166534',
            self::InProgress => '#0A74B8',
            self::Completed => '#5B6B86',
            self::Cancelled, self::NoShow => '#B91C1C',
        };
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this === self::Confirmed;
    }

    public function isInProgress(): bool
    {
        return $this === self::InProgress;
    }

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    public function isNoShow(): bool
    {
        return $this === self::NoShow;
    }

    public function isActive(): bool
    {
        return ! in_array($this, [self::Cancelled, self::NoShow], true);
    }
}
