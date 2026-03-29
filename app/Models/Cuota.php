<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'alumno_id',
        'tipo_cuota_id',
        'fecha_inicio',
        'fecha_fin',
        'importe',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function tipoCuota()
    {
        return $this->belongsTo(TipoCuota::class, 'tipo_cuota_id');
    }

    public function pagos()
    {
        return $this->hasMany(\App\Models\Pago::class, 'cuota_id');
    }

    public function pago()
    {
        return $this->hasOne(\App\Models\Pago::class)->latestOfMany('fecha_pago');
    }

    public function ultimoPago()
    {
        return $this->hasOne(Pago::class)->latestOfMany('fecha_pago');
    }
}