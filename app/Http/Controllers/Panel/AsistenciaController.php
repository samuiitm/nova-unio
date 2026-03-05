<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    /**
     * Historial general de asistencias (últimas asistencias con filtros)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = (string) $request->query('estado', '');
        $grupo_id = (string) $request->query('grupo_id', '');
        $desde = (string) $request->query('desde', '');
        $hasta = (string) $request->query('hasta', '');

        // query base con joins para poder ordenar/filtrar por fecha y grupo
        $baseQuery = Asistencia::query()
            ->select('asistencias.*')
            ->join('clases', 'clases.id', '=', 'asistencias.clase_id')
            ->join('grupos', 'grupos.id', '=', 'clases.grupo_id')
            ->join('alumnos', 'alumnos.id', '=', 'asistencias.alumno_id');

        // filtro por alumno (nombre/apellidos)
        if ($q !== '') {
            $baseQuery->where(function ($w) use ($q) {
                $w->where('alumnos.nombre', 'like', '%' . $q . '%')
                  ->orWhere('alumnos.apellidos', 'like', '%' . $q . '%');
            });
        }

        // filtro por estado
        if (in_array($estado, ['presente', 'ausente'], true)) {
            $baseQuery->where('asistencias.estado', $estado);
        } else {
            $estado = '';
        }

        // filtro por grupo
        if ($grupo_id !== '') {
            $baseQuery->where('clases.grupo_id', $grupo_id);
        }

        // filtro por fechas
        if ($desde !== '') {
            $baseQuery->whereDate('clases.fecha', '>=', $desde);
        }

        if ($hasta !== '') {
            $baseQuery->whereDate('clases.fecha', '<=', $hasta);
        }

        // resumen (para mostrar totales arriba)
        $totales = (clone $baseQuery)
            ->selectRaw("
                COUNT(*) as total,
                SUM(asistencias.estado = 'presente') as presentes,
                SUM(asistencias.estado = 'ausente') as ausentes
            ")
            ->first();

        // listado
        $asistencias = $baseQuery
            ->with(['clase.grupo', 'alumno'])
            ->orderBy('clases.fecha', 'desc')
            ->orderBy('clases.hora_inicio', 'desc')
            ->paginate(30)
            ->withQueryString();

        $grupos = Grupo::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('panel.asistencias.index', compact(
            'asistencias',
            'grupos',
            'q',
            'estado',
            'grupo_id',
            'desde',
            'hasta',
            'totales'
        ));
    }

    /**
     * Historial de asistencias de un alumno
     */
    public function alumno(Request $request, Alumno $alumno)
    {
        $estado = (string) $request->query('estado', '');
        $grupo_id = (string) $request->query('grupo_id', '');
        $desde = (string) $request->query('desde', '');
        $hasta = (string) $request->query('hasta', '');

        $baseQuery = Asistencia::query()
            ->select('asistencias.*')
            ->join('clases', 'clases.id', '=', 'asistencias.clase_id')
            ->join('grupos', 'grupos.id', '=', 'clases.grupo_id')
            ->where('asistencias.alumno_id', $alumno->id);

        if (in_array($estado, ['presente', 'ausente'], true)) {
            $baseQuery->where('asistencias.estado', $estado);
        } else {
            $estado = '';
        }

        if ($grupo_id !== '') {
            $baseQuery->where('clases.grupo_id', $grupo_id);
        }

        if ($desde !== '') {
            $baseQuery->whereDate('clases.fecha', '>=', $desde);
        }

        if ($hasta !== '') {
            $baseQuery->whereDate('clases.fecha', '<=', $hasta);
        }

        $totales = (clone $baseQuery)
            ->selectRaw("
                COUNT(*) as total,
                SUM(asistencias.estado = 'presente') as presentes,
                SUM(asistencias.estado = 'ausente') as ausentes
            ")
            ->first();

        $asistencias = $baseQuery
            ->with(['clase.grupo'])
            ->orderBy('clases.fecha', 'desc')
            ->orderBy('clases.hora_inicio', 'desc')
            ->paginate(30)
            ->withQueryString();

        $grupos = Grupo::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('panel.asistencias.alumno', compact(
            'alumno',
            'asistencias',
            'grupos',
            'estado',
            'grupo_id',
            'desde',
            'hasta',
            'totales'
        ));
    }
}