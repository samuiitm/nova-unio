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
        $grupos = \App\Models\Grupo::where('activo', 1)->orderBy('nombre')->get();
        $tiposCuota = \App\Models\TipoCuota::where('activo', 1)->orderBy('nombre')->get();

        return view('panel.alumnos.create', compact('grupos', 'tiposCuota'));
    }

    public function store(StoreAlumnoRequest $request)
    {
        $data = $request->validated();

        $data['activo'] = true;
        $data['fecha_baja'] = null;
        $data['fecha_inicio_actividad'] = null;

        $alumno = Alumno::create($data);

        return redirect()
            ->route('panel.alumnos.show', $alumno)
            ->with('ok', 'Alumno creado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $hoy = now()->toDateString();

        // Grupos activos (para mostrar en la ficha)
        $gruposActivos = $alumno->gruposActivos()->orderBy('nombre')->get();

        // Cuota vigente: pagada y no vencida (fecha_fin >= hoy) o sin fecha_fin
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

        // Cuota pendiente (si existe)
        $cuotaPendiente = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pendiente')
            ->with(['tipoCuota'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        // Última pagada (para saber si está vencida)
        $ultimaPagada = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id')
            ->first();

        // Estado principal que usa tu "ticket"
        $estadoCuota = 'sin_cuota';

        if ($cuotaVigente) {
            $estadoCuota = 'vigente';
        } elseif ($cuotaPendiente) {
            $estadoCuota = 'pendiente';
        } elseif ($ultimaPagada && $ultimaPagada->fecha_fin && $ultimaPagada->fecha_fin->toDateString() < $hoy) {
            $estadoCuota = 'vencida';
        }

        // Historial de cuotas (para la tabla)
        $cuotas = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        // Historial de pagos (para la tabla)
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

        // grupos actuales del alumno (activos = fecha_baja null)
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

        $alumno->update($data);

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
}