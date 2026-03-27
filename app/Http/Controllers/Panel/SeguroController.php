<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CobrarSeguroRequest;
use App\Http\Requests\StoreSeguroRequest;
use App\Models\Alumno;
use App\Models\Seguro;
use App\Services\CalculadorVigenciaSeguroService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SeguroController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tipo = trim((string) $request->query('tipo', ''));
        $estado = $request->query('estado', 'todos');

        $hoy = now()->toDateString();

        $query = Seguro::query()
            ->with('alumno')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->whereHas('alumno', function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($tipo !== '' && array_key_exists($tipo, $this->calculador()->tiposDisponibles())) {
            $query->where('tipo', $tipo);
        }

        if ($estado === 'vigentes') {
            $query->vigentes($hoy);
        } elseif ($estado === 'vencidos') {
            $query->vencidos($hoy);
        } elseif ($estado === 'pendientes') {
            $query->pendientes();
        }

        $seguros = $query->paginate(15)->withQueryString();

        $resumen = [
            'vigentes' => Seguro::query()->vigentes($hoy)->count(),
            'vencidos' => Seguro::query()->vencidos($hoy)->count(),
            'pendientes' => Seguro::query()->pendientes()->count(),
        ];

        $tiposSeguro = $this->calculador()->tiposDisponibles();

        return view('panel.pagos.seguros.index', compact(
            'seguros',
            'q',
            'tipo',
            'estado',
            'resumen',
            'tiposSeguro'
        ));
    }

    public function create(Request $request)
    {
        $alumnoPreseleccionado = null;

        if ($request->filled('alumno')) {
            $alumnoPreseleccionado = Alumno::findOrFail((int) $request->input('alumno'));
        }

        $alumnos = Alumno::query()
            ->orderByDesc('activo')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellidos', 'dni', 'activo']);

        $tiposSeguro = $this->calculador()->tiposDisponibles();

        return view('panel.pagos.seguros.crear', compact(
            'alumnos',
            'tiposSeguro',
            'alumnoPreseleccionado'
        ));
    }

    public function store(StoreSeguroRequest $request)
    {
        $data = $request->validated();
        $alumnoId = (int) $data['alumno_id'];

        if ($this->alumnoTieneSeguroAsignado($alumnoId)) {
            return back()
                ->withInput()
                ->withErrors([
                    'alumno_id' => 'Este alumno ya tiene un seguro asignado pendiente o vigente.',
                ]);
        }

        $datosTipo = $this->calculador()->datosTipo($data['tipo']);

        $payload = [
            'alumno_id' => $alumnoId,
            'tipo' => $data['tipo'],
            'importe' => $datosTipo['importe'],
            'estado' => $data['estado'],
            'fecha_pago' => null,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'metodo' => null,
            'notas' => null,
        ];

        if ($data['estado'] === 'pagado') {
            $vigencia = $this->calculador()->calcularVigencia($data['tipo'], $data['fecha_pago']);

            $payload['fecha_pago'] = Carbon::parse($data['fecha_pago'])->toDateString();
            $payload['fecha_inicio'] = $vigencia['inicio']->toDateString();
            $payload['fecha_fin'] = $vigencia['fin']->toDateString();
            $payload['metodo'] = $data['metodo'];
            $payload['notas'] = $data['notas'] ?? null;
        }

        $seguro = Seguro::create($payload);

        return redirect()
            ->route('panel.alumnos.show', $seguro->alumno_id)
            ->with('ok', 'Seguro deportivo guardado correctamente.');
    }

    public function cobrar(Seguro $seguro)
    {
        if ($seguro->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden cobrar seguros pendientes.');
        }

        $seguro->load('alumno');

        return view('panel.pagos.seguros.cobrar', compact('seguro'));
    }

    public function guardarCobro(CobrarSeguroRequest $request, Seguro $seguro)
    {
        if ($seguro->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden cobrar seguros pendientes.');
        }

        $data = $request->validated();
        $vigencia = $this->calculador()->calcularVigencia($seguro->tipo, $data['fecha_pago']);

        $seguro->update([
            'estado' => 'pagado',
            'fecha_pago' => Carbon::parse($data['fecha_pago'])->toDateString(),
            'fecha_inicio' => $vigencia['inicio']->toDateString(),
            'fecha_fin' => $vigencia['fin']->toDateString(),
            'metodo' => $data['metodo'],
            'notas' => $data['notas'] ?? null,
        ]);

        return redirect()
            ->route('panel.alumnos.show', $seguro->alumno_id)
            ->with('ok', 'Pago del seguro registrado correctamente.');
    }

    public function edit(Seguro $seguro)
    {
        if ($seguro->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden editar seguros pendientes.');
        }

        $seguro->load('alumno');

        $alumnos = Alumno::query()
            ->orderByDesc('activo')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellidos', 'dni', 'activo']);

        $tiposSeguro = $this->calculador()->tiposDisponibles();

        return view('panel.pagos.seguros.editar', compact(
            'seguro',
            'alumnos',
            'tiposSeguro'
        ));
    }

    public function update(StoreSeguroRequest $request, Seguro $seguro)
    {
        if ($seguro->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden editar seguros pendientes.');
        }

        $data = $request->validated();
        $alumnoId = (int) $data['alumno_id'];

        if ($alumnoId !== (int) $seguro->alumno_id && $this->alumnoTieneSeguroAsignado($alumnoId)) {
            return back()
                ->withInput()
                ->withErrors([
                    'alumno_id' => 'Ese alumno ya tiene un seguro asignado pendiente o vigente.',
                ]);
        }

        $datosTipo = $this->calculador()->datosTipo($data['tipo']);

        $seguro->update([
            'alumno_id' => $alumnoId,
            'tipo' => $data['tipo'],
            'importe' => $datosTipo['importe'],
            'estado' => 'pendiente',
            'fecha_pago' => null,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'metodo' => null,
            'notas' => null,
        ]);

        return redirect()
            ->route('panel.alumnos.show', $seguro->alumno_id)
            ->with('ok', 'Seguro pendiente actualizado correctamente.');
    }

    public function destroy(Seguro $seguro)
    {
        if ($seguro->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden eliminar seguros pendientes.');
        }

        $seguro->delete();

        return back()->with('ok', 'Seguro eliminado correctamente.');
    }

    public function destroyPago(Seguro $seguro)
    {
        if ($seguro->estado !== 'pagado') {
            return back()->with('ok', 'Solo se puede borrar el pago de un seguro pagado.');
        }

        $seguro->update([
            'estado' => 'pendiente',
            'fecha_pago' => null,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'metodo' => null,
            'notas' => null,
        ]);

        return back()->with('ok', 'Pago del seguro borrado. El seguro ha vuelto a pendiente.');
    }

    private function alumnoTieneSeguroAsignado(int $alumnoId): bool
    {
        $hoy = now()->toDateString();

        return Seguro::query()
            ->where('alumno_id', $alumnoId)
            ->where(function ($q) use ($hoy) {
                $q->where('estado', 'pendiente')
                    ->orWhere(function ($w) use ($hoy) {
                        $w->where('estado', 'pagado')
                            ->whereDate('fecha_fin', '>=', $hoy);
                    });
            })
            ->exists();
    }

    private function calculador(): CalculadorVigenciaSeguroService
    {
        return app(CalculadorVigenciaSeguroService::class);
    }
}