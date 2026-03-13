<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCuota extends Model
{
    protected $table = 'tipos_cuota';

    protected $fillable = [
        'nombre',
        'importe',
        'tipo_vigencia',
        'duracion_meses',
        'venta_inicio_mes',
        'venta_fin_mes',
        'activo',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
        'duracion_meses' => 'integer',
        'venta_inicio_mes' => 'integer',
        'venta_fin_mes' => 'integer',
        'activo' => 'boolean',
    ];

    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'tipo_cuota_id');
    }

    public function esTemporada(): bool
    {
        return $this->tipo_vigencia === 'temporada';
    }

    public function esIndefinida(): bool
    {
        return $this->tipo_vigencia === 'indefinida';
    }

    public function esPorMeses(): bool
    {
        return !$this->esTemporada() && !$this->esIndefinida();
    }
}