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
}