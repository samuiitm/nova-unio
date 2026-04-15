<?php

namespace Tests\Unit;

use App\Services\CalculadorVigenciaSeguroService;
use DomainException;
use PHPUnit\Framework\TestCase;

class CalculadorVigenciaSeguroServiceTest extends TestCase
{
    public function test_calcula_vigencia_anual_del_seguro_consell(): void
    {
        // Arrange
        $service = new CalculadorVigenciaSeguroService();

        // Act
        $resultado = $service->calcularVigencia('consell_esportiu', '2026-04-15');

        // Assert
        $this->assertSame('consell_esportiu', $resultado['tipo']);
        $this->assertSame('Seguro Consell Esportiu', $resultado['nombre']);
        $this->assertSame(45.0, $resultado['importe']);
        $this->assertSame('2026-04-15', $resultado['inicio']->toDateString());
        $this->assertSame('2027-04-14', $resultado['fin']->toDateString());
    }

    public function test_calcula_vigencia_anual_del_seguro_federacion(): void
    {
        // Arrange
        $service = new CalculadorVigenciaSeguroService();

        // Act
        $resultado = $service->calcularVigencia('federacio_catalana_lucha', '2026-09-01');

        // Assert
        $this->assertSame('federacio_catalana_lucha', $resultado['tipo']);
        $this->assertSame('Seguro Federación Catalana Lucha', $resultado['nombre']);
        $this->assertSame(75.0, $resultado['importe']);
        $this->assertSame('2026-09-01', $resultado['inicio']->toDateString());
        $this->assertSame('2027-08-31', $resultado['fin']->toDateString());
    }

    public function test_lanza_excepcion_si_el_tipo_de_seguro_no_es_valido(): void
    {
        // Arrange
        $service = new CalculadorVigenciaSeguroService();

        $this->expectException(DomainException::class);

        // Act
        $service->calcularVigencia('seguro_inventado', '2026-04-15');

        // Assert
        // La aserción se realiza mediante expectException.
    }
}