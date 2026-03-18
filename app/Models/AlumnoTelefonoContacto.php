<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoTelefonoContacto extends Model
{
    protected $table = 'alumno_telefonos_contacto';

    protected $fillable = [
        'alumno_id',
        'contacto',
        'telefono',
        'orden',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}