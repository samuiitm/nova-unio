@extends('layouts.panel')

@section('title', 'Resumen mensual | Nova Unió')

@section('content')
@php
    $tituloMes = ucfirst($inicio->locale('es')->translatedFormat('F Y'));
    $k = $resumen['kpis'];
@endphp

<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Resumen mensual</h1>
        <p class="mt-1 panel-muted">
            Informe general del mes de {{ $tituloMes }}.
        </p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('panel.informes.resumen.pdf', ['mes' => $mes]) }}" class="panel-btn px-5 py-3">
            Exportar PDF
        </a>
    </div>
</div>

@if(session('error'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('error') }}</div>
    </div>
@endif

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-[220px,auto]">
        <div>
            <label class="text-sm panel-muted">Mes</label>
            <input type="month" name="mes" value="{{ $mes }}" class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div class="flex items-end gap-3">
            <button class="panel-btn px-6 py-3">Ver resumen</button>
            <a href="{{ route('panel.informes.resumen') }}" class="panel-icon-btn px-6 py-3">Mes actual</a>
        </div>
    </form>
</div>

<div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Alumnos nuevos</div>
        <div class="mt-2 text-3xl font-semibold">{{ $k['alumnos_nuevos'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Activos al cierre</div>
        <div class="mt-2 text-3xl font-semibold">{{ $k['alumnos_activos_cierre'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Preinscripciones recibidas</div>
        <div class="mt-2 text-3xl font-semibold">{{ $k['preinscripciones_recibidas'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Preinscripciones convertidas</div>
        <div class="mt-2 text-3xl font-semibold">{{ $k['preinscripciones_convertidas'] }}</div>
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Ingresos cobrados</div>
        <div class="mt-2 text-3xl font-semibold">{{ number_format((float)$k['ingresos'], 2, ',', '.') }} €</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Pagos registrados</div>
        <div class="mt-2 text-3xl font-semibold">{{ $k['pagos_total'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Ticket medio</div>
        <div class="mt-2 text-3xl font-semibold">{{ number_format((float)$k['ticket_medio'], 2, ',', '.') }} €</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Clases del mes</div>
        <div class="mt-2 text-3xl font-semibold">{{ $k['clases_total'] }}</div>
    </div>
</div>

<div class="mt-5 grid gap-4 xl:grid-cols-2">
    <div class="panel-card p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Cobros por método</h2>
            <div class="text-sm panel-muted">{{ $tituloMes }}</div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Método</th>
                        <th class="py-2">Pagos</th>
                        <th class="py-2 text-right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resumen['pagos_por_metodo'] as $fila)
                        <tr class="border-t panel-border">
                            <td class="py-3">{{ ucfirst($fila->metodo) }}</td>
                            <td class="py-3">{{ (int) $fila->total_pagos }}</td>
                            <td class="py-3 text-right">{{ number_format((float)$fila->total_importe, 2, ',', '.') }} €</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 panel-muted">No hay pagos este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card p-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Cobros por tipo de cuota</h2>
            <div class="text-sm panel-muted">{{ $tituloMes }}</div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Tipo</th>
                        <th class="py-2">Pagos</th>
                        <th class="py-2 text-right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resumen['pagos_por_tipo'] as $fila)
                        <tr class="border-t panel-border">
                            <td class="py-3">{{ $fila->tipo }}</td>
                            <td class="py-3">{{ (int) $fila->total_pagos }}</td>
                            <td class="py-3 text-right">{{ number_format((float)$fila->total_importe, 2, ',', '.') }} €</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 panel-muted">No hay pagos este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-5 panel-card p-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h2 class="text-lg font-semibold">Clases y asistencias</h2>
            <p class="mt-1 text-sm panel-muted">Control mensual de clases, listas y asistencia.</p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border panel-border p-4">
            <div class="text-sm panel-muted">Clases canceladas</div>
            <div class="mt-2 text-2xl font-semibold">{{ $k['clases_canceladas'] }}</div>
        </div>

        <div class="rounded-2xl border panel-border p-4">
            <div class="text-sm panel-muted">Clases con lista</div>
            <div class="mt-2 text-2xl font-semibold">{{ $k['clases_con_lista'] }}</div>
            <div class="mt-1 text-xs panel-muted">
                {{ $k['porcentaje_clases_con_lista'] !== null ? number_format((float)$k['porcentaje_clases_con_lista'], 1, ',', '.') . '%' : '—' }}
            </div>
        </div>

        <div class="rounded-2xl border panel-border p-4">
            <div class="text-sm panel-muted">Clases sin lista</div>
            <div class="mt-2 text-2xl font-semibold">{{ $k['clases_sin_lista'] }}</div>
        </div>

        <div class="rounded-2xl border panel-border p-4">
            <div class="text-sm panel-muted">Presencia</div>
            <div class="mt-2 text-2xl font-semibold">
                {{ $k['porcentaje_presencia'] !== null ? number_format((float)$k['porcentaje_presencia'], 1, ',', '.') . '%' : '—' }}
            </div>
            <div class="mt-1 text-xs panel-muted">
                {{ $k['presentes'] }} presentes · {{ $k['ausentes'] }} ausentes
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Grupo</th>
                    <th class="py-2">Clases</th>
                    <th class="py-2">Canceladas</th>
                    <th class="py-2">Presentes</th>
                    <th class="py-2">Ausentes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resumen['grupos'] as $grupo)
                    <tr class="border-t panel-border">
                        <td class="py-3 font-medium">{{ $grupo->nombre }}</td>
                        <td class="py-3">{{ (int) $grupo->clases_total }}</td>
                        <td class="py-3">{{ (int) $grupo->clases_canceladas }}</td>
                        <td class="py-3">{{ (int) $grupo->presentes }}</td>
                        <td class="py-3">{{ (int) $grupo->ausentes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 panel-muted">No hay actividad de grupos este mes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5 grid gap-4 xl:grid-cols-2">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Últimos pagos del mes</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Fecha</th>
                        <th class="py-2">Alumno</th>
                        <th class="py-2">Tipo</th>
                        <th class="py-2 text-right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resumen['ultimos_pagos'] as $p)
                        <tr class="border-t panel-border">
                            <td class="py-3">{{ $p->fecha_pago?->format('d/m/Y') }}</td>
                            <td class="py-3">
                                {{ $p->alumno?->apellidos ? $p->alumno->apellidos . ', ' . $p->alumno->nombre : ($p->alumno->nombre ?? '—') }}
                            </td>
                            <td class="py-3">{{ $p->tipo_cuota_nombre ?: '—' }}</td>
                            <td class="py-3 text-right">{{ number_format((float)$p->importe, 2, ',', '.') }} €</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 panel-muted">No hay pagos este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Últimas preinscripciones del mes</h2>

        <div class="mt-4 space-y-3">
            @forelse($resumen['ultimas_preinscripciones'] as $pre)
                <div class="rounded-2xl border panel-border p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium">
                                {{ trim(($pre->nombre ?? '') . ' ' . ($pre->apellidos ?? '')) ?: 'Sin nombre' }}
                            </div>
                            <div class="mt-1 text-sm panel-muted">
                                {{ optional($pre->created_at)->format('d/m/Y H:i') ?: '—' }}
                            </div>
                        </div>

                        <div class="text-xs panel-muted">
                            {{ $pre->estado ?: 'nueva' }}
                        </div>
                    </div>

                    <div class="mt-3 grid gap-2 md:grid-cols-2 text-sm">
                        <div><span class="panel-muted">Modalidad:</span> {{ $pre->modalidad ?: '—' }}</div>
                        <div><span class="panel-muted">Nivel:</span> {{ $pre->nivel ?: '—' }}</div>
                        <div><span class="panel-muted">Objetivo:</span> {{ $pre->objetivo ?: '—' }}</div>
                        <div><span class="panel-muted">Teléfono:</span> {{ $pre->telefono ?: '—' }}</div>
                    </div>
                </div>
            @empty
                <div class="panel-muted text-sm">No hay preinscripciones este mes.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection