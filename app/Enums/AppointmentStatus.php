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
            self::Pending => __('Pending'),
            self::Confirmed => __('Confirmed'),
            self::InProgress => __('In Consultation'),
            self::Completed => __('Completed'),
            self::Cancelled => __('Cancelled'),
            self::NoShow => __('No Show'),
        };
    }

    public function badgeBackground(): string
    {
        return match ($this) {
            self::Pending => '#F59E0B',
            self::Confirmed => '#10B981',
            self::InProgress => '#0EA5E9',
            self::Completed => '#475569',
            self::Cancelled, self::NoShow => '#EF4444',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => '#FFFFFF',
            self::Confirmed => '#FFFFFF',
            self::InProgress => '#0F172A',
            self::Completed => '#FFFFFF',
            self::Cancelled, self::NoShow => '#FFFFFF',
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
