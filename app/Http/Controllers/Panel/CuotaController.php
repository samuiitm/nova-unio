<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\CobrarCuotaRequest;
use App\Http\Requests\StoreCuotaAlumnoRequest;
use App\Mail\JustificantePagoMail;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Pago;
use App\Models\TipoCuota;
use App\Services\CalculadorVigenciaCuotaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CuotaController extends Controller
{
    private function alumnoTieneCuotaAsignada(Alumno $alumno): bool
    {
        $hoy = now()->toDateString();

        return Cuota::where('alumno_id', $alumno->id)
            ->where('estado', '!=', 'anulada')
            ->where(function ($q) use ($hoy) {
                $q->where('estado', 'pendiente')
                    ->orWhere(function ($w) use ($hoy) {
                        $w->where('estado', 'pagada')
                            ->where(function ($x) use ($hoy) {
                                $x->whereNull('fecha_fin')
                                    ->orWhereDate('fecha_fin', '>=', $hoy);
                            });
                    });
            })
            ->exists();
    }

    public function create(Alumno $alumno)
    {
        if ($this->alumnoTieneCuotaAsignada($alumno)) {
            return redirect()
                ->route('panel.alumnos.show', $alumno)
                ->with('ok', 'Este alumno ya tiene una cuota asignada (pendiente o vigente).');
        }

        $tipos = TipoCuota::where('activo', 1)->orderBy('nombre')->get();
        $fechaPagoSugerida = now()->toDateString();

        return view('panel.pagos.cuotas.crear', compact(
            'alumno',
            'tipos',
            'fechaPagoSugerida'
        ));
    }

    public function store(StoreCuotaAlumnoRequest $request, Alumno $alumno)
    {
        $data = $request->validated();

        if ($this->alumnoTieneCuotaAsignada($alumno)) {
            return back()->withErrors([
                'tipo_cuota_id' => 'Este alumno ya tiene una cuota asignada (pendiente o vigente).',
            ])->withInput();
        }

        $tipo = TipoCuota::findOrFail($data['tipo_cuota_id']);
        $pagoCreado = null;

        DB::transaction(function () use ($data, $alumno, $tipo, &$pagoCreado) {
            if ($data['estado'] === 'pendiente') {
                $this->validarAsignacionPendiente($tipo);

                Cuota::create([
                    'alumno_id' => $alumno->id,
                    'tipo_cuota_id' => $tipo->id,
                    'importe' => $tipo->importe,
                    'estado' => 'pendiente',
                    'fecha_inicio' => null,
                    'fecha_fin' => null,
                ]);

                return;
            }

            $fechaPago = Carbon::parse($data['fecha_pago']);
            $vigencia = $this->calcularVigenciaPagada($tipo, $fechaPago);
            $inicio = $vigencia['inicio'];
            $fin = $vigencia['fin'];

            $cuota = Cuota::create([
                'alumno_id' => $alumno->id,
                'tipo_cuota_id' => $tipo->id,
                'importe' => $tipo->importe,
                'estado' => 'pagada',
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin?->toDateString(),
            ]);

            $pagoCreado = Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $alumno->id,
                'fecha_pago' => $fechaPago->toDateString(),
                'importe' => $tipo->importe,
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,

                'tipo_cuota_id' => $tipo->id,
                'tipo_cuota_nombre' => $tipo->nombre,
                'vigencia_inicio' => $inicio->toDateString(),
                'vigencia_fin' => $fin?->toDateString(),
            ]);
        });

        $this->enviarJustificantePago($pagoCreado);

        return redirect()
            ->route('panel.alumnos.show', $alumno)
            ->with('ok', 'Cuota guardada correctamente.');
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

        $tipo = $cuota->tipoCuota;
        $pagoCreado = null;

        DB::transaction(function () use ($cuota, $data, $tipo, &$pagoCreado) {
            $fechaPago = Carbon::parse($data['fecha_pago']);
            $vigencia = $this->calcularVigenciaPagada($tipo, $fechaPago);
            $inicio = $vigencia['inicio'];
            $fin = $vigencia['fin'];

            $cuota->update([
                'estado' => 'pagada',
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin?->toDateString(),
                'importe' => $tipo->importe,
            ]);

            $pagoCreado = Pago::create([
                'cuota_id' => $cuota->id,
                'alumno_id' => $cuota->alumno_id,
                'fecha_pago' => $fechaPago->toDateString(),
                'importe' => $tipo->importe,
                'metodo' => $data['metodo'],
                'notas' => $data['notas'] ?? null,

                'tipo_cuota_id' => $tipo->id,
                'tipo_cuota_nombre' => $tipo->nombre,
                'vigencia_inicio' => $inicio->toDateString(),
                'vigencia_fin' => $fin?->toDateString(),
            ]);
        });

        $this->enviarJustificantePago($pagoCreado);

        return redirect()
            ->route('panel.alumnos.show', $cuota->alumno_id)
            ->with('ok', 'Pago registrado correctamente.');
    }

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

        $this->validarAsignacionPendiente($tipo);

        $cuota->update([
            'tipo_cuota_id' => $tipo->id,
            'importe' => $tipo->importe,
            'estado' => 'pendiente',
            'fecha_inicio' => null,
            'fecha_fin' => null,
        ]);

        return redirect()
            ->route('panel.alumnos.show', $cuota->alumno_id)
            ->with('ok', 'Cuota pendiente actualizada correctamente.');
    }

    public function destroy(Cuota $cuota)
    {
        if ($cuota->pagos()->exists()) {
            return back()->withErrors([
                'cuota' => 'No se puede eliminar una cuota que tiene pagos registrados. Para conservar el historial, esta acción queda bloqueada.',
            ]);
        }

        if ($cuota->estado !== 'pendiente') {
            return back()->withErrors([
                'cuota' => 'Solo se pueden eliminar cuotas pendientes sin pagos.',
            ]);
        }

        $cuota->delete();

        return back()->with('ok', 'Cuota eliminada correctamente.');
    }

    private function calculadorCuotas(): CalculadorVigenciaCuotaService
    {
        return app(CalculadorVigenciaCuotaService::class);
    }

    private function validarAsignacionPendiente(TipoCuota $tipo): void
    {
        try {
            $this->calculadorCuotas()->asegurarQueSePuedeAsignar($tipo, now()->toDateString());
        } catch (\DomainException $e) {
            throw ValidationException::withMessages([
                'tipo_cuota_id' => $e->getMessage(),
            ]);
        }
    }

    private function calcularVigenciaPagada(TipoCuota $tipo, Carbon $fechaPago): array
    {
        try {
            return $this->calculadorCuotas()->calcularParaPago($tipo, $fechaPago);
        } catch (\DomainException $e) {
            throw ValidationException::withMessages([
                'fecha_pago' => $e->getMessage(),
            ]);
        }
    }

    private function enviarJustificantePago(?Pago $pago): void
    {
        if (!$pago) {
            return;
        }

        if (!config('mail.enviar_justificantes_pago')) {
            return;
        }

        if (!app()->environment('production')) {
            return;
        }

        $pago->loadMissing(['alumno', 'cuota.tipoCuota']);

        $email = $pago->alumno?->email;

        if (!$email) {
            return;
        }

        try {
            Mail::to($email)->send(new JustificantePagoMail($pago));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}