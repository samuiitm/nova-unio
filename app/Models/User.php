<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Tu tabla renombrada
    protected $table = 'usuarios';

    // Columnas reales (las tuyas)
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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',   // hashea automáticamente si asignas texto plano
            'rol' => RolUsuario::class,     // enum (si lo estás usando)
            'activo' => 'boolean',
        ];
    }

    // -------------------------
    // Auth: contraseña en password_hash
    // -------------------------
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    // Permite hacer $user->password = '1234' (y se guarda en password_hash)
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password_hash'] = $value;
    }

    // -------------------------
    // Compatibilidad Laravel/Breeze
    // -------------------------

    // name <-> nombre
    public function getNameAttribute(): ?string
    {
        return $this->nombre;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nombre'] = $value;
    }

    // role <-> rol
    public function getRoleAttribute(): mixed
    {
        return $this->rol;
    }

    public function setRoleAttribute(mixed $value): void
    {
        $this->attributes['rol'] = $value instanceof RolUsuario ? $value->value : $value;
    }

    // is_active <-> activo
    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->activo;
    }

    public function setIsActiveAttribute(bool $value): void
    {
        $this->attributes['activo'] = $value;
    }

    // -------------------------
    // Helpers en español (para UI)
    // -------------------------
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellidos ?? ''));
    }

    // alias opcional en inglés por si alguna parte del panel lo usa
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
}