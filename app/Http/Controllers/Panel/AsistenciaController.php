<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Clase;
use App\Models\Grupo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $grupo_id = (string) $request->query('grupo_id', '');

        $desde = $request->query('desde');
        $hasta = $request->query('hasta');

        $base = now();

        $desdeC = $desde
            ? Carbon::parse($desde)->startOfDay()
            : $base->copy()->startOfMonth();

        $hastaC = $hasta
            ? Carbon::parse($hasta)->endOfDay()
            : $base->copy()->endOfMonth();

        if ($desdeC->gt($hastaC)) {
            [$desdeC, $hastaC] = [$hastaC, $desdeC];
        }

        $desdeStr = $desdeC->toDateString();
        $hastaStr = $hastaC->toDateString();

        $grupos = Grupo::orderBy('nombre')->get(['id', 'nombre']);

        $clases = Clase::query()
            ->with(['grupo:id,nombre'])
            ->whereBetween('fecha', [$desdeStr, $hastaStr])
            ->when($grupo_id !== '', fn ($q) => $q->where('grupo_id', $grupo_id))
            ->withCount([
                'asistencias as total' => fn ($q) => $q,
                'asistencias as presentes' => fn ($q) => $q->where('estado', 'presente'),
                'asistencias as ausentes' => fn ($q) => $q->where('estado', 'ausente'),
            ])
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio')
            ->paginate(20)
            ->withQueryString();

        return view('panel.asistencias.index', compact(
            'clases',
            'grupos',
            'grupo_id',
            'desdeStr',
            'hastaStr',
        ));
    }
}