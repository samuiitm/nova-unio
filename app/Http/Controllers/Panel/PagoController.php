<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\TipoCuota;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    public function pendientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $grupo_id = (string) $request->query('grupo_id', '');
        $incluirSinGrupo = (bool) $request->boolean('incluir_sin_grupo', true);

        $grupos = Grupo::orderBy('nombre')->get(['id', 'nombre']);

        $cuotasPendientes = Cuota::query()
            ->with(['alumno.gruposActivos', 'tipoCuota'])
            ->where('estado', 'pendiente')
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('alumno', function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->when($grupo_id !== '', function ($query) use ($grupo_id) {
                $query->whereHas('alumno.gruposActivos', fn ($g) => $g->where('grupos.id', $grupo_id));
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $alumnosSinCuota = Alumno::query()
            ->where('activo', 1)
            ->with('gruposActivos')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->when($grupo_id !== '', function ($query) use ($grupo_id) {
                $query->whereHas('gruposActivos', fn ($g) => $g->where('grupos.id', $grupo_id));
            })
            ->when(!$incluirSinGrupo, function ($query) {
                $query->whereHas('gruposActivos');
            })
            ->whereDoesntHave('cuotas', function ($c) {
                $c->where('estado', '!=', 'anulada');
            })
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->limit(80)
            ->get();

        return view('panel.pagos.pendientes', compact(
            'q',
            'grupo_id',
            'incluirSinGrupo',
            'grupos',
            'cuotasPendientes',
            'alumnosSinCuota'
        ));
    }

    public function vencidas(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $grupo_id = (string) $request->query('grupo_id', '');
        $hoy = now()->toDateString();

        $grupos = Grupo::orderBy('nombre')->get(['id', 'nombre']);

        $subAlumnosConUltimaPagadaVencida = Cuota::query()
            ->select('alumno_id')
            ->where('estado', 'pagada')
            ->groupBy('alumno_id')
            ->havingRaw('MAX(fecha_fin) < ?', [$hoy]);

        $alumnos = Alumno::query()
            ->where('activo', 1)
            ->whereIn('id', $subAlumnosConUltimaPagadaVencida)
            ->whereDoesntHave('cuotas', fn ($c) => $c->where('estado', 'pendiente'))
            ->whereDoesntHave('cuotas', function ($c) use ($hoy) {
                $c->where('estado', 'pagada')
                    ->where(function ($q) use ($hoy) {
                        $q->whereNull('fecha_fin')
                            ->orWhereDate('fecha_fin', '>=', $hoy);
                    });
            })
            ->with(['gruposActivos', 'ultimaCuotaPagada.tipoCuota', 'ultimaCuotaPagada.pago']);

        if ($q !== '') {
            $alumnos->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%");
            });
        }

        if ($grupo_id !== '') {
            $alumnos->whereHas('gruposActivos', fn ($g) => $g->where('grupos.id', $grupo_id));
        }

        $alumnosVencidos = $alumnos
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('panel.pagos.vencidas', compact('q', 'grupo_id', 'grupos', 'alumnosVencidos'));
    }

    public function historial(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $metodo = (string) $request->query('metodo', '');
        $desde = (string) $request->query('desde', '');
        $hasta = (string) $request->query('hasta', '');
        $tipo_cuota_id = (string) $request->query('tipo_cuota_id', '');

        $baseQuery = Pago::query()
            ->join('alumnos', 'alumnos.id', '=', 'pagos.alumno_id')
            ->join('cuotas', 'cuotas.id', '=', 'pagos.cuota_id')
            ->leftJoin('tipos_cuota', 'tipos_cuota.id', '=', 'cuotas.tipo_cuota_id');

        if ($q !== '') {
            $baseQuery->where(function ($w) use ($q) {
                $w->where('alumnos.nombre', 'like', "%{$q}%")
                    ->orWhere('alumnos.apellidos', 'like', "%{$q}%");
            });
        }

        if (in_array($metodo, ['efectivo', 'bizum', 'tarjeta', 'transferencia', 'otro'], true)) {
            $baseQuery->where('pagos.metodo', $metodo);
        } else {
            $metodo = '';
        }

        if ($desde !== '') {
            $baseQuery->whereDate('pagos.fecha_pago', '>=', $desde);
        }

        if ($hasta !== '') {
            $baseQuery->whereDate('pagos.fecha_pago', '<=', $hasta);
        }

        if ($tipo_cuota_id !== '') {
            $baseQuery->where('cuotas.tipo_cuota_id', $tipo_cuota_id);
        }

        $totales = (clone $baseQuery)
            ->selectRaw("COUNT(*) as total, COALESCE(SUM(pagos.importe),0) as suma")
            ->first();

        $pagos = (clone $baseQuery)
            ->select('pagos.*')
            ->with(['alumno', 'cuota.tipoCuota'])
            ->orderByDesc('pagos.fecha_pago')
            ->orderByDesc('pagos.id')
            ->paginate(30)
            ->withQueryString();

        $tipos = TipoCuota::orderByDesc('activo')->orderBy('nombre')->get(['id', 'nombre']);

        return view('panel.pagos.historial', compact(
            'pagos',
            'tipos',
            'q',
            'metodo',
            'desde',
            'hasta',
            'tipo_cuota_id',
            'totales'
        ));
    }

    public function destroy(Pago $pago)
    {
        $cuota = $pago->cuota;

        if (!$cuota) {
            return back()->withErrors([
                'pago' => 'Este pago no tiene una cuota asociada válida.',
            ]);
        }

        $totalPagosCuota = $cuota->pagos()->count();

        if ($totalPagosCuota > 1) {
            return back()->withErrors([
                'pago' => 'Este pago pertenece a una cuota con historial antiguo reutilizado. Por seguridad no se puede borrar desde el panel. Revísalo manualmente para no perder historial.',
            ]);
        }

        DB::transaction(function () use ($pago, $cuota) {
            $pago->delete();

            $cuota->update([
                'estado' => 'pendiente',
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);
        });

        return back()->with('ok', 'Pago borrado correctamente. La cuota ha vuelto a pendiente.');
    }

    public function recibo(Pago $pago)
    {
        $pago->loadMissing(['alumno', 'cuota.tipoCuota']);

        $pdf = Pdf::loadView('pdf.justificante-pago', [
            'pago' => $pago,
        ]);

        $nombreAlumno = trim(($pago->alumno?->nombre ?? '') . ' ' . ($pago->alumno?->apellidos ?? ''));
        $nombreAlumnoSlug = Str::slug($nombreAlumno ?: 'alumno');
        $fecha = $pago->fecha_pago?->format('Y-m-d') ?? now()->format('Y-m-d');

        $nombreArchivo = "justificante-pago-{$pago->id}-{$nombreAlumnoSlug}-{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }
}