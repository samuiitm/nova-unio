<?php

namespace Tests\Unit;

use App\Models\TipoCuota;
use App\Services\CalculadorVigenciaCuotaService;
use DomainException;
use PHPUnit\Framework\TestCase;

class CalculadorVigenciaCuotaServiceTest extends TestCase
{
    public function test_calcula_vigencia_para_cuota_por_meses(): void
    {
        // Arrange
        $tipo = new TipoCuota([
            'nombre' => 'Mensual',
            'tipo_vigencia' => 'meses',
            'duracion_meses' => 1,
        ]);

        $service = new CalculadorVigenciaCuotaService();

        // Act
        $resultado = $service->calcularParaPago($tipo, '2026-04-15');

        // Assert
        $this->assertSame('2026-04-15', $resultado['inicio']->toDateString());
        $this->assertSame('2026-05-15', $resultado['fin']->toDateString());
    }

    public function test_calcula_vigencia_para_cuota_indefinida(): void
    {
        // Arrange
        $tipo = new TipoCuota([
            'nombre' => 'Beca',
            'tipo_vigencia' => 'indefinida',
        ]);

        $service = new CalculadorVigenciaCuotaService();

        // Act
        $resultado = $service->calcularParaPago($tipo, '2026-04-15');

        // Assert
        $this->assertSame('2026-04-15', $resultado['inicio']->toDateString());
        $this->assertNull($resultado['fin']);
    }

    public function test_calcula_vigencia_para_temporada_dentro_de_ventana(): void
    {
        // Arrange
        $tipo = new TipoCuota([
            'nombre' => 'Temporada',
            'tipo_vigencia' => 'temporada',
            'venta_inicio_mes' => 8,
            'venta_fin_mes' => 12,
        ]);

        $service = new CalculadorVigenciaCuotaService();

        // Act
        $resultado = $service->calcularParaPago($tipo, '2026-09-10');

        // Assert
        $this->assertSame('2026-09-10', $resultado['inicio']->toDateString());
        $this->assertSame('2027-06-30', $resultado['fin']->toDateString());
    }

    public function test_temporada_pagada_en_agosto_empieza_en_septiembre(): void
    {
        // Arrange
        $tipo = new TipoCuota([
            'nombre' => 'Temporada',
            'tipo_vigencia' => 'temporada',
            'venta_inicio_mes' => 8,
            'venta_fin_mes' => 12,
        ]);

        $service = new CalculadorVigenciaCuotaService();

        // Act
        $resultado = $service->calcularParaPago($tipo, '2026-08-20');

        // Assert
        $this->assertSame('2026-09-01', $resultado['inicio']->toDateString());
        $this->assertSame('2027-06-30', $resultado['fin']->toDateString());
    }

    public function test_lanza_excepcion_si_la_cuota_de_temporada_se_quiere_vender_fuera_de_ventana(): void
    {
        // Arrange
        $tipo = new TipoCuota([
            'nombre' => 'Temporada',
            'tipo_vigencia' => 'temporada',
            'venta_inicio_mes' => 8,
            'venta_fin_mes' => 12,
        ]);

        $service = new CalculadorVigenciaCuotaService();

        $this->expectException(DomainException::class);

        // Act
        $service->calcularParaPago($tipo, '2026-02-10');

        // Assert
    }
}