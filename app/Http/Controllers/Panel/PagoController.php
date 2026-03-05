<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\TipoCuota;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function pendientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $grupo_id = (string) $request->query('grupo_id', '');
        $incluirSinGrupo = (bool) $request->boolean('incluir_sin_grupo', false);

        $hoy = now()->toDateString();

        $grupos = Grupo::orderBy('nombre')->get(['id', 'nombre']);

        // Cuotas pendientes NO vencidas
        $cuotasQuery = Cuota::query()
            ->with(['alumno.gruposActivos', 'tipoCuota'])
            ->where('estado', 'pendiente')
            ->whereDate('fecha_fin', '>=', $hoy);

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
            ->orderBy('fecha_fin')
            ->paginate(15)
            ->withQueryString();

        // Alumnos sin cuota (sin cuota actual, sin cuota vencida pendiente, sin cuota futura pendiente)
        $alumnosQuery = Alumno::query()
            ->where('activo', 1)
            ->with(['gruposActivos', 'ultimaCuota']);

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

        // No tiene cuota que cubra hoy (pagada o pendiente)
        $alumnosQuery->whereDoesntHave('cuotas', function ($c) use ($hoy) {
            $c->where('estado', '!=', 'anulada')
              ->whereDate('fecha_inicio', '<=', $hoy)
              ->whereDate('fecha_fin', '>=', $hoy);
        });

        // No tiene cuota pendiente vencida (eso va a "Cuotas vencidas")
        $alumnosQuery->whereDoesntHave('cuotas', function ($c) use ($hoy) {
            $c->where('estado', 'pendiente')
              ->whereDate('fecha_fin', '<', $hoy);
        });

        // No tiene cuota pendiente futura creada
        $alumnosQuery->whereDoesntHave('cuotas', function ($c) use ($hoy) {
            $c->where('estado', 'pendiente')
              ->whereDate('fecha_inicio', '>', $hoy);
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

        $query = Cuota::query()
            ->with(['alumno.gruposActivos', 'tipoCuota'])
            ->where('estado', 'pendiente')
            ->whereDate('fecha_fin', '<', $hoy);

        if ($q !== '') {
            $query->whereHas('alumno', function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhere('apellidos', 'like', "%{$q}%");
            });
        }

        if ($grupo_id !== '') {
            $query->whereHas('alumno.gruposActivos', fn ($g) => $g->where('grupos.id', $grupo_id));
        }

        $cuotasVencidas = $query
            ->orderByDesc('fecha_fin')
            ->paginate(20)
            ->withQueryString();

        return view('panel.pagos.vencidas', compact('q', 'grupo_id', 'grupos', 'cuotasVencidas'));
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
}