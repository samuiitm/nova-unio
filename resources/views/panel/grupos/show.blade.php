@extends('layouts.panel')

@section('title', 'Grupo | Nova Unió')

@php
    $dias = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
        5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];
@endphp

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">{{ $grupo->nombre }}</h1>
            <p class="mt-1 panel-muted">Gestión del grupo: alumnos y horarios.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('panel.grupos.edit', $grupo) }}" class="panel-btn px-5 py-3">Editar</a>
            <a href="{{ route('panel.grupos.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
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

    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <!-- Alumnos -->
        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">Alumnos del grupo</h2>
            <p class="mt-1 text-sm panel-muted">Asignar y dar de baja alumnos.</p>

            <form class="mt-4 grid gap-3 sm:grid-cols-[1fr,160px,auto]"
                  method="POST" action="{{ route('panel.grupos.alumnos.asignar', $grupo) }}">
                @csrf
                <select name="alumno_id" class="panel-input px-4 py-3" required>
                    <option value="">Selecciona un alumno...</option>
                    @foreach($alumnosDisponibles as $a)
                        <option value="{{ $a->id }}">{{ $a->apellidos }}, {{ $a->nombre }}</option>
                    @endforeach
                </select>

                <input type="date" name="fecha_alta" class="panel-input px-4 py-3"
                       value="{{ now()->format('Y-m-d') }}">

                <button class="panel-btn px-5 py-3">Asignar</button>
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left panel-muted">
                        <tr>
                            <th class="py-2">Alumno</th>
                            <th class="py-2">Alta</th>
                            <th class="py-2 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grupo->alumnosActivos as $a)
                            <tr class="border-t panel-border">
                                <td class="py-3">
                                    <div class="font-medium">{{ $a->apellidos }}, {{ $a->nombre }}</div>
                                </td>
                                <td class="py-3 panel-muted">
                                    {{ $a->pivot->fecha_alta ? \Carbon\Carbon::parse($a->pivot->fecha_alta)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="py-3 text-right">
                                    <form method="POST" action="{{ route('panel.grupos.alumnos.baja', [$grupo, $a]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="panel-icon-btn px-4 py-2"
                                                onclick="return confirm('¿Dar de baja al alumno en este grupo?')">
                                            Baja
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 panel-muted">No hay alumnos asignados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Horarios -->
        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">Horarios</h2>
            <p class="mt-1 text-sm panel-muted">Programación semanal del grupo.</p>

            <form class="mt-4 grid gap-3 sm:grid-cols-2"
                  method="POST" action="{{ route('panel.grupos.programaciones.store', $grupo) }}">
                @csrf

                <div>
                    <label class="text-sm font-medium">Día *</label>
                    <select name="dia_semana" class="mt-1 w-full panel-input px-4 py-3" required>
                        @foreach($dias as $k => $d)
                            <option value="{{ $k }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Vigente desde</label>
                    <input type="date" name="vigente_desde" class="mt-1 w-full panel-input px-4 py-3">
                </div>

                <div>
                    <label class="text-sm font-medium">Hora inicio *</label>
                    <input type="time" name="hora_inicio" class="mt-1 w-full panel-input px-4 py-3" required>
                </div>

                <div>
                    <label class="text-sm font-medium">Hora fin *</label>
                    <input type="time" name="hora_fin" class="mt-1 w-full panel-input px-4 py-3" required>
                </div>

                <div class="sm:col-span-2">
                    <label class="text-sm font-medium">Vigente hasta</label>
                    <input type="date" name="vigente_hasta" class="mt-1 w-full panel-input px-4 py-3">
                </div>

                <div class="sm:col-span-2">
                    <button class="panel-btn px-6 py-3">Añadir horario</button>
                </div>
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left panel-muted">
                        <tr>
                            <th class="py-2">Día</th>
                            <th class="py-2">Horario</th>
                            <th class="py-2">Vigencia</th>
                            <th class="py-2 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grupo->programaciones as $p)
                            <tr class="border-t panel-border">
                                <td class="py-3">{{ $dias[$p->dia_semana] ?? $p->dia_semana }}</td>
                                <td class="py-3">
                                    {{ substr($p->hora_inicio,0,5) }} - {{ substr($p->hora_fin,0,5) }}
                                </td>
                                <td class="py-3 panel-muted">
                                    {{ $p->vigente_desde ? $p->vigente_desde->format('d/m/Y') : '—' }}
                                    →
                                    {{ $p->vigente_hasta ? $p->vigente_hasta->format('d/m/Y') : '—' }}
                                </td>
                                <td class="py-3 text-right">
                                    <form method="POST" action="{{ route('panel.grupos.programaciones.destroy', [$grupo, $p]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="panel-icon-btn px-4 py-2"
                                                onclick="return confirm('¿Borrar este horario?')">
                                            Borrar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 panel-muted">No hay horarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('panel.grupos.horarios') }}" class="text-sm panel-muted hover:text-white">
                    Ver todos los horarios →
                </a>
            </div>
        </div>
    </div>
@endsection