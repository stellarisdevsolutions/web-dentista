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
            self::Admin => 'Administrador',
            self::Receptionist => 'Recepción',
            self::Dentist => 'Odontólogo',
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
}
