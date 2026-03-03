<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\RolUsuario;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RolUsuario::class,
            'is_active' => 'boolean',
        ];
    }

    public function getNombreAttribute(): ?string
    {
        return $this->name;
    }

    public function setNombreAttribute(?string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getPasswordHashAttribute(): ?string
    {
        return $this->password; // hash
    }

    public function setPasswordHashAttribute(?string $value): void
    {
        // Si asignas aquí, ya debe venir hasheado.
        $this->attributes['password'] = $value;
    }

    public function getRolAttribute(): mixed
    {
        return $this->role; // RolUsuario (por el cast) o string
    }

    public function setRolAttribute(mixed $value): void
    {
        $this->attributes['role'] = $value instanceof RolUsuario ? $value->value : $value;
    }

    public function getActivoAttribute(): bool
    {
        return (bool) $this->is_active;
    }

    public function setActivoAttribute(bool $value): void
    {
        $this->attributes['is_active'] = $value;
    }
}
