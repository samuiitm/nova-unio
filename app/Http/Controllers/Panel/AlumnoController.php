<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos'); // todos | activos | inactivos
        $orden = $request->query('orden', 'reciente'); // reciente | nombre

        $query = Alumno::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('catsalut', 'like', "%{$q}%")
                    ->orWhere('poblacion', 'like', "%{$q}%");
            });
        }

        if ($estado === 'activos') {
            $query->where('activo', 1);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', 0);
        }

        if ($orden === 'nombre') {
            $query->orderByDesc('activo')
                ->orderBy('apellidos')
                ->orderBy('nombre');
        } else {
            $query->orderByDesc('activo')
                ->orderByDesc('created_at');
        }

        $alumnos = $query->paginate(10)->withQueryString();

        $nuevosMes = Alumno::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return view('panel.alumnos.index', compact('alumnos', 'q', 'estado', 'orden', 'nuevosMes'));
    }

    public function create()
    {
        $grupos = Grupo::where('activo', 1)->orderBy('nombre')->get();
        $tiposCuota = \App\Models\TipoCuota::where('activo', 1)->orderBy('nombre')->get();

        return view('panel.alumnos.create', compact('grupos', 'tiposCuota'));
    }

    public function store(StoreAlumnoRequest $request)
    {
        $data = $request->validated();

        $grupoIds = collect($data['grupos'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        unset($data['grupos']);

        $data['activo'] = true;
        $data['fecha_baja'] = null;
        $data['fecha_inicio_actividad'] = null;

        $alumno = DB::transaction(function () use ($data, $grupoIds) {
            $alumno = Alumno::create($data);

            $this->sincronizarGrupos($alumno, $grupoIds);

            return $alumno;
        });

        return redirect()
            ->route('panel.alumnos.show', $alumno)
            ->with('ok', 'Alumno creado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $hoy = now()->toDateString();

        $gruposActivos = $alumno->gruposActivos()->orderBy('nombre')->get();

        $cuotaVigente = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', $hoy);
            })
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id')
            ->first();

        $cuotaPendiente = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pendiente')
            ->with(['tipoCuota'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        $ultimaPagada = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id')
            ->first();

        $estadoCuota = 'sin_cuota';

        if ($cuotaVigente) {
            $estadoCuota = 'vigente';
        } elseif ($cuotaPendiente) {
            $estadoCuota = 'pendiente';
        } elseif ($ultimaPagada && $ultimaPagada->fecha_fin && $ultimaPagada->fecha_fin->toDateString() < $hoy) {
            $estadoCuota = 'vencida';
        }

        $cuotas = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $pagos = Pago::query()
            ->where('alumno_id', $alumno->id)
            ->with(['cuota.tipoCuota'])
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->get();

        return view('panel.alumnos.show', compact(
            'alumno',
            'gruposActivos',
            'estadoCuota',
            'cuotaVigente',
            'cuotaPendiente',
            'ultimaPagada',
            'cuotas',
            'pagos'
        ));
    }

    public function edit(Alumno $alumno)
    {
        $grupos = Grupo::where('activo', 1)->orderBy('nombre')->get();

        $gruposSeleccionados = $alumno->grupos()
            ->wherePivotNull('fecha_baja')
            ->pluck('grupos.id')
            ->map(fn ($v) => (int) $v)
            ->all();

        return view('panel.alumnos.edit', compact('alumno', 'grupos', 'gruposSeleccionados'));
    }

    public function update(UpdateAlumnoRequest $request, Alumno $alumno)
    {
        $data = $request->validated();

        $grupoIds = collect($data['grupos'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        unset($data['grupos']);

        DB::transaction(function () use ($alumno, $data, $grupoIds) {
            $alumno->update($data);
            $this->sincronizarGrupos($alumno, $grupoIds);
        });

        return redirect()
            ->route('panel.alumnos.show', $alumno)
            ->with('ok', 'Alumno actualizado.');
    }

    public function baja(Alumno $alumno)
    {
        $alumno->update([
            'activo' => false,
            'fecha_baja' => now()->toDateString(),
        ]);

        return back()->with('ok', 'Alumno dado de baja.');
    }

    public function activar(Alumno $alumno)
    {
        $alumno->update([
            'activo' => true,
            'fecha_baja' => null,
        ]);

        return back()->with('ok', 'Alumno activado.');
    }

    private function sincronizarGrupos(Alumno $alumno, array $grupoIds): void
    {
        $ahora = now();
        $hoy = $ahora->toDateString();

        $grupoIds = collect($grupoIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $gruposActivosActuales = DB::table('alumno_grupo')
            ->where('alumno_id', $alumno->id)
            ->whereNull('fecha_baja')
            ->pluck('grupo_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $gruposParaAlta = array_values(array_diff($grupoIds, $gruposActivosActuales));
        $gruposParaBaja = array_values(array_diff($gruposActivosActuales, $grupoIds));

        foreach ($gruposParaAlta as $grupoId) {
            $alumno->grupos()->attach($grupoId, [
                'fecha_alta' => $hoy,
                'fecha_baja' => null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        if (!empty($gruposParaBaja)) {
            DB::table('alumno_grupo')
                ->where('alumno_id', $alumno->id)
                ->whereIn('grupo_id', $gruposParaBaja)
                ->whereNull('fecha_baja')
                ->update([
                    'fecha_baja' => $hoy,
                    'updated_at' => $ahora,
                ]);
        }
    }
}