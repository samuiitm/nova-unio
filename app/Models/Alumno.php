<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';

    protected $fillable = [
        'nombre',
        'apellidos',
        'catsalut',
        'fecha_nacimiento',
        'lugar_nacimiento',
        'dni',
        'direccion',
        'cp',
        'poblacion',
        'telefono',
        'email',
        'activo',
        'fecha_baja',
        'fecha_inicio_actividad',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_baja' => 'date',
        'fecha_inicio_actividad' => 'date',
        'activo' => 'boolean',
    ];

    public function grupos()
    {
        return $this->belongsToMany(\App\Models\Grupo::class, 'alumno_grupo', 'alumno_id', 'grupo_id')
            ->withPivot(['fecha_alta', 'fecha_baja'])
            ->withTimestamps();
    }

    public function gruposActivos()
    {
        return $this->grupos()->wherePivotNull('fecha_baja');
    }

    public function cuotas()
    {
        return $this->hasMany(\App\Models\Cuota::class);
    }

    public function pagos()
    {
        return $this->hasMany(\App\Models\Pago::class);
    }

    public function ultimaCuota()
    {
        return $this->hasOne(\App\Models\Cuota::class)->latestOfMany('fecha_fin');
    }
}