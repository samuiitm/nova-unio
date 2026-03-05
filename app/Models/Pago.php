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

        'tipo_cuota_id',
        'tipo_cuota_nombre',
        'vigencia_inicio',
        'vigencia_fin',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'vigencia_inicio' => 'date',
        'vigencia_fin' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }

    public function tipoCuota()
    {
        return $this->belongsTo(TipoCuota::class, 'tipo_cuota_id');
    }
}