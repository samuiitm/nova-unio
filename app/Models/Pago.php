<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'cuota_id',
        'alumno_id',
        'fecha_pago',
        'importe',
        'metodo',
        'notas',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'importe' => 'decimal:2',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
}