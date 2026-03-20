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
                    ->orWhereDate('alumno_grupo.fecha_baja', '>', $clase->fecha);
            })
            ->where(function ($q) use ($clase) {
                $q->where(function ($w) {
                    $w->whereNull('alumnos.fecha_baja')
                        ->where('alumnos.activo', 1);
                })->orWhereDate('alumnos.fecha_baja', '>', $clase->fecha);
            })
            ->with([
                'cuotas' => function ($q) {
                    $q->where('estado', '!=', 'anulada')
                        ->with('tipoCuota')
                        ->orderByDesc('fecha_fin')
                        ->orderByDesc('id');
                },
            ])
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();
    }

    private function estadoCuotaAlumnoEnFecha($alumno, string $fechaClase): array
    {
        $fechaClase = Carbon::parse($fechaClase)->toDateString();

        $cuotaVigente = $alumno->cuotas->first(function ($cuota) use ($fechaClase) {
            if ($cuota->estado !== 'pagada') {
                return false;
            }

            $inicioOk = !$cuota->fecha_inicio || $cuota->fecha_inicio->toDateString() <= $fechaClase;
            $finOk = !$cuota->fecha_fin || $cuota->fecha_fin->toDateString() >= $fechaClase;

            return $inicioOk && $finOk;
        });

        if ($cuotaVigente) {
            return [
                'clave' => 'al_dia',
                'texto' => 'Al día',
                'detalle' => $cuotaVigente->tipoCuota?->nombre,
            ];
        }

        $cuotaPendiente = $alumno->cuotas->first(fn ($cuota) => $cuota->estado === 'pendiente');

        if ($cuotaPendiente) {
            return [
                'clave' => 'pendiente',
                'texto' => 'Pendiente',
                'detalle' => $cuotaPendiente->tipoCuota?->nombre,
            ];
        }

        $tuvoPagada = $alumno->cuotas->contains(fn ($cuota) => $cuota->estado === 'pagada');

        if ($tuvoPagada) {
            return [
                'clave' => 'no_al_dia',
                'texto' => 'No al día',
                'detalle' => 'Cuota vencida',
            ];
        }

        return [
            'clave' => 'no_al_dia',
            'texto' => 'No al día',
            'detalle' => 'Sin cuota',
        ];
    }

    private function estadoVisual(Clase $clase, int $totalAsistencias): array
    {
        $fecha = Carbon::parse($clase->fecha)->startOfDay();

        $limiteSinLista = now()->subDay()->startOfDay();
        $limiteBloqueo = now()->subDays(2)->startOfDay();

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

        $alumnos = $this->alumnosDelGrupoEnFecha($clase)->map(function ($alumno) use ($clase) {
            $alumno->estado_cuota_clase = $this->estadoCuotaAlumnoEnFecha($alumno, $clase->fecha);
            return $alumno;
        });

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
            'asistencias' => ['nullable', 'array'],
            'asistencias.*' => ['nullable', 'in:presente,ausente'],
        ]);

        $asistencias = collect($data['asistencias'] ?? []);

        DB::transaction(function () use ($clase, $alumnoIds, $asistencias) {
            Asistencia::where('clase_id', $clase->id)
                ->whereNotIn('alumno_id', $alumnoIds)
                ->delete();

            foreach ($alumnoIds as $alumnoId) {
                $estado = $asistencias->get((string) $alumnoId, 'ausente');

                if (!in_array($estado, ['presente', 'ausente'], true)) {
                    $estado = 'ausente';
                }

                Asistencia::updateOrCreate(
                    ['clase_id' => $clase->id, 'alumno_id' => (int) $alumnoId],
                    ['estado' => $estado]
                );
            }
        });

        return back()->with('ok', 'Asistencia guardada.');
    }

    public function cancelar(Request $request, Clase $clase)
    {
        if ($clase->estado === 'cancelada') {
            return redirect()
                ->route('panel.clases.show', array_filter([
                    'clase' => $clase,
                    'mes' => $request->query('mes'),
                ], fn ($v) => $v !== null && $v !== ''))
                ->with('ok', 'La clase ya estaba cancelada.');
        }

        DB::transaction(function () use ($clase) {
            Asistencia::where('clase_id', $clase->id)->delete();

            $clase->update([
                'estado' => 'cancelada',
                'asistencia_cerrada' => true,
            ]);
        });

        return redirect()
            ->route('panel.clases.show', array_filter([
                'clase' => $clase,
                'mes' => $request->query('mes'),
            ], fn ($v) => $v !== null && $v !== ''))
            ->with('ok', 'Clase cancelada correctamente.');
    }

    public function reactivar(Request $request, Clase $clase)
    {
        if ($clase->estado !== 'cancelada') {
            return redirect()
                ->route('panel.clases.show', array_filter([
                    'clase' => $clase,
                    'mes' => $request->query('mes'),
                ], fn ($v) => $v !== null && $v !== ''))
                ->with('ok', 'La clase ya estaba activa.');
        }

        $clase->update([
            'estado' => 'programada',
            'asistencia_cerrada' => false,
        ]);

        return redirect()
            ->route('panel.clases.show', array_filter([
                'clase' => $clase,
                'mes' => $request->query('mes'),
            ], fn ($v) => $v !== null && $v !== ''))
            ->with('ok', 'Clase reactivada correctamente.');
    }
}