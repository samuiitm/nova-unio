<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'usuarios';

    /**
     * Campos asignables (columnas en inglés).
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'avatar_path',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RolUsuario::class,
            'is_active' => 'boolean',
        ];
    }

    // -------------------------
    // Helpers / aliases en español
    // -------------------------

    public function getNombreAttribute(): ?string
    {
        return $this->name;
    }

    public function setNombreAttribute(?string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getApellidosAttribute(): ?string
    {
        return $this->last_name;
    }

    public function setApellidosAttribute(?string $value): void
    {
        $this->attributes['last_name'] = $value;
    }

    public function getTelefonoAttribute(): ?string
    {
        return $this->phone;
    }

    public function setTelefonoAttribute(?string $value): void
    {
        $this->attributes['phone'] = $value;
    }

    public function getFotoPerfilAttribute(): ?string
    {
        return $this->avatar_path;
    }

    public function setFotoPerfilAttribute(?string $value): void
    {
        $this->attributes['avatar_path'] = $value;
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getRolAttribute(): mixed
    {
        return $this->role; // enum (cast) o string
    }

    public function getActivoAttribute(): bool
    {
        return (bool) $this->is_active;
    }
}