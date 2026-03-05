@extends('layouts.panel')

@section('title', 'Pasar lista | Nova Unió')

@section('content')
@php
    $mesVolver = request('mes', now()->format('Y-m'));
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Pasar lista</h1>
        <p class="mt-1 panel-muted">
            {{ \Carbon\Carbon::parse($clase->fecha)->format('d/m/Y') }}
            · {{ substr($clase->hora_inicio,0,5) }} - {{ substr($clase->hora_fin,0,5) }}
            · {{ $clase->grupo->nombre }}
        </p>
    </div>

    <a href="{{ route('panel.calendario', ['mes' => $mesVolver]) }}" class="panel-icon-btn px-5 py-3">
        Volver
    </a>
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
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-lg font-semibold">Alumnos</div>
            <div class="text-sm panel-muted">Por defecto quedan como ausentes. Marca presentes y guarda.</div>
        </div>

        @if($clase->estado === 'cancelada')
            <span class="text-xs px-3 py-1 rounded-full"
                  style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                Clase cancelada
            </span>
        @endif

        @if($clase->asistencia_cerrada)
            <span class="text-xs px-3 py-1 rounded-full panel-muted"
                  style="background: rgb(var(--p-surface) / .10); border: 1px solid rgb(var(--p-border) / .18);">
                Asistencia cerrada
            </span>
        @endif
    </div>

    <form class="mt-5"
          method="POST"
          action="{{ route('panel.clases.asistencia', $clase) }}">
        @csrf

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Alumno</th>
                        <th class="py-2">Asistencia</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($alumnos as $alumno)
                        @php
                            $estado = $asistencias[$alumno->id] ?? 'ausente';
                        @endphp

                        <tr class="border-t panel-border">
                            <td class="py-3">
                                <div class="font-medium">
                                    {{ $alumno->apellidos }}, {{ $alumno->nombre }}
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center gap-4">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio"
                                               name="asistencias[{{ $alumno->id }}]"
                                               value="presente"
                                               @checked($estado === 'presente')
                                               @disabled($clase->asistencia_cerrada)>
                                        <span>Presente</span>
                                    </label>

                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio"
                                               name="asistencias[{{ $alumno->id }}]"
                                               value="ausente"
                                               @checked($estado === 'ausente')
                                               @disabled($clase->asistencia_cerrada)>
                                        <span>Ausente</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-6 panel-muted">
                                Este grupo no tiene alumnos en esta fecha.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            <button class="panel-btn px-6 py-3"
                    @disabled($clase->asistencia_cerrada || $alumnos->count() === 0)>
                Guardar asistencia
            </button>
        </div>
    </form>
</div>
@endsection