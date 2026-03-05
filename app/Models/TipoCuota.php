<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCuota extends Model
{
    protected $table = 'tipos_cuota';

    protected $fillable = [
        'nombre',
        'importe',
        'duracion_meses',
        'activo',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
        'duracion_meses' => 'integer',
        'activo' => 'boolean',
    ];

    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'tipo_cuota_id');
    }
}