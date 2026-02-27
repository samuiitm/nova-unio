<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preinscripcion extends Model
{
    protected $table = 'preinscripciones';

    protected $fillable = [
        'nombre','apellidos','email','telefono','edad',
        'modalidad','nivel','objetivo','mensaje',
        'estado','alumno_id',
    ];
}