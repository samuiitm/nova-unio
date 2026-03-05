@extends('layouts.panel')

@section('title', 'Asistencias | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Asistencias</h1>
        <p class="mt-1 panel-muted">Historial general con filtros.</p>
    </div>

    <a href="{{ route('panel.asistencias.index') }}" class="panel-icon-btn px-5 py-3">
        Limpiar filtros
    </a>
</div>

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-6">
        <div class="lg:col-span-2">
            <label class="text-sm panel-muted">Buscar alumno</label>
            <input name="q" value="{{ $q }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="Nombre o apellidos">
        </div>

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
            <label class="text-sm panel-muted">Estado</label>
            <select name="estado" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Todos</option>
                <option value="presente" @selected($estado==='presente')>Presente</option>
                <option value="ausente" @selected($estado==='ausente')>Ausente</option>
            </select>
        </div>

        <div>
            <label class="text-sm panel-muted">Desde</label>
            <input type="date" name="desde" value="{{ $desde }}" class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div>
            <label class="text-sm panel-muted">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta }}" class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div class="lg:col-span-6 flex items-center justify-between gap-3 mt-2">
            <div class="text-sm panel-muted">
                @php
                    $total = (int)($totales->total ?? 0);
                    $pres = (int)($totales->presentes ?? 0);
                    $aus  = (int)($totales->ausentes ?? 0);
                    $pct  = $total > 0 ? round(($pres / $total) * 100) : 0;
                @endphp
                Total: <span class="font-semibold">{{ $total }}</span> ·
                Presentes: <span class="font-semibold">{{ $pres }}</span> ·
                Ausentes: <span class="font-semibold">{{ $aus }}</span> ·
                % Presencia: <span class="font-semibold">{{ $pct }}%</span>
            </div>

            <button class="panel-btn px-6 py-3">Filtrar</button>
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
                    <th class="py-2">Alumno</th>
                    <th class="py-2">Estado</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($asistencias as $a)
                    <tr class="border-t panel-border">
                        <td class="py-3">
                            {{ \Carbon\Carbon::parse($a->clase->fecha)->format('d/m/Y') }}
                        </td>
                        <td class="py-3">
                            {{ substr($a->clase->hora_inicio,0,5) }}
                        </td>
                        <td class="py-3">
                            {{ $a->clase->grupo->nombre }}
                        </td>
                        <td class="py-3">
                            {{ $a->alumno->apellidos }}, {{ $a->alumno->nombre }}
                        </td>
                        <td class="py-3">
                            @if($a->estado === 'presente')
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);">
                                    Presente
                                </span>
                            @else
                                <span class="text-xs px-3 py-1 rounded-full panel-muted"
                                      style="background: rgb(var(--p-surface) / .10); border: 1px solid rgb(var(--p-border) / .18);">
                                    Ausente
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2">
                                @if(\Illuminate\Support\Facades\Route::has('panel.clases.show'))
                                    <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.clases.show', $a->clase_id) }}">
                                        Ver clase
                                    </a>
                                @endif

                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.asistencias.alumno', $a->alumno_id) }}">
                                    Ver alumno
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 panel-muted">
                            No hay asistencias con esos filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $asistencias->links() }}
    </div>
</div>
@endsection