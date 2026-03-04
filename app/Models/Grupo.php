<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function programaciones(): HasMany
    {
        return $this->hasMany(GrupoProgramacion::class, 'grupo_id');
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
}