<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\GrupoProgramacion;
use App\Services\GeneradorClasesService;
use Illuminate\Http\Request;

class GrupoProgramacionController extends Controller
{
    public function store(Request $request, Grupo $grupo, GeneradorClasesService $generador)
    {
        $data = $request->validate([
            'dia_semana' => ['required', 'integer', 'between:1,7'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'vigente_desde' => ['required', 'date'],
            'vigente_hasta' => ['nullable', 'date', 'after_or_equal:vigente_desde'],
        ]);

        $grupo->programaciones()->create($data);

        $generador->generarParaGrupoTrasCambio($grupo);

        return back()->with('ok', 'Horario añadido y clases próximas generadas automáticamente.');
    }

    public function update(Request $request, Grupo $grupo, GrupoProgramacion $programacion, GeneradorClasesService $generador)
    {
        abort_unless((int) $programacion->grupo_id === (int) $grupo->id, 404);

        $data = $request->validate([
            'dia_semana' => ['required', 'integer', 'between:1,7'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'vigente_desde' => ['required', 'date'],
            'vigente_hasta' => ['nullable', 'date', 'after_or_equal:vigente_desde'],
        ]);

        $programacion->update($data);

        $generador->generarParaGrupoTrasCambio($grupo);

        return back()->with('ok', 'Horario actualizado y clases próximas aseguradas.');
    }

    public function destroy(Grupo $grupo, GrupoProgramacion $programacion)
    {
        abort_unless((int) $programacion->grupo_id === (int) $grupo->id, 404);

        $programacion->delete();

        return back()->with('ok', 'Horario eliminado.');
    }
}