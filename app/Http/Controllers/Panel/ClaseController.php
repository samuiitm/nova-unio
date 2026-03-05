<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Clase;
use Illuminate\Http\Request;

class ClaseController extends Controller
{
    /**
     * Mostrar clase para pasar lista
     */
    public function show(Clase $clase)
    {
        // alumnos activos del grupo
        $alumnos = $clase->grupo
            ->alumnos()
            ->wherePivotNull('fecha_baja')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        // asistencias ya guardadas
        $asistencias = $clase->asistencias()
            ->pluck('estado', 'alumno_id');

        return view('panel.clases.show', [
            'clase' => $clase,
            'alumnos' => $alumnos,
            'asistencias' => $asistencias
        ]);
    }

    /**
     * Guardar asistencias
     */
    public function guardarAsistencia(Request $request, Clase $clase)
    {
        if ($clase->asistencia_cerrada) {
            return back()->with('ok', 'La asistencia está cerrada y no se puede modificar.');
        }

        $data = $request->validate([
            'asistencias' => ['required', 'array'],
            'asistencias.*' => ['required', 'in:presente,ausente'],
        ]);

        // solo alumnos activos del grupo (para evitar ids raros)
        $idsPermitidos = $clase->grupo
            ->alumnos()
            ->wherePivotNull('fecha_baja')
            ->pluck('alumnos.id')
            ->map(fn($id) => (int) $id)
            ->all();

        foreach ($data['asistencias'] as $alumno_id => $estado) {

            $alumno_id = (int) $alumno_id;

            if (!in_array($alumno_id, $idsPermitidos, true)) {
                continue;
            }

            $clase->asistencias()->updateOrCreate(
                ['alumno_id' => $alumno_id],
                ['estado' => $estado]
            );
        }

        return back()->with('ok', 'Asistencia guardada.');
    }
}