<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'foto_perfil',
        'password_hash',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',
            'rol' => RolUsuario::class,
            'activo' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password_hash'] = $value;
    }

    public function getNameAttribute(): ?string
    {
        return $this->nombre;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nombre'] = $value;
    }

    public function getRoleAttribute(): mixed
    {
        return $this->rol;
    }

    public function setRoleAttribute(mixed $value): void
    {
        $this->attributes['rol'] = $value instanceof RolUsuario ? $value->value : $value;
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->activo;
    }

    public function setIsActiveAttribute(bool $value): void
    {
        $this->attributes['activo'] = $value;
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellidos ?? ''));
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->telefono;
    }

    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['telefono'] = $value;
    }

    public function getAvatarPathAttribute(): ?string
    {
        return $this->foto_perfil;
    }

    public function setAvatarPathAttribute(?string $value): void
    {
        $this->attributes['foto_perfil'] = $value;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->foto_perfil) {
            return route('panel.media.mi-avatar');
        }

        return \Illuminate\Support\Facades\Vite::asset('resources/img/usuario-default.svg');
    }

    public function rolEnum(): ?RolUsuario
    {
        return $this->rol instanceof RolUsuario
            ? $this->rol
            : RolUsuario::tryFrom((string) $this->rol);
    }

    public function esAdmin(): bool
    {
        return $this->rolEnum() === RolUsuario::Admin;
    }

    public function esEntrenadorAdmin(): bool
    {
        return $this->rolEnum() === RolUsuario::EntrenadorAdmin;
    }

    public function esEntrenador(): bool
    {
        return $this->rolEnum() === RolUsuario::Entrenador;
    }

    public function puedeGestionarClub(): bool
    {
        return $this->rolEnum()?->puedeGestionarClub() ?? false;
    }

    public function puedeGestionarUsuarios(): bool
    {
        return $this->rolEnum()?->puedeGestionarUsuarios() ?? false;
    }

    public function getRolLabelAttribute(): string
    {
        return $this->rolEnum()?->label() ?? ucfirst((string) $this->rol);
    }
}