<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Seguro extends Model
{
    protected $table = 'seguros';

    protected $fillable = [
        'alumno_id',
        'tipo',
        'importe',
        'estado',
        'fecha_pago',
        'fecha_inicio',
        'fecha_fin',
        'metodo',
        'notas',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
        'fecha_pago' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function scopeVigentes(Builder $query, ?string $fecha = null): Builder
    {
        return $query
            ->where('estado', 'pagado')
            ->whereDate('fecha_fin', '>=', $fecha ?: now()->toDateString());
    }

    public function scopeVencidos(Builder $query, ?string $fecha = null): Builder
    {
        return $query
            ->where('estado', 'pagado')
            ->whereDate('fecha_fin', '<', $fecha ?: now()->toDateString());
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', 'pendiente');
    }

    public function getTipoNombreAttribute(): string
    {
        return match ($this->tipo) {
            'consell_esportiu' => 'Seguro Consell Esportiu',
            'federacio_catalana_lucha' => 'Seguro Federación Catalana Lucha',
            default => 'Seguro deportivo',
        };
    }

    public function getEstadoVisualAttribute(): string
    {
        if ($this->estado === 'pendiente') {
            return 'Pendiente';
        }

        if ($this->fecha_fin && $this->fecha_fin->toDateString() < now()->toDateString()) {
            return 'Vencido';
        }

        return 'Vigente';
    }
}