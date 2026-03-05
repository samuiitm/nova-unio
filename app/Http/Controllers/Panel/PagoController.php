<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Grupo;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function pendientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $grupo_id = (string) $request->query('grupo_id', '');
        $incluirSinGrupo = (bool) $request->boolean('incluir_sin_grupo', false);

        $grupos = Grupo::orderBy('nombre')->get(['id', 'nombre']);

        // Cuotas pendientes = estado pendiente (no están en vigor)
        $cuotasQuery = Cuota::query()
            ->with(['alumno.gruposActivos', 'tipoCuota'])
            ->where('estado', 'pendiente');

        if ($q !== '') {
            $cuotasQuery->whereHas('alumno', function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhere('apellidos', 'like', "%{$q}%");
            });
        }

        if ($grupo_id !== '') {
            $cuotasQuery->whereHas('alumno.gruposActivos', fn ($g) => $g->where('grupos.id', $grupo_id));
        }

        if (!$incluirSinGrupo) {
            $cuotasQuery->whereHas('alumno.gruposActivos');
        }

        $cuotasPendientes = $cuotasQuery
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Alumnos sin cuota = no tienen cuotas (o solo anuladas)
        $alumnosQuery = Alumno::query()
            ->where('activo', 1)
            ->with(['gruposActivos', 'ultimaCuotaPagada']);

        if ($q !== '') {
            $alumnosQuery->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhere('apellidos', 'like', "%{$q}%");
            });
        }

        if ($grupo_id !== '') {
            $alumnosQuery->whereHas('gruposActivos', fn ($g) => $g->where('grupos.id', $grupo_id));
        }

        if (!$incluirSinGrupo) {
            $alumnosQuery->whereHas('gruposActivos');
        }

        $alumnosQuery->whereDoesntHave('cuotas', function ($c) {
            $c->where('estado', '!=', 'anulada');
        });

        $alumnosSinCuota = $alumnosQuery
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->limit(50)
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

        // Cuotas vencidas = pagadas que el fin ya pasó
        // Y solo mostramos las que de verdad “necesitan renovar”:
        // - sin cuota vigente pagada
        // - sin cuota pendiente
        $alumnos = Alumno::query()
            ->where('activo', 1)
            ->with(['gruposActivos', 'ultimaCuotaPagada'])
            ->whereDoesntHave('cuotas', function ($c) {
                $c->where('estado', 'pendiente');
            })
            ->whereDoesntHave('cuotas', function ($c) use ($hoy) {
                $c->where('estado', 'pagada')
                  ->whereDate('fecha_fin', '>=', $hoy);
            })
            ->whereHas('cuotas', function ($c) {
                $c->where('estado', 'pagada');
            });

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
        // lo dejamos como está (global), porque sirve para "caja"
        // luego lo añadimos también en ficha del alumno
        $q = trim((string) $request->query('q', ''));
        $metodo = (string) $request->query('metodo', '');
        $desde = (string) $request->query('desde', '');
        $hasta = (string) $request->query('hasta', '');

        $baseQuery = Pago::query()
            ->join('alumnos', 'alumnos.id', '=', 'pagos.alumno_id');

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

        if ($desde !== '') $baseQuery->whereDate('pagos.fecha_pago', '>=', $desde);
        if ($hasta !== '') $baseQuery->whereDate('pagos.fecha_pago', '<=', $hasta);

        $totales = (clone $baseQuery)
            ->selectRaw("COUNT(*) as total, COALESCE(SUM(pagos.importe),0) as suma")
            ->first();

        $pagos = (clone $baseQuery)
            ->select('pagos.*')
            ->with(['alumno', 'cuota.tipoCuota'])
            ->orderByDesc('pagos.fecha_pago')
            ->paginate(30)
            ->withQueryString();

        return view('panel.pagos.historial', compact('pagos', 'q', 'metodo', 'desde', 'hasta', 'totales'));
    }

    public function destroy(Pago $pago)
    {
        DB::transaction(function () use ($pago) {
            $pago->cuota()->update(['estado' => 'pendiente']);
            $pago->delete();
        });

        return back()->with('ok', 'Pago eliminado. La cuota vuelve a pendiente.');
    }
}