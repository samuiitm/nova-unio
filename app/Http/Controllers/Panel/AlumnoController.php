<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Pago;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos');
        $orden = $request->query('orden', 'reciente');

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
        return view('panel.alumnos.create');
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
        $alumno->load([
            'gruposActivos',
        ]);

        $hoy = now()->toDateString();

        // Cuota vigente (pagada y fecha_fin >= hoy)
        $cuotaVigente = $alumno->cuotas()
            ->with(['tipoCuota', 'pago'])
            ->where('estado', 'pagada')
            ->whereDate('fecha_fin', '>=', $hoy)
            ->orderByDesc('fecha_fin')
            ->first();

        // Cuota pendiente (si existe)
        $cuotaPendiente = $alumno->cuotas()
            ->with(['tipoCuota'])
            ->where('estado', 'pendiente')
            ->orderByDesc('created_at')
            ->first();

        // Última cuota pagada (sirve para mostrar “vencida”)
        $ultimaPagada = $alumno->cuotas()
            ->with(['tipoCuota', 'pago'])
            ->where('estado', 'pagada')
            ->orderByDesc('fecha_fin')
            ->first();

        // Estado principal de la cuota del alumno (lo que usas arriba del ticket)
        $estadoCuota = 'sin_cuota';

        if ($cuotaVigente) {
            $estadoCuota = 'vigente';
        } elseif ($cuotaPendiente) {
            $estadoCuota = 'pendiente';
        } elseif ($ultimaPagada && $ultimaPagada->fecha_fin && $ultimaPagada->fecha_fin->toDateString() < $hoy) {
            $estadoCuota = 'vencida';
        }

        // Historial de cuotas (todas, incluyendo anuladas si las hubiera)
        $cuotas = $alumno->cuotas()
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('created_at')
            ->get();

        // Historial de pagos (por alumno)
        $pagos = Pago::query()
            ->where('alumno_id', $alumno->id)
            ->with(['cuota.tipoCuota'])
            ->orderByDesc('fecha_pago')
            ->get();

        return view('panel.alumnos.show', compact(
            'alumno',
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
        return view('panel.alumnos.edit', compact('alumno'));
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