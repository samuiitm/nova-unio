<?php

namespace Tests\Feature;

use App\Models\Clase;
use App\Models\Grupo;
use App\Models\GrupoProgramacion;
use App\Services\GeneradorClasesService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneradorClasesServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_clases_para_un_grupo_segun_su_programacion(): void
    {
        // Arrange
        $grupo = Grupo::create([
            'nombre' => 'MMA Junior',
            'activo' => 1,
            'color' => '#7C5CFF',
        ]);

        GrupoProgramacion::create([
            'grupo_id' => $grupo->id,
            'dia_semana' => 3, // miércoles
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
            'vigente_desde' => '2026-04-01',
            'vigente_hasta' => null,
        ]);

        $service = new GeneradorClasesService();

        // Act
        $resultado = $service->generarParaGrupoTrasCambio(
            $grupo,
            Carbon::parse('2026-04-15')
        );

        $clasesDelGrupo = Clase::query()
            ->where('grupo_id', $grupo->id)
            ->orderBy('fecha')
            ->get();

        // Assert
        $this->assertSame(2, $resultado['creadas']);
        $this->assertCount(2, $clasesDelGrupo);

        $this->assertDatabaseHas('clases', [
            'grupo_id' => $grupo->id,
            'fecha' => '2026-04-15',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
        ]);

        $this->assertDatabaseHas('clases', [
            'grupo_id' => $grupo->id,
            'fecha' => '2026-04-22',
            'hora_inicio' => '18:00:00',
            'hora_fin' => '19:00:00',
        ]);
    }

    public function test_no_duplica_clases_si_se_ejecuta_dos_veces(): void
    {
        // Arrange
        $grupo = Grupo::create([
            'nombre' => 'Sambo',
            'activo' => 1,
            'color' => '#7C5CFF',
        ]);

        GrupoProgramacion::create([
            'grupo_id' => $grupo->id,
            'dia_semana' => 1, // lunes
            'hora_inicio' => '17:00:00',
            'hora_fin' => '18:00:00',
            'vigente_desde' => '2026-04-01',
            'vigente_hasta' => null,
        ]);

        $service = new GeneradorClasesService();

        // Act
        $primerResultado = $service->generarParaGrupoTrasCambio(
            $grupo,
            Carbon::parse('2026-04-13')
        );

        $segundoResultado = $service->generarParaGrupoTrasCambio(
            $grupo,
            Carbon::parse('2026-04-13')
        );

        $clasesDelGrupo = Clase::query()
            ->where('grupo_id', $grupo->id)
            ->orderBy('fecha')
            ->get();

        // Assert
        $this->assertSame(2, $primerResultado['creadas']);
        $this->assertSame(0, $segundoResultado['creadas']);
        $this->assertCount(2, $clasesDelGrupo);

        $this->assertDatabaseHas('clases', [
            'grupo_id' => $grupo->id,
            'fecha' => '2026-04-13',
            'hora_inicio' => '17:00:00',
            'hora_fin' => '18:00:00',
        ]);

        $this->assertDatabaseHas('clases', [
            'grupo_id' => $grupo->id,
            'fecha' => '2026-04-20',
            'hora_inicio' => '17:00:00',
            'hora_fin' => '18:00:00',
        ]);
    }
}