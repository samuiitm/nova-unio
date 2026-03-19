@extends('layouts.panel')

@section('title', 'Clase | Nova Unió')

@section('content')
@php
    $mesVolver = $mesVolver ?? null;

    $fechaFmt = \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y');
    $horaIni = $clase->hora_inicio ? substr($clase->hora_inicio, 0, 5) : '—';
    $horaFin = $clase->hora_fin ? substr($clase->hora_fin, 0, 5) : '—';
@endphp

<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Clase</h1>
        <p class="mt-1 panel-muted">
            {{ $clase->grupo->nombre }} · {{ $fechaFmt }} · {{ $horaIni }}-{{ $horaFin }}
        </p>
    </div>

    <div class="flex gap-2 flex-wrap">
        @if($clase->estado === 'cancelada')
            <form method="POST"
                  action="{{ route('panel.clases.reactivar', ['clase' => $clase, 'mes' => $mesVolver]) }}">
                @csrf
                @method('PATCH')

                <button class="panel-btn px-5 py-3">
                    Reactivar clase
                </button>
            </form>
        @else
            <form method="POST"
                  action="{{ route('panel.clases.cancelar', ['clase' => $clase, 'mes' => $mesVolver]) }}"
                  onsubmit="return confirm('¿Seguro que quieres cancelar esta clase? Si tenía asistencias, se borrarán.');">
                @csrf
                @method('PATCH')

                <button class="panel-icon-btn px-5 py-3">
                    Cancelar clase
                </button>
            </form>
        @endif

        @if($mesVolver)
            <a href="{{ route('panel.calendario', ['mes' => $mesVolver]) }}" class="panel-icon-btn px-5 py-3">Volver</a>
        @else
            <a href="{{ route('panel.calendario') }}" class="panel-icon-btn px-5 py-3">Volver</a>
        @endif
    </div>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="mt-5 panel-card p-4">
        <div class="text-sm font-medium">Hay errores:</div>
        <ul class="mt-2 text-sm panel-muted list-disc pl-5">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-5 panel-card p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="text-lg font-semibold">Lista</div>
            <div class="text-sm panel-muted">
                Marca el estado de cada alumno para esta clase.
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($estadoVisual === 'cancelada')
                <span class="text-xs px-3 py-1 rounded-full"
                      style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                    Cancelada
                </span>
            @elseif($estadoVisual === 'cerrada')
                <span class="text-xs px-3 py-1 rounded-full"
                      style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                    Cerrada
                </span>
            @elseif($estadoVisual === 'sin_lista_bloqueada')
                <span class="text-xs px-3 py-1 rounded-full"
                      style="background: rgb(255 180 80 / .08); color: rgb(255 205 140 / .85); border: 1px solid rgb(255 180 80 / .18); opacity:.85;">
                    Sin lista (bloq.)
                </span>
            @elseif($estadoVisual === 'sin_lista')
                <span class="text-xs px-3 py-1 rounded-full"
                      style="background: rgb(255 180 80 / .12); color: rgb(255 205 140); border: 1px solid rgb(255 180 80 / .22);">
                    Sin lista
                </span>
            @elseif($estadoVisual === 'pasada')
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
        </div>
    </div>

    @if($bloqueada)
        <div class="mt-4 panel-card p-4">
            <div class="text-sm">
                Esta clase está <span class="font-semibold">bloqueada</span> y no se puede modificar la asistencia.
            </div>
            <div class="mt-1 text-sm panel-muted">
                @if($estadoVisual === 'sin_lista_bloqueada')
                    Han pasado 2 días sin pasar lista.
                @elseif($estadoVisual === 'cerrada')
                    La asistencia está cerrada.
                @elseif($estadoVisual === 'cancelada')
                    La clase está cancelada.
                @endif
            </div>
        </div>
    @elseif($estadoVisual === 'sin_lista')
        <div class="mt-4 panel-card p-4">
            <div class="text-sm">
                Aviso: esta clase ya es de hace 1 día y aún no se ha pasado lista.
            </div>
            <div class="mt-1 text-sm panel-muted">
                Si pasan 2 días sin lista, se bloqueará.
            </div>
        </div>
    @endif

    <form class="mt-5" method="POST" action="{{ route('panel.clases.asistencia', $clase) }}">
        @csrf

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Alumno</th>
                        <th class="py-2">Estado cuota</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2">Estado actual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alumnos as $a)
                        @php
                            $as = $asistencias->get($a->id);
                            $estado = $as?->estado;
                            $estadoCuota = $a->estado_cuota_clase ?? null;
                        @endphp
                        <tr class="border-t panel-border">
                            <td class="py-3">
                                <div class="font-medium">{{ $a->apellidos }}, {{ $a->nombre }}</div>
                            </td>

                            <td class="py-3">
                                @if($estadoCuota)
                                    @php
                                        $estiloCuota = match($estadoCuota['clave']) {
                                            'al_dia' => 'background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);',
                                            'pendiente' => 'background: rgb(255 180 0 / .12); color: rgb(255 210 120); border: 1px solid rgb(255 180 0 / .22);',
                                            default => 'background: rgb(255 80 120 / .12); color: rgb(255 150 170); border: 1px solid rgb(255 80 120 / .22);',
                                        };
                                    @endphp

                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                        style="{{ $estiloCuota }}">
                                        {{ $estadoCuota['texto'] }}
                                    </div>
                                @else
                                    <span class="panel-muted">—</span>
                                @endif
                            </td>

                            <td class="py-3">
                                <div class="flex items-center gap-4">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio"
                                            name="asistencias[{{ $a->id }}]"
                                            value="presente"
                                            @checked($estado === 'presente')
                                            @disabled($bloqueada)>
                                        <span>Presente</span>
                                    </label>

                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio"
                                            name="asistencias[{{ $a->id }}]"
                                            value="ausente"
                                            @checked($estado === 'ausente')
                                            @disabled($bloqueada)>
                                        <span>Ausente</span>
                                    </label>
                                </div>
                            </td>

                            <td class="py-3 panel-muted">
                                @if($estado === 'presente')
                                    Presente
                                @elseif($estado === 'ausente')
                                    Ausente
                                @else
                                    Sin guardar
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 panel-muted">No hay alumnos para esta clase.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <div class="text-xs panel-muted">
                Alumnos: {{ $alumnos->count() }} · Guardadas: {{ (int) $totalAsistencias }}
            </div>

            <div class="flex gap-2">
                @if(!$bloqueada)
                    <button class="panel-btn px-6 py-3">
                        Guardar asistencia
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection