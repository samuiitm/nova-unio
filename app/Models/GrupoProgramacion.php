<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrupoProgramacion extends Model
{
    protected $table = 'grupo_programacion';

    protected $fillable = [
        'grupo_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'vigente_desde',
        'vigente_hasta',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'vigente_desde' => 'date',
        'vigente_hasta' => 'date',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}