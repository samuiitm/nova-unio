<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoProgramacionRequest;
use App\Http\Requests\UpdateGrupoProgramacionRequest;
use App\Models\Grupo;
use App\Models\GrupoProgramacion;

class GrupoProgramacionController extends Controller
{
    public function store(StoreGrupoProgramacionRequest $request, Grupo $grupo)
    {
        $data = $request->validated();

        // Si no ponen vigencia, empieza hoy
        if (empty($data['vigente_desde'])) {
            $data['vigente_desde'] = now()->toDateString();
        }

        // Comprobación simple: fin > inicio
        if ($data['hora_fin'] <= $data['hora_inicio']) {
            return back()->withErrors(['hora_fin' => 'La hora fin debe ser mayor que la hora inicio.']);
        }

        $grupo->programaciones()->create($data);

        return back()->with('ok', 'Horario añadido.');
    }

    public function update(UpdateGrupoProgramacionRequest $request, Grupo $grupo, GrupoProgramacion $programacion)
    {
        $data = $request->validated();

        // Si lo dejan vacío, ponemos hoy
        if (empty($data['vigente_desde'])) {
            $data['vigente_desde'] = now()->toDateString();
        }

        if ($data['hora_fin'] <= $data['hora_inicio']) {
            return back()->withErrors(['hora_fin' => 'La hora fin debe ser mayor que la hora inicio.']);
        }

        // Para asegurar que es del mismo grupo
        if ($programacion->grupo_id !== $grupo->id) {
            abort(404);
        }

        $programacion->update($data);

        return back()->with('ok', 'Horario actualizado.');
    }

    public function destroy(Grupo $grupo, GrupoProgramacion $programacion)
    {
        if ($programacion->grupo_id !== $grupo->id) {
            abort(404);
        }

        $programacion->delete();

        return back()->with('ok', 'Horario borrado.');
    }
}