<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $table = 'clases';

    protected $fillable = [
        'grupo_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'asistencia_cerrada'
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function inicioCarbon(): Carbon
    {
        $fecha = $this->fecha instanceof CarbonInterface
            ? $this->fecha->toDateString()
            : (string) $this->fecha;

        $horaInicio = $this->hora_inicio instanceof CarbonInterface
            ? $this->hora_inicio->format('H:i:s')
            : (string) $this->hora_inicio;

        return Carbon::parse(trim($fecha . ' ' . $horaInicio), config('app.timezone'));
    }

    public function limiteEdicionAsistenciaCarbon(): Carbon
    {
        return $this->inicioCarbon()->copy()->addHours(48);
    }

    public function estadoVisualAsistencia(?int $totalAsistencias = null, ?CarbonInterface $ahora = null): array
    {
        $totalAsistencias = max(0, (int) ($totalAsistencias ?? 0));
        $ahora = $ahora ? Carbon::instance($ahora) : now();

        $inicio = $this->inicioCarbon();
        $limite = $this->limiteEdicionAsistenciaCarbon();

        $esCancelada = ($this->estado ?? null) === 'cancelada';
        $cerradaManual = (bool) ($this->asistencia_cerrada ?? false);
        $fueraDeVentana = $ahora->gt($limite);

        if ($esCancelada) {
            return [
                'clave' => 'cancelada',
                'bloqueada' => true,
                'inicio' => $inicio,
                'limite' => $limite,
            ];
        }

        if ($cerradaManual) {
            return [
                'clave' => 'cerrada',
                'bloqueada' => true,
                'inicio' => $inicio,
                'limite' => $limite,
            ];
        }

        if ($fueraDeVentana && $totalAsistencias === 0) {
            return [
                'clave' => 'sin_lista_bloqueada',
                'bloqueada' => true,
                'inicio' => $inicio,
                'limite' => $limite,
            ];
        }

        if ($fueraDeVentana) {
            return [
                'clave' => 'cerrada',
                'bloqueada' => true,
                'inicio' => $inicio,
                'limite' => $limite,
            ];
        }

        if ($totalAsistencias > 0) {
            return [
                'clave' => 'pasada',
                'bloqueada' => false,
                'inicio' => $inicio,
                'limite' => $limite,
            ];
        }

        if ($ahora->gte($inicio)) {
            return [
                'clave' => 'sin_lista',
                'bloqueada' => false,
                'inicio' => $inicio,
                'limite' => $limite,
            ];
        }

        return [
            'clave' => 'abierta',
            'bloqueada' => false,
            'inicio' => $inicio,
            'limite' => $limite,
        ];
    }
}