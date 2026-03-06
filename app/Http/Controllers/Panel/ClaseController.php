<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Clase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaseController extends Controller
{
    private function alumnosDelGrupoEnFecha(Clase $clase)
    {
        return $clase->grupo->alumnos()
            ->wherePivot('fecha_alta', '<=', $clase->fecha)
            ->where(function ($q) use ($clase) {
                $q->whereNull('alumno_grupo.fecha_baja')
                  ->orWhere('alumno_grupo.fecha_baja', '>=', $clase->fecha);
            })
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();
    }

    private function estadoVisual(Clase $clase, int $totalAsistencias): array
    {
        $fecha = Carbon::parse($clase->fecha)->startOfDay();

        $limiteSinLista = now()->subDay()->startOfDay();    // ayer
        $limiteBloqueo  = now()->subDays(2)->startOfDay();  // hace 2 días

        $esCancelada = ($clase->estado ?? null) === 'cancelada';
        $cerradaManual = (bool) ($clase->asistencia_cerrada ?? false);

        $bloqueadaSinLista = !$esCancelada && !$cerradaManual && $fecha->lte($limiteBloqueo) && $totalAsistencias === 0;
        $sinLista = !$esCancelada && !$cerradaManual && $fecha->lte($limiteSinLista) && $totalAsistencias === 0 && !$bloqueadaSinLista;

        if ($esCancelada) return ['cancelada', true];
        if ($cerradaManual) return ['cerrada', true];
        if ($bloqueadaSinLista) return ['sin_lista_bloqueada', true];
        if ($sinLista) return ['sin_lista', false];
        if ($totalAsistencias > 0) return ['pasada', false];

        return ['abierta', false];
    }

    public function show(Request $request, Clase $clase)
    {
        $clase->load('grupo');

        $alumnos = $this->alumnosDelGrupoEnFecha($clase);

        $asistencias = Asistencia::where('clase_id', $clase->id)
            ->get()
            ->keyBy('alumno_id');

        $totalAsistencias = $asistencias->count();

        [$estadoVisual, $bloqueada] = $this->estadoVisual($clase, $totalAsistencias);

        $mesVolver = $request->query('mes');

        return view('panel.clases.show', compact(
            'clase',
            'alumnos',
            'asistencias',
            'totalAsistencias',
            'estadoVisual',
            'bloqueada',
            'mesVolver'
        ));
    }

    public function guardarAsistencia(Request $request, Clase $clase)
    {
        $clase->load('grupo');

        $alumnos = $this->alumnosDelGrupoEnFecha($clase);
        $alumnoIds = $alumnos->pluck('id');

        $totalExistentes = Asistencia::where('clase_id', $clase->id)->count();
        [$estadoVisual, $bloqueada] = $this->estadoVisual($clase, $totalExistentes);

        if ($bloqueada) {
            return back()->with('ok', 'No se puede guardar: la clase está bloqueada.');
        }

        $data = $request->validate([
            'presentes' => ['nullable', 'array'],
            'presentes.*' => ['integer'],
        ]);

        $presentes = collect($data['presentes'] ?? [])
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($id) => $alumnoIds->contains($id))
            ->values();

        DB::transaction(function () use ($clase, $alumnoIds, $presentes) {

            // por si acaso: borrar asistencias de alumnos que ya no tocan
            Asistencia::where('clase_id', $clase->id)
                ->whereNotIn('alumno_id', $alumnoIds)
                ->delete();

            foreach ($alumnoIds as $alumnoId) {
                $estado = $presentes->contains((int) $alumnoId) ? 'presente' : 'ausente';

                Asistencia::updateOrCreate(
                    ['clase_id' => $clase->id, 'alumno_id' => (int) $alumnoId],
                    ['estado' => $estado]
                );
            }
        });

        return back()->with('ok', 'Asistencia guardada.');
    }
}