<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Clase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index(Request $request)
    {
        $mes = (string) $request->query('mes', now()->format('Y-m'));

        try {
            $base = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        } catch (\Throwable $e) {
            $mes = now()->format('Y-m');
            $base = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        }

        $inicio = $base->copy()->startOfMonth();
        $fin = $base->copy()->endOfMonth();

        // Para que el calendario pinte semanas completas
        $inicioCal = $inicio->copy()->startOfWeek(Carbon::MONDAY);
        $finCal = $fin->copy()->endOfWeek(Carbon::SUNDAY);

        $clases = Clase::query()
            ->with(['grupo:id,nombre'])
            ->whereBetween('fecha', [$inicioCal->toDateString(), $finCal->toDateString()])
            ->withCount([
                'asistencias as asistencias_total'
            ])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('fecha');

        return view('panel.calendario.index', compact('mes', 'inicio', 'fin', 'clases'));
    }
}