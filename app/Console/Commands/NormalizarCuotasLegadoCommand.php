<?php

namespace App\Console\Commands;

use App\Services\NormalizadorCuotasLegadoService;
use Illuminate\Console\Command;

class NormalizarCuotasLegadoCommand extends Command
{
    protected $signature = 'cuotas:normalizar-legado {--apply : Ejecuta los cambios de verdad}';
    protected $description = 'Analiza o normaliza cuotas antiguas contaminadas con varios pagos';

    public function handle(NormalizadorCuotasLegadoService $service): int
    {
        if (!$this->option('apply')) {
            $resultado = $service->analizar();

            $this->info('Cuotas contaminadas: ' . $resultado['total_cuotas_contaminadas']);

            foreach ($resultado['detalle'] as $item) {
                $this->line("Cuota #{$item['cuota_id']} · Alumno #{$item['alumno_id']} · Pagos: {$item['total_pagos']}");

                foreach ($item['pagos'] as $pago) {
                    $this->line(
                        "  - Pago #{$pago['pago_id']} | {$pago['fecha_pago']} | {$pago['importe']} € | {$pago['notas']}"
                    );
                }
            }

            $this->comment('Ejecuta con --apply cuando hayas revisado el análisis.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Esto va a separar pagos históricos en cuotas nuevas. ¿Continuar?')) {
            return self::INVALID;
        }

        $resultado = $service->ejecutar();

        $this->info('Cuotas procesadas: ' . $resultado['cuotas_procesadas']);
        $this->info('Pagos reasignados: ' . $resultado['pagos_reasignados']);

        return self::SUCCESS;
    }
}