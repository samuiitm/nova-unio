<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Clase;
use App\Models\Pago;
use App\Models\Preinscripcion;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    public function resumen(Request $request)
    {
        [$mes, $inicio, $fin] = $this->resolverMes($request->query('mes'));
        $resumen = $this->montarResumenMensual($inicio, $fin);

        return view('panel.informes.resumen', compact('mes', 'inicio', 'fin', 'resumen'));
    }

    public function resumenPdf(Request $request)
    {
        [$mes, $inicio, $fin] = $this->resolverMes($request->query('mes'));

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()
                ->route('panel.informes.resumen', ['mes' => $mes])
                ->with('error', 'Falta instalar la librería de PDF: composer require barryvdh/laravel-dompdf');
        }

        $resumen = $this->montarResumenMensual($inicio, $fin);

        $pdf = Pdf::loadView('panel.informes.pdf.resumen-mensual', [
            'mes' => $mes,
            'inicio' => $inicio,
            'fin' => $fin,
            'resumen' => $resumen,
        ])->setPaper('a4');

        return $pdf->download('resumen-mensual-' . $mes . '.pdf');
    }

    private function resolverMes(?string $mes): array
    {
        $mes = is_string($mes) && preg_match('/^\d{4}-\d{2}$/', $mes)
            ? $mes
            : now()->format('Y-m');

        $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        return [$mes, $inicio, $fin];
    }

    private function montarResumenMensual(Carbon $inicio, Carbon $fin): array
    {
        $inicioDia = $inicio->copy()->startOfDay();
        $finDia = $fin->copy()->endOfDay();

        $inicioDate = $inicio->toDateString();
        $finDate = $fin->toDateString();

        $hoy = now()->startOfDay();

        $alumnosNuevos = Alumno::query()
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->count();

        $alumnosActivosCierre = Alumno::query()
            ->whereDate('created_at', '<=', $finDate)
            ->where(function ($q) use ($finDate) {
                $q->whereNull('fecha_baja')
                    ->orWhereDate('fecha_baja', '>', $finDate);
            })
            ->count();

        $preinscripcionesRecibidas = Preinscripcion::query()
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->count();

        $preinscripcionesConvertidas = Preinscripcion::query()
            ->whereNotNull('alumno_id')
            ->whereBetween('updated_at', [$inicioDia, $finDia])
            ->count();

        $ingresos = (float) Pago::query()
            ->whereBetween('fecha_pago', [$inicioDate, $finDate])
            ->sum('importe');

        $pagosTotal = Pago::query()
            ->whereBetween('fecha_pago', [$inicioDate, $finDate])
            ->count();

        $ticketMedio = $pagosTotal > 0
            ? round($ingresos / $pagosTotal, 2)
            : 0;

        $pagosPorMetodo = Pago::query()
            ->select('metodo')
            ->selectRaw('COUNT(*) as total_pagos')
            ->selectRaw('COALESCE(SUM(importe), 0) as total_importe')
            ->whereBetween('fecha_pago', [$inicioDate, $finDate])
            ->groupBy('metodo')
            ->orderByDesc('total_importe')
            ->get();

        $pagosPorTipo = Pago::query()
            ->selectRaw("COALESCE(tipo_cuota_nombre, 'Sin tipo') as tipo")
            ->selectRaw('COUNT(*) as total_pagos')
            ->selectRaw('COALESCE(SUM(importe), 0) as total_importe')
            ->whereBetween('fecha_pago', [$inicioDate, $finDate])
            ->groupBy(DB::raw("COALESCE(tipo_cuota_nombre, 'Sin tipo')"))
            ->orderByDesc('total_importe')
            ->get();

        $clasesTotales = Clase::query()
            ->whereBetween('fecha', [$inicioDate, $finDate])
            ->count();

        $clasesCanceladas = Clase::query()
            ->whereBetween('fecha', [$inicioDate, $finDate])
            ->where('estado', 'cancelada')
            ->count();

        $clasesConLista = Clase::query()
            ->whereBetween('fecha', [$inicioDate, $finDate])
            ->has('asistencias')
            ->count();

        if ($inicio->gt($hoy)) {
            $clasesSinLista = 0;
        } else {
            $finAnalisis = $fin->copy()->lt($hoy) ? $fin->copy() : $hoy->copy();

            $clasesSinLista = Clase::query()
                ->whereBetween('fecha', [$inicioDate, $finAnalisis->toDateString()])
                ->doesntHave('asistencias')
                ->count();
        }

        $asistenciasBase = Asistencia::query()
            ->whereHas('clase', function ($q) use ($inicioDate, $finDate) {
                $q->whereBetween('fecha', [$inicioDate, $finDate]);
            });

        $presentes = (clone $asistenciasBase)
            ->where('estado', 'presente')
            ->count();

        $ausentes = (clone $asistenciasBase)
            ->where('estado', 'ausente')
            ->count();

        $asistenciasTotal = $presentes + $ausentes;

        $porcentajePresencia = $asistenciasTotal > 0
            ? round(($presentes / $asistenciasTotal) * 100, 1)
            : null;

        $porcentajeClasesConLista = $clasesTotales > 0
            ? round(($clasesConLista / $clasesTotales) * 100, 1)
            : null;

        $gruposResumen = DB::table('grupos')
            ->leftJoin('clases', function ($join) use ($inicioDate, $finDate) {
                $join->on('grupos.id', '=', 'clases.grupo_id')
                    ->whereBetween('clases.fecha', [$inicioDate, $finDate]);
            })
            ->leftJoin('asistencias', 'asistencias.clase_id', '=', 'clases.id')
            ->select('grupos.id', 'grupos.nombre')
            ->selectRaw('COUNT(DISTINCT clases.id) as clases_total')
            ->selectRaw("COUNT(DISTINCT CASE WHEN clases.estado = 'cancelada' THEN clases.id END) as clases_canceladas")
            ->selectRaw("COALESCE(SUM(CASE WHEN asistencias.estado = 'presente' THEN 1 ELSE 0 END), 0) as presentes")
            ->selectRaw("COALESCE(SUM(CASE WHEN asistencias.estado = 'ausente' THEN 1 ELSE 0 END), 0) as ausentes")
            ->groupBy('grupos.id', 'grupos.nombre')
            ->havingRaw("COUNT(DISTINCT clases.id) > 0 OR COALESCE(SUM(CASE WHEN asistencias.estado IN ('presente','ausente') THEN 1 ELSE 0 END), 0) > 0")
            ->orderByDesc('clases_total')
            ->orderBy('grupos.nombre')
            ->get();

        $ultimosPagos = Pago::query()
            ->with('alumno')
            ->whereBetween('fecha_pago', [$inicioDate, $finDate])
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $ultimasPreinscripciones = Preinscripcion::query()
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return [
            'kpis' => [
                'alumnos_nuevos' => $alumnosNuevos,
                'alumnos_activos_cierre' => $alumnosActivosCierre,
                'preinscripciones_recibidas' => $preinscripcionesRecibidas,
                'preinscripciones_convertidas' => $preinscripcionesConvertidas,
                'ingresos' => $ingresos,
                'pagos_total' => $pagosTotal,
                'ticket_medio' => $ticketMedio,
                'clases_total' => $clasesTotales,
                'clases_canceladas' => $clasesCanceladas,
                'clases_con_lista' => $clasesConLista,
                'clases_sin_lista' => $clasesSinLista,
                'porcentaje_clases_con_lista' => $porcentajeClasesConLista,
                'presentes' => $presentes,
                'ausentes' => $ausentes,
                'asistencias_total' => $asistenciasTotal,
                'porcentaje_presencia' => $porcentajePresencia,
            ],
            'pagos_por_metodo' => $pagosPorMetodo,
            'pagos_por_tipo' => $pagosPorTipo,
            'grupos' => $gruposResumen,
            'ultimos_pagos' => $ultimosPagos,
            'ultimas_preinscripciones' => $ultimasPreinscripciones,
        ];
    }
}