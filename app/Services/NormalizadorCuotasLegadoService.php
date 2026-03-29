<?php

namespace App\Services;

use App\Models\Cuota;
use App\Models\Pago;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NormalizadorCuotasLegadoService
{
    public function analizar(): array
    {
        $cuotaIds = DB::table('pagos')
            ->select('cuota_id')
            ->whereNotNull('cuota_id')
            ->groupBy('cuota_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('cuota_id');

        $detalle = [];

        foreach ($cuotaIds as $cuotaId) {
            $cuota = Cuota::find($cuotaId);

            if (!$cuota) {
                continue;
            }

            $pagos = Pago::query()
                ->where('cuota_id', $cuotaId)
                ->orderBy('fecha_pago')
                ->orderBy('id')
                ->get();

            $detalle[] = [
                'cuota_id' => $cuota->id,
                'alumno_id' => $cuota->alumno_id,
                'total_pagos' => $pagos->count(),
                'pagos' => $pagos->map(fn (Pago $p) => [
                    'pago_id' => $p->id,
                    'fecha_pago' => optional($p->fecha_pago)->toDateString(),
                    'importe' => (float) $p->importe,
                    'notas' => $p->notas,
                    'vigencia_inicio' => $p->vigencia_inicio,
                    'vigencia_fin' => $p->vigencia_fin,
                ])->all(),
            ];
        }

        return [
            'total_cuotas_contaminadas' => count($detalle),
            'detalle' => $detalle,
        ];
    }

    public function ejecutar(): array
    {
        $cuotaIds = DB::table('pagos')
            ->select('cuota_id')
            ->whereNotNull('cuota_id')
            ->groupBy('cuota_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('cuota_id');

        $procesadas = 0;
        $pagos_reasignados = 0;

        foreach ($cuotaIds as $cuotaId) {
            DB::transaction(function () use ($cuotaId, &$procesadas, &$pagos_reasignados) {
                $cuota = Cuota::lockForUpdate()->find($cuotaId);

                if (!$cuota) {
                    return;
                }

                $pagos = Pago::query()
                    ->where('cuota_id', $cuotaId)
                    ->orderBy('fecha_pago')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($pagos->count() <= 1) {
                    return;
                }

                $ultimoPago = $pagos
                    ->sortByDesc(fn (Pago $p) => optional($p->fecha_pago)->toDateString() . '-' . str_pad((string) $p->id, 10, '0', STR_PAD_LEFT))
                    ->first();

                $pagosAnteriores = $pagos->filter(fn (Pago $p) => $p->id !== $ultimoPago->id)->values();

                foreach ($pagosAnteriores as $pago) {
                    $nuevaCuota = Cuota::create([
                        'alumno_id' => $pago->alumno_id ?: $cuota->alumno_id,
                        'tipo_cuota_id' => $pago->tipo_cuota_id ?: $cuota->tipo_cuota_id,
                        'importe' => $pago->importe,
                        'estado' => 'pagada',
                        'fecha_inicio' => $pago->vigencia_inicio,
                        'fecha_fin' => $pago->vigencia_fin,
                        'created_at' => $pago->created_at,
                        'updated_at' => $pago->updated_at,
                    ]);

                    $pago->update([
                        'cuota_id' => $nuevaCuota->id,
                    ]);

                    $pagos_reasignados++;
                }

                $cuota->update([
                    'alumno_id' => $ultimoPago->alumno_id ?: $cuota->alumno_id,
                    'tipo_cuota_id' => $ultimoPago->tipo_cuota_id ?: $cuota->tipo_cuota_id,
                    'importe' => $ultimoPago->importe,
                    'estado' => 'pagada',
                    'fecha_inicio' => $ultimoPago->vigencia_inicio,
                    'fecha_fin' => $ultimoPago->vigencia_fin,
                    'updated_at' => now(),
                ]);

                $procesadas++;
            });
        }

        return [
            'cuotas_procesadas' => $procesadas,
            'pagos_reasignados' => $pagos_reasignados,
        ];
    }
}