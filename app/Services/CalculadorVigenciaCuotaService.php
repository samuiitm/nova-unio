<?php

namespace App\Services;

use App\Models\TipoCuota;
use Carbon\Carbon;
use DomainException;

class CalculadorVigenciaCuotaService
{
    private const TEMPORADA_INICIO_MES = 9;
    private const TEMPORADA_INICIO_DIA = 1;
    private const TEMPORADA_FIN_MES = 6;
    private const TEMPORADA_FIN_DIA = 30;

    public function asegurarQueSePuedeAsignar(TipoCuota $tipo, Carbon|string|null $fecha = null): void
    {
        if (!$tipo->esTemporada()) {
            return;
        }

        $fecha = $fecha instanceof Carbon
            ? $fecha->copy()
            : Carbon::parse($fecha ?: now()->toDateString());

        if (!$this->estaEnVentanaDeVenta($tipo, $fecha)) {
            throw new DomainException($this->mensajeFueraDeVentana($tipo));
        }
    }

    public function calcularParaPago(TipoCuota $tipo, Carbon|string $fechaPago): array
    {
        $fechaPago = $fechaPago instanceof Carbon
            ? $fechaPago->copy()
            : Carbon::parse($fechaPago);

        if ($tipo->esPorMeses()) {
            return [
                'inicio' => $fechaPago->copy(),
                'fin' => $fechaPago->copy()->addMonthsNoOverflow(max(1, (int) $tipo->duracion_meses)),
            ];
        }

        $this->asegurarQueSePuedeAsignar($tipo, $fechaPago);

        [$inicioTemporada, $finTemporada] = $this->rangoTemporadaSegunFecha($fechaPago);

        // Si se cobra antes de septiembre, la vigencia empieza cuando arranca la temporada.
        $inicioVigencia = $fechaPago->lt($inicioTemporada)
            ? $inicioTemporada->copy()
            : $fechaPago->copy();

        return [
            'inicio' => $inicioVigencia,
            'fin' => $finTemporada->copy(),
        ];
    }

    public function estaEnVentanaDeVenta(TipoCuota $tipo, Carbon|string $fecha): bool
    {
        if (!$tipo->esTemporada()) {
            return true;
        }

        $fecha = $fecha instanceof Carbon ? $fecha->copy() : Carbon::parse($fecha);

        $inicio = (int) ($tipo->venta_inicio_mes ?: 8);
        $fin = (int) ($tipo->venta_fin_mes ?: 12);
        $mes = (int) $fecha->month;

        if ($inicio <= $fin) {
            return $mes >= $inicio && $mes <= $fin;
        }

        return $mes >= $inicio || $mes <= $fin;
    }

    public function mensajeFueraDeVentana(TipoCuota $tipo): string
    {
        return 'La cuota de temporada solo se puede vender ' . $this->descripcionVentanaDeVenta($tipo) . '.';
    }

    public function descripcionVentanaDeVenta(TipoCuota $tipo): string
    {
        $inicio = (int) ($tipo->venta_inicio_mes ?: 8);
        $fin = (int) ($tipo->venta_fin_mes ?: 12);

        return 'de ' . $this->nombreMes($inicio) . ' a ' . $this->nombreMes($fin);
    }

    private function rangoTemporadaSegunFecha(Carbon $fecha): array
    {
        if ($fecha->month >= self::TEMPORADA_INICIO_MES) {
            $inicio = Carbon::create($fecha->year, self::TEMPORADA_INICIO_MES, self::TEMPORADA_INICIO_DIA);
            $fin = Carbon::create($fecha->year + 1, self::TEMPORADA_FIN_MES, self::TEMPORADA_FIN_DIA);

            return [$inicio, $fin];
        }

        if ($fecha->month <= self::TEMPORADA_FIN_MES) {
            $inicio = Carbon::create($fecha->year - 1, self::TEMPORADA_INICIO_MES, self::TEMPORADA_INICIO_DIA);
            $fin = Carbon::create($fecha->year, self::TEMPORADA_FIN_MES, self::TEMPORADA_FIN_DIA);

            return [$inicio, $fin];
        }

        // Julio y agosto apuntan a la temporada que va a arrancar en septiembre.
        $inicio = Carbon::create($fecha->year, self::TEMPORADA_INICIO_MES, self::TEMPORADA_INICIO_DIA);
        $fin = Carbon::create($fecha->year + 1, self::TEMPORADA_FIN_MES, self::TEMPORADA_FIN_DIA);

        return [$inicio, $fin];
    }

    private function nombreMes(int $mes): string
    {
        return [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ][$mes] ?? 'mes';
    }
}