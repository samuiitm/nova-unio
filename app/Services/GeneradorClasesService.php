<?php

namespace App\Services;

use App\Models\Clase;
use App\Models\Grupo;
use App\Models\GrupoProgramacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GeneradorClasesService
{
    public function generarParaGrupoTrasCambio(Grupo $grupo, ?Carbon $fechaBase = null): array
    {
        $fechaBase = ($fechaBase ?: now())->copy()->startOfDay();

        $inicio = $fechaBase->copy();
        $fin = $fechaBase->copy()->endOfWeek()->addWeek()->endOfWeek();

        return $this->generarPeriodo($inicio, $fin, collect([$grupo]));
    }

    public function generarAutomaticoDomingo(?Carbon $fechaBase = null): array
    {
        $fechaBase = ($fechaBase ?: now())->copy()->startOfDay();

        $inicio = $fechaBase->copy()->next(Carbon::MONDAY)->startOfDay();
        $fin = $inicio->copy()->addWeek()->endOfWeek();

        $grupos = Grupo::query()
            ->where('activo', 1)
            ->with([
                'programaciones' => function ($q) use ($inicio, $fin) {
                    $q->whereDate('vigente_desde', '<=', $fin->toDateString())
                        ->where(function ($w) use ($inicio) {
                            $w->whereNull('vigente_hasta')
                                ->orWhereDate('vigente_hasta', '>=', $inicio->toDateString());
                        })
                        ->orderBy('dia_semana')
                        ->orderBy('hora_inicio');
                },
            ])
            ->get();

        return $this->generarPeriodo($inicio, $fin, $grupos);
    }

    private function generarPeriodo(Carbon $inicio, Carbon $fin, Collection $grupos): array
    {
        $creadas = 0;
        $procesadas = 0;

        foreach ($grupos as $grupo) {
            $programaciones = $grupo->relationLoaded('programaciones')
                ? $grupo->programaciones
                : $grupo->programaciones()
                    ->whereDate('vigente_desde', '<=', $fin->toDateString())
                    ->where(function ($w) use ($inicio) {
                        $w->whereNull('vigente_hasta')
                            ->orWhereDate('vigente_hasta', '>=', $inicio->toDateString());
                    })
                    ->orderBy('dia_semana')
                    ->orderBy('hora_inicio')
                    ->get();

            foreach ($programaciones as $programacion) {
                $fecha = $inicio->copy();

                while ($fecha->lte($fin)) {
                    if (
                        $fecha->dayOfWeekIso === (int) $programacion->dia_semana
                        && $this->programacionVigenteEnFecha($programacion, $fecha)
                    ) {
                        $procesadas++;

                        $clase = Clase::firstOrCreate(
                            [
                                'grupo_id' => $grupo->id,
                                'fecha' => $fecha->toDateString(),
                                'hora_inicio' => $programacion->hora_inicio,
                                'hora_fin' => $programacion->hora_fin,
                            ],
                            [
                                'estado' => 'programada',
                                'asistencia_cerrada' => 0,
                            ]
                        );

                        if ($clase->wasRecentlyCreated) {
                            $creadas++;
                        }
                    }

                    $fecha->addDay();
                }
            }
        }

        return [
            'inicio' => $inicio,
            'fin' => $fin,
            'procesadas' => $procesadas,
            'creadas' => $creadas,
        ];
    }

    private function programacionVigenteEnFecha(GrupoProgramacion $programacion, Carbon $fecha): bool
    {
        if ($programacion->vigente_desde && $fecha->lt($programacion->vigente_desde->copy()->startOfDay())) {
            return false;
        }

        if ($programacion->vigente_hasta && $fecha->gt($programacion->vigente_hasta->copy()->endOfDay())) {
            return false;
        }

        return true;
    }
}