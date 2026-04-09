<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($estado === 'activos') {
            $query->where('activo', 1);
        }

        if ($estado === 'inactivos') {
            $query->where('activo', 0);
        }

        $grupos = $query
            ->with([
                'programaciones' => function ($q) {
                    $q->orderBy('dia_semana')->orderBy('hora_inicio');
                }
            ])
            ->withCount([
                'alumnosActivos as alumnos_count',
                'programaciones as horarios_count',
            ])
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

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
            'programaciones' => fn ($q) => $q->orderBy('dia_semana')->orderBy('hora_inicio'),
            'alumnosActivos' => fn ($q) => $q->orderBy('apellidos')->orderBy('nombre'),
        ]);

        $alumnosDisponibles = Alumno::where('activo', 1)
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellidos']);

        return view('panel.grupos.show', compact('grupo', 'alumnosDisponibles'));
    }

    public function edit(Grupo $grupo)
    {
        return redirect()->route('panel.grupos.show', $grupo);
    }

    public function update(UpdateGrupoRequest $request, Grupo $grupo)
    {
        $data = $request->validated();
        $data['activo'] = (bool) ($data['activo'] ?? false);

        $grupo->update($data);

        return back()->with('ok', 'Grupo actualizado.');
    }

    public function asignarAlumno(Request $request, Grupo $grupo)
    {
        $data = $request->validate([
            'alumno_id' => ['required', 'exists:alumnos,id'],
            'fecha_alta' => ['nullable', 'date'],
        ]);

        $hoy = $data['fecha_alta'] ?? now()->toDateString();
        $ahora = now();

        $yaEsta = DB::table('alumno_grupo')
            ->where('grupo_id', $grupo->id)
            ->where('alumno_id', $data['alumno_id'])
            ->whereNull('fecha_baja')
            ->exists();

        if ($yaEsta) {
            return back()->with('ok', 'Ese alumno ya está en el grupo.');
        }

        DB::table('alumno_grupo')->insert([
            'grupo_id' => $grupo->id,
            'alumno_id' => (int) $data['alumno_id'],
            'fecha_alta' => $hoy,
            'fecha_baja' => null,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        return back()->with('ok', 'Alumno asignado al grupo.');
    }

    public function bajaAlumno(Grupo $grupo, Alumno $alumno)
    {
        $actualizadas = DB::table('alumno_grupo')
            ->where('grupo_id', $grupo->id)
            ->where('alumno_id', $alumno->id)
            ->whereNull('fecha_baja')
            ->update([
                'fecha_baja' => now()->toDateString(),
                'updated_at' => now(),
            ]);

        if (!$actualizadas) {
            return back()->with('ok', 'No había ninguna asignación activa de este alumno en el grupo.');
        }

        return back()->with('ok', 'Alumno dado de baja del grupo.');
    }

    public function activarAlumno(Grupo $grupo, Alumno $alumno)
    {
        $yaEsta = DB::table('alumno_grupo')
            ->where('grupo_id', $grupo->id)
            ->where('alumno_id', $alumno->id)
            ->whereNull('fecha_baja')
            ->exists();

        if ($yaEsta) {
            return back()->with('ok', 'Ese alumno ya está activo en el grupo.');
        }

        DB::table('alumno_grupo')->insert([
            'grupo_id' => $grupo->id,
            'alumno_id' => $alumno->id,
            'fecha_alta' => now()->toDateString(),
            'fecha_baja' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('ok', 'Alumno activado en el grupo.');
    }

    public function destroy(Grupo $grupo)
    {
        $tieneAlumnos = $grupo->alumnosActivos()->exists();

        if ($tieneAlumnos) {
            return back()->with('ok', 'No se puede borrar: el grupo tiene alumnos asignados.');
        }

        $grupo->delete();

        return redirect()->route('panel.grupos.index')->with('ok', 'Grupo borrado.');
    }
}