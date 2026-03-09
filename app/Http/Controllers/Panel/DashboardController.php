<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Clase;
use App\Models\Cuota;
use App\Models\Preinscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $rol = $this->resolverRol($user);
        $puedeGestionClub = in_array($rol, ['admin', 'entrenador_admin'], true);

        $hoy = now()->startOfDay();
        $manana = now()->copy()->addDay()->startOfDay();
        $inicioSemana = now()->copy()->startOfWeek();
        $finSemana = now()->copy()->endOfWeek();
        $inicioMes = now()->copy()->startOfMonth();
        $finMes = now()->copy()->endOfMonth();

        if ($puedeGestionClub) {
            return view('panel.dashboard', [
                'modo' => 'gestion',
                'rol' => $rol,
                'cards' => [
                    [
                        'titulo' => 'Alumnos activos',
                        'valor' => Alumno::where('activo', 1)->count(),
                        'link' => route('panel.alumnos.index'),
                        'icono' => 'alumnos',
                    ],
                    [
                        'titulo' => 'Cuotas pendientes',
                        'valor' => Cuota::where('estado', 'pendiente')->count(),
                        'link' => route('panel.pagos.pendientes'),
                        'icono' => 'cuotas',
                    ],
                    [
                        'titulo' => 'Clases programadas hoy',
                        'valor' => Clase::query()
                            ->whereDate('fecha', $hoy->toDateString())
                            ->where(function ($q) {
                                $q->whereNull('estado')
                                    ->orWhere('estado', '!=', 'cancelada');
                            })
                            ->count(),
                        'link' => route('panel.calendario'),
                        'icono' => 'calendario',
                    ],
                    [
                        'titulo' => 'Preinscripciones abiertas',
                        'valor' => Preinscripcion::query()
                            ->whereIn('estado', ['nueva', 'en_proceso'])
                            ->count(),
                        'link' => route('panel.preinscripciones.index'),
                        'icono' => 'preinscripciones',
                    ],
                ],
                'cardsSecundarias' => [
                    [
                        'titulo' => 'Asistencias esta semana',
                        'valor' => Asistencia::query()
                            ->whereHas('clase', function ($q) use ($inicioSemana, $finSemana) {
                                $q->whereBetween('fecha', [
                                    $inicioSemana->toDateString(),
                                    $finSemana->toDateString(),
                                ]);
                            })
                            ->count(),
                        'texto' => 'Registros de asistencia guardados',
                        'icono' => 'asistencias',
                    ],
                    [
                        'titulo' => 'Nuevos alumnos este mes',
                        'valor' => Alumno::query()
                            ->whereBetween('created_at', [$inicioMes, $finMes])
                            ->count(),
                        'texto' => 'Altas registradas en el mes actual',
                        'icono' => 'nuevos',
                    ],
                ],
                'cuotasPendientes' => Cuota::query()
                    ->with([
                        'alumno.gruposActivos' => function ($q) {
                            $q->orderBy('nombre');
                        },
                        'tipoCuota',
                    ])
                    ->where('estado', 'pendiente')
                    ->orderByDesc('created_at')
                    ->limit(6)
                    ->get(),
                'proximasClases' => Clase::query()
                    ->with('grupo')
                    ->whereDate('fecha', '>=', $hoy->toDateString())
                    ->where(function ($q) {
                        $q->whereNull('estado')
                            ->orWhere('estado', '!=', 'cancelada');
                    })
                    ->orderBy('fecha')
                    ->orderBy('hora_inicio')
                    ->limit(6)
                    ->get(),
                'preinscripcionesRecientes' => Preinscripcion::query()
                    ->whereIn('estado', ['nueva', 'en_proceso'])
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get(),
            ]);
        }

        return view('panel.dashboard', [
            'modo' => 'entrenador',
            'rol' => $rol,
            'cards' => [
                [
                    'titulo' => 'Clases hoy',
                    'valor' => Clase::query()
                        ->whereDate('fecha', $hoy->toDateString())
                        ->where(function ($q) {
                            $q->whereNull('estado')
                                ->orWhere('estado', '!=', 'cancelada');
                        })
                        ->count(),
                    'link' => route('panel.calendario'),
                    'icono' => 'calendario',
                ],
                [
                    'titulo' => 'Clases mañana',
                    'valor' => Clase::query()
                        ->whereDate('fecha', $manana->toDateString())
                        ->where(function ($q) {
                            $q->whereNull('estado')
                                ->orWhere('estado', '!=', 'cancelada');
                        })
                        ->count(),
                    'link' => route('panel.calendario', ['mes' => $manana->format('Y-m')]),
                    'icono' => 'calendario',
                ],
                [
                    'titulo' => 'Listas pendientes',
                    'valor' => Clase::query()
                        ->whereDate('fecha', '<=', $hoy->toDateString())
                        ->where(function ($q) {
                            $q->whereNull('estado')
                                ->orWhere('estado', '!=', 'cancelada');
                        })
                        ->doesntHave('asistencias')
                        ->count(),
                    'link' => route('panel.asistencias.index'),
                    'icono' => 'asistencias',
                ],
                [
                    'titulo' => 'Clases esta semana',
                    'valor' => Clase::query()
                        ->whereBetween('fecha', [
                            $inicioSemana->toDateString(),
                            $finSemana->toDateString(),
                        ])
                        ->where(function ($q) {
                            $q->whereNull('estado')
                                ->orWhere('estado', '!=', 'cancelada');
                        })
                        ->count(),
                    'link' => route('panel.calendario', ['mes' => $hoy->format('Y-m')]),
                    'icono' => 'clases',
                ],
            ],
            'cardsSecundarias' => [],
            'cuotasPendientes' => collect(),
            'proximasClases' => Clase::query()
                ->with('grupo')
                ->whereDate('fecha', '>=', $hoy->toDateString())
                ->where(function ($q) {
                    $q->whereNull('estado')
                        ->orWhere('estado', '!=', 'cancelada');
                })
                ->orderBy('fecha')
                ->orderBy('hora_inicio')
                ->limit(8)
                ->get(),
            'preinscripcionesRecientes' => collect(),
            'clasesSinLista' => Clase::query()
                ->with('grupo')
                ->whereDate('fecha', '<=', $hoy->toDateString())
                ->where(function ($q) {
                    $q->whereNull('estado')
                        ->orWhere('estado', '!=', 'cancelada');
                })
                ->doesntHave('asistencias')
                ->orderByDesc('fecha')
                ->orderBy('hora_inicio')
                ->limit(8)
                ->get(),
        ]);
    }

    private function resolverRol($user): ?string
    {
        if (!$user) {
            return null;
        }

        if (method_exists($user, 'rolEnum') && $user->rolEnum()) {
            return $user->rolEnum()->value;
        }

        if (isset($user->rol) && is_object($user->rol) && property_exists($user->rol, 'value')) {
            return $user->rol->value;
        }

        return is_string($user->rol ?? null) ? $user->rol : null;
    }
}