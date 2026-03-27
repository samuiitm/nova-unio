@extends('layouts.panel')

@section('title', 'Asistencias | Nova Unió')

@section('content')
@php
    $limiteSinLista = now()->subDay()->startOfDay();   // ayer
    $limiteBloqueo  = now()->subDays(2)->startOfDay(); // hace 2 días
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Asistencias</h1>
        <p class="mt-1 panel-muted">Historial por clases. Se ve si falta pasar lista o si está bloqueada.</p>
    </div>
</div>

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-4">
        <div>
            <label class="text-sm panel-muted">Grupo</label>
            <select name="grupo_id" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Todos</option>
                @foreach($grupos as $g)
                    <option value="{{ $g->id }}" @selected((string)$grupo_id === (string)$g->id)>{{ $g->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm panel-muted">Desde</label>
            <input type="date" name="desde" value="{{ $desdeStr }}" class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div>
            <label class="text-sm panel-muted">Hasta</label>
            <input type="date" name="hasta" value="{{ $hastaStr }}" class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div class="flex items-end gap-2">
            <button class="panel-btn px-6 py-3 w-full">Filtrar</button>
        </div>
    </form>
</div>

<div class="mt-5 panel-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Fecha</th>
                    <th class="py-2">Hora</th>
                    <th class="py-2">Grupo</th>
                    <th class="py-2">Presentes</th>
                    <th class="py-2">Ausentes</th>
                    <th class="py-2">Estado</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody class="align-top">
                @forelse($clases as $c)
                    @php
                        $fecha = \Carbon\Carbon::parse($c->fecha)->startOfDay();
                        $horaIni = $c->hora_inicio ? substr($c->hora_inicio,0,5) : '—';
                        $horaFin = $c->hora_fin ? substr($c->hora_fin,0,5) : '—';

                        $total = (int) $c->total;

                        $esCancelada = ($c->estado ?? null) === 'cancelada';
                        $cerradaManual = (bool) ($c->asistencia_cerrada ?? false);

                        $bloqueadaSinLista = !$esCancelada && !$cerradaManual && $fecha->lte($limiteBloqueo) && $total === 0;
                        $sinLista = !$esCancelada && !$cerradaManual && $fecha->lte($limiteSinLista) && $total === 0 && !$bloqueadaSinLista;

                        $pasada = !$esCancelada && !$cerradaManual && $total > 0;
                    @endphp

                    <tr class="border-t panel-border">
                        <td class="py-3">
                            <div class="font-medium">{{ $fecha->format('d/m/Y') }}</div>
                        </td>

                        <td class="py-3">
                            {{ $horaIni }} - {{ $horaFin }}
                        </td>

                        <td class="py-3">
                            {{ $c->grupo->nombre ?? '—' }}
                        </td>

                        <td class="py-3">
                            <span class="font-semibold">{{ (int)$c->presentes }}</span>
                            <span class="panel-muted">/ {{ $total }}</span>
                        </td>

                        <td class="py-3">
                            {{ (int)$c->ausentes }}
                        </td>

                        <td class="py-3">
                            @if($esCancelada)
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                                    Cancelada
                                </span>
                            @elseif($cerradaManual)
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                                    Cerrada
                                </span>
                            @elseif($bloqueadaSinLista)
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(255 180 80 / .08); color: rgb(255 205 140 / .85); border: 1px solid rgb(255 180 80 / .18); opacity:.85;">
                                    Sin lista (bloq.)
                                </span>
                            @elseif($sinLista)
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(255 180 80 / .12); color: rgb(255 205 140); border: 1px solid rgb(255 180 80 / .22);">
                                    Sin lista
                                </span>
                            @elseif($pasada)
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);">
                                    Pasada
                                </span>
                            @else
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);">
                                    Abierta
                                </span>
                            @endif
                        </td>

                        <td class="py-3 text-right whitespace-nowrap">
                            <a class="panel-icon-btn px-4 py-2 inline-flex items-center"
                               href="{{ route('panel.clases.show', $c) }}">
                                Ver clase
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 panel-muted">No hay clases en este rango.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5 flex items-center justify-between gap-3">
        <div class="text-xs panel-muted">
            Mostrando {{ $clases->firstItem() ?? 0 }}-{{ $clases->lastItem() ?? 0 }} de {{ $clases->total() }} clases
        </div>
        <div>
            {{ $clases->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@endsection