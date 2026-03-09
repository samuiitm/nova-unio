<?php

namespace App\Enums;

enum RolUsuario: string
{
    case Admin = 'admin';
    case EntrenadorAdmin = 'entrenador_admin';
    case Entrenador = 'entrenador';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::EntrenadorAdmin => 'Entrenador admin',
            self::Entrenador => 'Entrenador',
        };
    }

    public function puedeGestionarClub(): bool
    {
        return in_array($this, [self::Admin, self::EntrenadorAdmin], true);
    }

    public function puedeGestionarUsuarios(): bool
    {
        return $this === self::Admin;
    }
}