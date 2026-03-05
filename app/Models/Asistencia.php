<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'clase_id',
        'alumno_id',
        'estado',
    ];

    public function clase()
    {
        return $this->belongsTo(Clase::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}