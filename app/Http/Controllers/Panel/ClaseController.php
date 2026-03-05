<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Clase;
use Illuminate\Http\Request;

class ClaseController extends Controller
{
    public function show(Clase $clase)
    {
        // alumnos del grupo activos en la fecha de la clase (histórico correcto)
        $alumnos = $clase->grupo
            ->alumnos()
            ->wherePivot('fecha_alta', '<=', $clase->fecha)
            ->where(function ($q) use ($clase) {
                $q->whereNull('alumno_grupo.fecha_baja')
                  ->orWhere('alumno_grupo.fecha_baja', '>=', $clase->fecha);
            })
            ->orderBy('alumnos.apellidos')
            ->orderBy('alumnos.nombre')
            ->get();

        $asistencias = $clase->asistencias()
            ->pluck('estado', 'alumno_id');

        return view('panel.clases.show', compact('clase', 'alumnos', 'asistencias'));
    }

    public function guardarAsistencia(Request $request, Clase $clase)
    {
        if ($clase->asistencia_cerrada) {
            return back()->with('ok', 'La asistencia está cerrada y no se puede modificar.');
        }

        $data = $request->validate([
            'asistencias' => ['required', 'array'],
            'asistencias.*' => ['required', 'in:presente,ausente'],
        ]);

        // ids permitidos (alumnos del grupo activos en esa fecha)
        $idsPermitidos = $clase->grupo
            ->alumnos()
            ->wherePivot('fecha_alta', '<=', $clase->fecha)
            ->where(function ($q) use ($clase) {
                $q->whereNull('alumno_grupo.fecha_baja')
                  ->orWhere('alumno_grupo.fecha_baja', '>=', $clase->fecha);
            })
            ->pluck('alumnos.id')
            ->map(fn ($id) => (int) $id)
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