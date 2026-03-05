<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CobrarCuotaRequest;
use App\Http\Requests\StoreCuotaAlumnoRequest;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\TipoCuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CuotaController extends Controller
{
    public function create(Alumno $alumno)
    {
        $tipos = TipoCuota::where('activo', 1)->orderBy('nombre')->get();

        $ultimaPagada = Cuota::where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->orderByDesc('fecha_fin')
            ->first();

        $fechaInicio = $ultimaPagada
            ? Carbon::parse($ultimaPagada->fecha_fin)->addDay()->toDateString()
            : now()->toDateString();

        return view('panel.pagos.cuotas.crear', compact('alumno', 'tipos', 'fechaInicio'));
    }

    public function store(StoreCuotaAlumnoRequest $request, Alumno $alumno)
    {
        $data = $request->validated();

        $tipo = null;
        if (!empty($data['tipo_cuota_id'])) {
            $tipo = TipoCuota::find($data['tipo_cuota_id']);
        }

        $inicio = Carbon::parse($data['fecha_inicio']);

        // Si hay tipo, calculamos fin e importe por defecto
        $fin = null;
        if ($tipo) {
            $fin = $inicio->copy()->addMonthsNoOverflow((int) $tipo->duracion_meses);
            $importe = $tipo->importe;
        } else {
            if (empty($data['fecha_fin'])) {
                return back()->withErrors(['fecha_fin' => 'La fecha fin es obligatoria si no eliges tipo de cuota.'])->withInput();
            }
            if (empty($data['importe'])) {
                return back()->withErrors(['importe' => 'El importe es obligatorio si no eliges tipo de cuota.'])->withInput();
            }
            $fin = Carbon::parse($data['fecha_fin']);
            $importe = $data['importe'];
        }

        // solape de cuotas (evita líos)
        $solapa = Cuota::where('alumno_id', $alumno->id)
            ->where('estado', '!=', 'anulada')
            ->whereDate('fecha_inicio', '<=', $fin->toDateString())
            ->whereDate('fecha_fin', '>=', $inicio->toDateString())
            ->exists();

        if ($solapa) {
            return back()->withErrors([
                'fecha_inicio' => 'Este periodo se solapa con otra cuota del alumno.',
            ])->withInput();
        }

        DB::transaction(function () use ($data, $alumno, $tipo, $inicio, $fin, $importe) {

            $cuota = Cuota::create([
                'alumno_id' => $alumno->id,
                'tipo_cuota_id' => $tipo?->id,
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
                'importe' => $importe,
                'estado' => $data['estado'],
            ]);

            if ($data['estado'] === 'pagada') {
                Pago::create([
                    'cuota_id' => $cuota->id,
                    'alumno_id' => $alumno->id,
                    'fecha_pago' => $data['fecha_pago'],
                    'importe' => $importe,
                    'metodo' => $data['metodo'],
                    'notas' => $data['notas'] ?? null,
                ]);
            }
        });

        return redirect()->route('panel.alumnos.show', $alumno)->with('ok', 'Cuota creada.');
    }

    public function cobrar(Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Esta cuota no se puede cobrar.');
        }

        $cuota->load(['alumno', 'tipoCuota']);

        return view('panel.pagos.cuotas.cobrar', compact('cuota'));
    }

    public function guardarCobro(CobrarCuotaRequest $request, Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Esta cuota no se puede cobrar.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($cuota, $data) {
            Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $cuota->alumno_id,
                'fecha_pago' => $data['fecha_pago'],
                'importe' => $data['importe'],
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,
            ]);

            $cuota->update(['estado' => 'pagada']);
        });

        return redirect()->route('panel.pagos.historial')->with('ok', 'Pago registrado.');
    }

    public function anular(Cuota $cuota)
    {
        if ($cuota->estado === 'pagada') {
            return back()->with('ok', 'No se puede anular una cuota ya pagada.');
        }

        $cuota->update(['estado' => 'anulada']);

        return back()->with('ok', 'Cuota anulada.');
    }
}