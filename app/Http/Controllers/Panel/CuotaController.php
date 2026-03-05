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
    private function alumnoTieneCuotaAsignada(Alumno $alumno): bool
    {
        $hoy = now()->toDateString();

        return Cuota::where('alumno_id', $alumno->id)
            ->where('estado', '!=', 'anulada') // por si quedaran antiguas
            ->where(function ($q) use ($hoy) {
                $q->where('estado', 'pendiente')
                  ->orWhere(function ($w) use ($hoy) {
                      $w->where('estado', 'pagada')
                        ->whereDate('fecha_fin', '>=', $hoy);
                  });
            })
            ->exists();
    }

    public function create(Alumno $alumno)
    {
        // Si tiene pendiente o vigente, NO dejamos asignar desde aquí.
        // (los cambios se hacen editando la pendiente o borrando pago)
        if ($this->alumnoTieneCuotaAsignada($alumno)) {
            return redirect()
                ->route('panel.alumnos.show', $alumno)
                ->with('ok', 'Este alumno ya tiene una cuota asignada (pendiente o vigente).');
        }

        $tipos = TipoCuota::where('activo', 1)->orderBy('nombre')->get();

        // Si el alumno tiene una cuota vencida, la vamos a renovar EDITANDO esa misma cuota
        $hoy = now()->toDateString();
        $cuotaVencida = Cuota::where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->whereDate('fecha_fin', '<', $hoy)
            ->orderByDesc('fecha_fin')
            ->first();

        $cuotaIdRenovar = $cuotaVencida?->id; // si es null, es asignación “desde cero”
        $fechaPagoSugerida = now()->toDateString();

        return view('panel.pagos.cuotas.crear', compact(
            'alumno',
            'tipos',
            'fechaPagoSugerida',
            'cuotaIdRenovar'
        ));
    }

    public function store(StoreCuotaAlumnoRequest $request, Alumno $alumno)
    {
        $data = $request->validated();

        // Si llega cuotaIdRenovar => actualizamos esa cuota (renovar sin crear otra)
        $cuota = null;

        if ($request->filled('cuota_id')) {
            $cuota = Cuota::where('id', $request->input('cuota_id'))
                ->where('alumno_id', $alumno->id)
                ->firstOrFail();
        } else {
            // si no viene cuota_id, pero hay una vencida, también renovamos esa (por seguridad)
            $hoy = now()->toDateString();
            $cuota = Cuota::where('alumno_id', $alumno->id)
                ->where('estado', 'pagada')
                ->whereDate('fecha_fin', '<', $hoy)
                ->orderByDesc('fecha_fin')
                ->first();
        }

        // si NO hay cuota existente, creamos una nueva (alumno sin cuota)
        if (!$cuota) {
            // si tiene pendiente o vigente, bloqueamos
            if ($this->alumnoTieneCuotaAsignada($alumno)) {
                return back()->withErrors([
                    'tipo_cuota_id' => 'Este alumno ya tiene una cuota asignada (pendiente o vigente).',
                ])->withInput();
            }

            $cuota = new Cuota(['alumno_id' => $alumno->id]);
        }

        $tipo = TipoCuota::findOrFail($data['tipo_cuota_id']);

        DB::transaction(function () use ($data, $alumno, $tipo, $cuota) {

            // ---- PENDIENTE: NO entra en vigor, NO hay pago
            if ($data['estado'] === 'pendiente') {
                $cuota->fill([
                    'tipo_cuota_id' => $tipo->id,
                    'importe' => $tipo->importe,
                    'estado' => 'pendiente',

                    // no usamos fechas reales en pendiente (las pondremos al cobrar)
                    'fecha_inicio' => now()->toDateString(),
                    'fecha_fin' => now()->toDateString(),
                ])->save();

                return;
            }

            // ---- PAGADA: entra en vigor en fecha_pago
            $fechaPago = Carbon::parse($data['fecha_pago']);
            $inicio = $fechaPago->copy();
            $fin = $fechaPago->copy()->addMonthsNoOverflow((int) $tipo->duracion_meses);

            // actualizamos la cuota (si era vencida => renovación sin crear otra)
            $cuota->fill([
                'tipo_cuota_id' => $tipo->id,
                'importe' => $tipo->importe,
                'estado' => 'pagada',
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
            ])->save();

            // creamos un pago NUEVO (historial real)
            Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $alumno->id,
                'fecha_pago' => $fechaPago->toDateString(),
                'importe' => $tipo->importe,
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,

                'tipo_cuota_id' => $tipo->id,
                'tipo_cuota_nombre' => $tipo->nombre,
                'vigencia_inicio' => $inicio->toDateString(),
                'vigencia_fin' => $fin->toDateString(),
            ]);
        });

        return redirect()->route('panel.alumnos.show', $alumno)->with('ok', 'Cuota guardada.');
    }

    public function cobrar(Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden cobrar cuotas pendientes.');
        }

        $cuota->load(['alumno', 'tipoCuota']);

        return view('panel.pagos.cuotas.cobrar', compact('cuota'));
    }

    public function guardarCobro(CobrarCuotaRequest $request, Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden cobrar cuotas pendientes.');
        }

        $data = $request->validated();
        $cuota->load(['tipoCuota', 'alumno']);

        DB::transaction(function () use ($cuota, $data) {
            $fechaPago = Carbon::parse($data['fecha_pago']);
            $inicio = $fechaPago->copy();
            $fin = $fechaPago->copy()->addMonthsNoOverflow((int) $cuota->tipoCuota->duracion_meses);

            // actualizamos cuota a pagada y le ponemos vigencia real
            $cuota->update([
                'estado' => 'pagada',
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
                'importe' => $cuota->tipoCuota->importe,
            ]);

            // pago nuevo en historial
            Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $cuota->alumno_id,
                'fecha_pago' => $fechaPago->toDateString(),
                'importe' => $cuota->tipoCuota->importe,
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,

                'tipo_cuota_id' => $cuota->tipoCuota->id,
                'tipo_cuota_nombre' => $cuota->tipoCuota->nombre,
                'vigencia_inicio' => $inicio->toDateString(),
                'vigencia_fin' => $fin->toDateString(),
            ]);
        });

        return redirect()->route('panel.alumnos.show', $cuota->alumno_id)->with('ok', 'Pago registrado.');
    }

    // editar SOLO si pendiente
    public function edit(Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden editar cuotas pendientes.');
        }

        $cuota->load('alumno');
        $tipos = TipoCuota::where('activo', 1)->orderBy('nombre')->get();

        return view('panel.pagos.cuotas.editar', compact('cuota', 'tipos'));
    }

    public function update(StoreCuotaAlumnoRequest $request, Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden editar cuotas pendientes.');
        }

        $data = $request->validated();
        $tipo = TipoCuota::findOrFail($data['tipo_cuota_id']);

        $cuota->update([
            'tipo_cuota_id' => $tipo->id,
            'importe' => $tipo->importe,
            'estado' => 'pendiente',
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->toDateString(),
        ]);

        return redirect()->route('panel.alumnos.show', $cuota->alumno_id)->with('ok', 'Cuota pendiente actualizada.');
    }

    // eliminar SOLO si pendiente
    public function destroy(Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden eliminar cuotas pendientes.');
        }

        $cuota->delete();

        return back()->with('ok', 'Cuota eliminada.');
    }
}