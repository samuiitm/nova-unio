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
            $base = now()->startOfMonth();
            $mes = $base->format('Y-m');
        }

        $inicio = $base->copy()->startOfMonth();
        $fin = $base->copy()->endOfMonth();

        $clases = Clase::with('grupo')
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('fecha');

        return view('panel.calendario.index', compact('clases', 'inicio', 'fin', 'mes'));
    }
}