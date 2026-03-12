<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function programaciones()
    {
        return $this->hasMany(GrupoProgramacion::class);
    }

    public function alumnos(): BelongsToMany
    {
        return $this->belongsToMany(Alumno::class, 'alumno_grupo', 'grupo_id', 'alumno_id')
            ->withPivot(['fecha_alta', 'fecha_baja'])
            ->withTimestamps();
    }

    public function alumnosActivos(): BelongsToMany
    {
        return $this->alumnos()->wherePivotNull('fecha_baja');
    }

    public function clases()
    {
        return $this->hasMany(Clase::class);
    }

    public function getColorHexAttribute(): string
    {
        return self::normalizarColor($this->color);
    }

    public function getColorRgbAttribute(): string
    {
        [$r, $g, $b] = self::rgbDesdeHex($this->color_hex);

        return $r . ' ' . $g . ' ' . $b;
    }

    private static function normalizarColor(?string $color): string
    {
        $color = strtoupper(trim((string) $color));

        if (!preg_match('/^#[A-F0-9]{6}$/', $color)) {
            return '#7C5CFF';
        }

        return $color;
    }

    private static function rgbDesdeHex(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}