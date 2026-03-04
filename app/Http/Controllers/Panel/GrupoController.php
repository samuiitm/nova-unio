<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos'); // todos | activos | inactivos

        $query = Grupo::query();

        if ($q !== '') {
            $query->where('nombre', 'like', "%{$q}%");
        }

        if ($estado === 'activos') $query->where('activo', 1);
        if ($estado === 'inactivos') $query->where('activo', 0);

        $grupos = $query->orderByDesc('activo')->orderBy('nombre')->paginate(10)->withQueryString();

        return view('panel.grupos.index', compact('grupos', 'q', 'estado'));
    }

    public function create()
    {
        return view('panel.grupos.create');
    }

    public function store(StoreGrupoRequest $request)
    {
        $data = $request->validated();
        $data['activo'] = (bool) ($data['activo'] ?? true);

        $grupo = Grupo::create($data);

        return redirect()->route('panel.grupos.show', $grupo)->with('ok', 'Grupo creado.');
    }

    public function show(Grupo $grupo)
    {
        $grupo->load([
            'programaciones' => fn($q) => $q->orderBy('dia_semana')->orderBy('hora_inicio'),
            'alumnosActivos' => fn($q) => $q->orderBy('apellidos')->orderBy('nombre'),
        ]);

        // Para el selector de “asignar alumno”
        $alumnosDisponibles = Alumno::where('activo', 1)
            ->orderBy('apellidos')->orderBy('nombre')
            ->get(['id', 'nombre', 'apellidos']);

        return view('panel.grupos.show', compact('grupo', 'alumnosDisponibles'));
    }

    public function edit(Grupo $grupo)
    {
        return view('panel.grupos.edit', compact('grupo'));
    }

    public function update(UpdateGrupoRequest $request, Grupo $grupo)
    {
        $data = $request->validated();
        $data['activo'] = (bool) ($data['activo'] ?? false);

        $grupo->update($data);

        return redirect()->route('panel.grupos.show', $grupo)->with('ok', 'Grupo actualizado.');
    }

    // Asignar alumno a grupo
    public function asignarAlumno(Request $request, Grupo $grupo)
    {
        $data = $request->validate([
            'alumno_id' => ['required', 'exists:alumnos,id'],
            'fecha_alta' => ['nullable', 'date'],
        ]);

        // Evitar duplicado activo
        $yaEsta = $grupo->alumnos()
            ->where('alumnos.id', $data['alumno_id'])
            ->wherePivotNull('fecha_baja')
            ->exists();

        if ($yaEsta) {
            return back()->with('ok', 'Ese alumno ya está en el grupo.');
        }

        $grupo->alumnos()->attach($data['alumno_id'], [
            'fecha_alta' => $data['fecha_alta'] ?? now()->toDateString(),
            'fecha_baja' => null,
        ]);

        return back()->with('ok', 'Alumno asignado al grupo.');
    }

    // Dar de baja alumno del grupo
    public function bajaAlumno(Grupo $grupo, Alumno $alumno)
    {
        $grupo->alumnos()->updateExistingPivot($alumno->id, [
            'fecha_baja' => now()->toDateString(),
        ]);

        return back()->with('ok', 'Alumno dado de baja del grupo.');
    }

    // Reactivar alumno en el grupo (crea alta nueva)
    public function activarAlumno(Grupo $grupo, Alumno $alumno)
    {
        $yaEsta = $grupo->alumnos()
            ->where('alumnos.id', $alumno->id)
            ->wherePivotNull('fecha_baja')
            ->exists();

        if ($yaEsta) {
            return back()->with('ok', 'Ese alumno ya está activo en el grupo.');
        }

        $grupo->alumnos()->attach($alumno->id, [
            'fecha_alta' => now()->toDateString(),
            'fecha_baja' => null,
        ]);

        return back()->with('ok', 'Alumno activado en el grupo.');
    }
}