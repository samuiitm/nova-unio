<?php

namespace App\Services;

use Carbon\Carbon;
use DomainException;

class CalculadorVigenciaSeguroService
{
    public function tiposDisponibles(): array
    {
        return [
            'consell_esportiu' => [
                'nombre' => 'Seguro Consell Esportiu',
                'importe' => 45.00,
            ],
            'federacio_catalana_lucha' => [
                'nombre' => 'Seguro Federación Catalana Lucha',
                'importe' => 75.00,
            ],
        ];
    }

    public function datosTipo(string $tipo): array
    {
        $tipos = $this->tiposDisponibles();

        if (!array_key_exists($tipo, $tipos)) {
            throw new DomainException('El tipo de seguro no es válido.');
        }

        return $tipos[$tipo];
    }

    public function calcularVigencia(string $tipo, Carbon|string $fechaPago): array
    {
        $datos = $this->datosTipo($tipo);

        $fechaPago = $fechaPago instanceof Carbon
            ? $fechaPago->copy()->startOfDay()
            : Carbon::parse($fechaPago)->startOfDay();

        return [
            'tipo' => $tipo,
            'nombre' => $datos['nombre'],
            'importe' => (float) $datos['importe'],
            'inicio' => $fechaPago->copy(),
            'fin' => $fechaPago->copy()->addYear()->subDay(),
        ];
    }
}