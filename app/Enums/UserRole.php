<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Receptionist = 'receptionist';
    case Dentist = 'dentist';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('Administrator'),
            self::Receptionist => __('Receptionist'),
            self::Dentist => __('Dentist'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Receptionist => 'info',
            self::Dentist => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'heroicon-m-shield-check',
            self::Receptionist => 'heroicon-m-clipboard-document-list',
            self::Dentist => 'heroicon-m-academic-cap',
        };
    }
}
