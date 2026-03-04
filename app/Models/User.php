<?php

namespace App\Models;

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
        'last_name',
        'email',
        'phone',
        'avatar_path',
        'password',
        'role',
        'is_active',
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

    // Helpers en español (para UI / panel)
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->name ?? '') . ' ' . ($this->last_name ?? ''));
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