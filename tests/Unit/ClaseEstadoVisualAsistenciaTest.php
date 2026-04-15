<?php

namespace Tests\Unit;

use App\Models\Clase;
use Carbon\Carbon;
use Tests\TestCase;

class ClaseEstadoVisualAsistenciaTest extends TestCase
{
    public function test_una_clase_futura_sin_lista_aparece_como_abierta(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-15 10:00:00'));

        $clase = new Clase([
            'fecha' => '2026-04-16',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'programada',
            'asistencia_cerrada' => 0,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(0);

        // Assert
        $this->assertSame('abierta', $estado['clave']);
        $this->assertFalse($estado['bloqueada']);
    }

    public function test_una_clase_pasada_sin_lista_dentro_de_48_horas_aparece_como_sin_lista(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-16 17:00:00'));

        $clase = new Clase([
            'fecha' => '2026-04-15',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'programada',
            'asistencia_cerrada' => 0,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(0);

        // Assert
        $this->assertSame('sin_lista', $estado['clave']);
        $this->assertFalse($estado['bloqueada']);
    }

    public function test_una_clase_sin_lista_fuera_de_48_horas_aparece_como_sin_lista_bloqueada(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-18 18:01:00'));

        $clase = new Clase([
            'fecha' => '2026-04-16',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'programada',
            'asistencia_cerrada' => 0,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(0);

        // Assert
        $this->assertSame('sin_lista_bloqueada', $estado['clave']);
        $this->assertTrue($estado['bloqueada']);
    }

    public function test_una_clase_con_lista_dentro_de_48_horas_aparece_como_pasada(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-16 17:00:00'));

        $clase = new Clase([
            'fecha' => '2026-04-15',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'programada',
            'asistencia_cerrada' => 0,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(5);

        // Assert
        $this->assertSame('pasada', $estado['clave']);
        $this->assertFalse($estado['bloqueada']);
    }

    public function test_una_clase_con_lista_fuera_de_48_horas_aparece_como_cerrada(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-18 18:01:00'));

        $clase = new Clase([
            'fecha' => '2026-04-16',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'programada',
            'asistencia_cerrada' => 0,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(4);

        // Assert
        $this->assertSame('cerrada', $estado['clave']);
        $this->assertTrue($estado['bloqueada']);
    }

    public function test_una_clase_cancelada_aparece_como_cancelada_y_bloqueada(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-15 12:00:00'));

        $clase = new Clase([
            'fecha' => '2026-04-15',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'cancelada',
            'asistencia_cerrada' => 0,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(0);

        // Assert
        $this->assertSame('cancelada', $estado['clave']);
        $this->assertTrue($estado['bloqueada']);
    }

    public function test_una_clase_con_cierre_manual_aparece_como_cerrada_y_bloqueada(): void
    {
        // Arrange
        Carbon::setTestNow(Carbon::parse('2026-04-15 12:00:00'));

        $clase = new Clase([
            'fecha' => '2026-04-15',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'estado' => 'programada',
            'asistencia_cerrada' => 1,
        ]);

        // Act
        $estado = $clase->estadoVisualAsistencia(0);

        // Assert
        $this->assertSame('cerrada', $estado['clave']);
        $this->assertTrue($estado['bloqueada']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}