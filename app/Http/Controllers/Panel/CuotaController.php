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
    private function alumnoTieneCuotaAsignada(\App\Models\Alumno $alumno): bool
    {
        $hoy = now()->toDateString();

        return \App\Models\Cuota::where('alumno_id', $alumno->id)
            ->where('estado', '!=', 'anulada')
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
        // Si ya tiene una cuota asignada (pendiente o pagada vigente), no dejamos crear otra
        if ($this->alumnoTieneCuotaAsignada($alumno)) {
            return redirect()
                ->route('panel.alumnos.show', $alumno)
                ->with('ok', 'Este alumno ya tiene una cuota asignada (pendiente o vigente).');
        }

        $tipos = \App\Models\TipoCuota::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        // Fecha de pago sugerida (hoy). La cuota entra en vigor al pagar.
        $fechaPagoSugerida = now()->toDateString();

        return view('panel.pagos.cuotas.crear', compact('alumno', 'tipos', 'fechaPagoSugerida'));
    }

    public function store(StoreCuotaAlumnoRequest $request, Alumno $alumno)
    {
        if ($this->alumnoTieneCuotaAsignada($alumno)) {
            return back()->withErrors([
                'tipo_cuota_id' => 'Este alumno ya tiene una cuota asignada (pendiente o vigente).',
            ])->withInput();
        }

        $data = $request->validated();
        $tipo = TipoCuota::findOrFail($data['tipo_cuota_id']);

        DB::transaction(function () use ($data, $alumno, $tipo) {

            // cuota pendiente: NO está en vigor, se activará al cobrar
            if ($data['estado'] === 'pendiente') {
                Cuota::create([
                    'alumno_id' => $alumno->id,
                    'tipo_cuota_id' => $tipo->id,
                    // placeholder (no se usa hasta cobrar)
                    'fecha_inicio' => now()->toDateString(),
                    'fecha_fin' => now()->toDateString(),
                    'importe' => $tipo->importe,
                    'estado' => 'pendiente',
                ]);

                return;
            }

            // cuota pagada: entra en vigor hoy (fecha_pago <= hoy)
            $fechaPago = Carbon::parse($data['fecha_pago']);
            $inicio = $fechaPago->copy();
            $fin = $fechaPago->copy()->addMonthsNoOverflow((int) $tipo->duracion_meses);

            $cuota = Cuota::create([
                'alumno_id' => $alumno->id,
                'tipo_cuota_id' => $tipo->id,
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
                'importe' => $tipo->importe,
                'estado' => 'pagada',
            ]);

            Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $alumno->id,
                'fecha_pago' => $fechaPago->toDateString(),
                'importe' => $tipo->importe,
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,
            ]);
        });

        return redirect()->route('panel.alumnos.show', $alumno)->with('ok', 'Cuota asignada.');
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
        $cuota->load('tipoCuota');

        DB::transaction(function () use ($cuota, $data) {
            $fechaPago = Carbon::parse($data['fecha_pago']);
            $inicio = $fechaPago->copy();
            $fin = $fechaPago->copy()->addMonthsNoOverflow((int) $cuota->tipoCuota->duracion_meses);

            Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $cuota->alumno_id,
                'fecha_pago' => $fechaPago->toDateString(),
                'importe' => $cuota->tipoCuota->importe,
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,
            ]);

            $cuota->update([
                'estado' => 'pagada',
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
                'importe' => $cuota->tipoCuota->importe,
            ]);
        });

        return redirect()->route('panel.alumnos.show', $cuota->alumno_id)->with('ok', 'Pago registrado. Cuota activa.');
    }

    // Editar SOLO si está pendiente (sin pago)
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

        // reutilizamos validación, pero aquí forzamos estado pendiente
        $data = $request->validated();
        $tipo = TipoCuota::findOrFail($data['tipo_cuota_id']);

        $cuota->update([
            'tipo_cuota_id' => $tipo->id,
            'importe' => $tipo->importe,
            // placeholder
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->toDateString(),
            'estado' => 'pendiente',
        ]);

        return redirect()->route('panel.alumnos.show', $cuota->alumno_id)->with('ok', 'Cuota pendiente actualizada.');
    }

    // Eliminar SOLO si está pendiente y sin pago
    public function destroy(Cuota $cuota)
    {
        if ($cuota->estado !== 'pendiente') {
            return back()->with('ok', 'Solo se pueden eliminar cuotas pendientes.');
        }

        $tienePago = $cuota->pago()->exists();
        if ($tienePago) {
            return back()->with('ok', 'No se puede eliminar: tiene pago. Borra el pago primero.');
        }

        $cuota->delete();

        return back()->with('ok', 'Cuota eliminada.');
    }

    // Anular: deja rastro. Si tenía pago, lo borramos para no contar dinero
    public function anular(Cuota $cuota)
    {
        DB::transaction(function () use ($cuota) {
            $cuota->pago()->delete();
            $cuota->update(['estado' => 'anulada']);
        });

        return back()->with('ok', 'Cuota anulada.');
    }
}