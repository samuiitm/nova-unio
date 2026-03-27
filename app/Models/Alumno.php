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
        'tutor_legal_nombre',
        'tutor_legal_dni',
        'tutor_legal_relacion',
        'foto_path',
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

    protected $appends = [
        'foto_url',
    ];

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_path) {
            return route('panel.media.alumnos.foto', $this);
        }

        return \Illuminate\Support\Facades\Vite::asset('resources/img/alumno-default.svg');
    }

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
        return $this->hasMany(\App\Models\Cuota::class, 'alumno_id');
    }

    public function cuotaActual()
    {
        return $this->hasOne(\App\Models\Cuota::class, 'alumno_id')
            ->where('estado', '!=', 'anulada')
            ->latestOfMany();
    }

    public function ultimaCuotaPagada()
    {
        return $this->hasOne(\App\Models\Cuota::class, 'alumno_id')
            ->where('estado', 'pagada')
            ->latestOfMany('fecha_fin');
    }

    public function telefonosContacto()
    {
        return $this->hasMany(\App\Models\AlumnoTelefonoContacto::class, 'alumno_id')
            ->orderBy('orden')
            ->orderBy('id');
    }

    public function seguros()
    {
        return $this->hasMany(\App\Models\Seguro::class, 'alumno_id');
    }
}