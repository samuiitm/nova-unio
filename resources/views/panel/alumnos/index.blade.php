@extends('layouts.panel')

@section('title', 'Listado de alumnos | Nova Unió')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Listado de alumnos</h1>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                @php
                    $base = ['q' => $q, 'orden' => $orden];
                @endphp

                <a href="{{ route('panel.alumnos.index', array_merge($base, ['estado' => 'todos'])) }}"
                   class="panel-icon-btn px-4 py-2 {{ $estado==='todos' ? 'bg-white/10' : '' }}">
                    Todos
                </a>

                <a href="{{ route('panel.alumnos.index', array_merge($base, ['estado' => 'activos'])) }}"
                   class="panel-icon-btn px-4 py-2 {{ $estado==='activos' ? 'bg-white/10' : '' }}">
                    Activo
                </a>

                <a href="{{ route('panel.alumnos.index', array_merge($base, ['estado' => 'inactivos'])) }}"
                   class="panel-icon-btn px-4 py-2 {{ $estado==='inactivos' ? 'bg-white/10' : '' }}">
                    Inactivo
                </a>

                <select class="panel-input px-4 py-2 opacity-60" disabled>
                    <option>Todos los grupos</option>
                </select>

                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="estado" value="{{ $estado }}">
                    <input type="hidden" name="q" value="{{ $q }}">

                    <select name="orden" class="panel-input px-4 py-2" onchange="this.form.submit()">
                        <option value="reciente" @selected($orden==='reciente')>Ordenar por: Más reciente</option>
                        <option value="nombre" @selected($orden==='nombre')>Ordenar por: Nombre</option>
                    </select>
                </form>
            </div>

            <div class="mt-3 text-sm panel-muted">
                +{{ $nuevosMes }} alumnos nuevos este mes
            </div>
        </div>

        <a href="{{ route('panel.alumnos.create') }}" class="panel-btn px-5 py-3">
            Crear alumno
        </a>
    </div>

    @if(session('ok'))
        <div class="mt-5 panel-card p-4">
            <div class="text-sm">{{ session('ok') }}</div>
        </div>
    @endif

    <div class="mt-5 panel-card p-5">
        <form method="GET" class="relative">
            <input type="hidden" name="estado" value="{{ $estado }}">
            <input type="hidden" name="orden" value="{{ $orden }}">

            <input name="q" value="{{ $q }}"
                   class="panel-input w-full pl-11 pr-11 py-3"
                   placeholder="Buscar alumno...">

            <div class="absolute left-3 top-1/2 -translate-y-1/2 opacity-60">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <button class="absolute right-3 top-1/2 -translate-y-1/2 opacity-70" aria-label="Buscar">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 18a8 8 0 1 1 5.3-14L21 9"/>
                </svg>
            </button>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Alumno</th>
                        <th class="py-2">Grupo</th>
                        <th class="py-2">Teléfono</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2">Cuota</th>
                        <th class="py-2">Estado cuota</th>
                        <th class="py-2 text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($alumnos as $a)
                        @php
                            $grupos = $a->gruposActivos ?? collect();
                            $cuota = $a->cuotaActual;

                            $hoy = now()->toDateString();

                            $tituloCuota = $cuota?->tipoCuota?->nombre ?? '—';

                            $estadoCuota = 'sin_cuota';

                            if ($cuota) {
                                if ($cuota->estado === 'pendiente') {
                                    $estadoCuota = 'pendiente';
                                } elseif ($cuota->estado === 'pagada') {
                                    if ($cuota->fecha_fin && $cuota->fecha_fin->toDateString() < $hoy) {
                                        $estadoCuota = 'vencida';
                                    } else {
                                        $estadoCuota = 'vigente';
                                    }
                                }
                            }
                        @endphp

                        <tr class="border-t panel-border items-center">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $a->foto_url }}"
                                        alt="Foto de {{ $a->nombre }}"
                                        class="h-11 w-11 rounded-xl object-cover border panel-border shrink-0"
                                    >

                                    <div>
                                        <div class="font-medium">{{ $a->nombre }} {{ $a->apellidos }}</div>
                                        <div class="text-xs panel-muted">
                                            {{ $a->email ?: '—' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                @if($grupos->isEmpty())
                                    <span class="panel-muted">—</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($grupos as $g)
                                            <span class="text-xs px-3 py-1 rounded-full"
                                                  style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                                                {{ $g->nombre }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td class="py-3">
                                {{ $a->telefono ?: '—' }}
                            </td>

                            <td class="py-3">
                                @if($a->activo)
                                    <span class="text-xs px-3 py-1 rounded-full"
                                          style="background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);">
                                        Activo
                                    </span>
                                @else
                                    <span class="text-xs px-3 py-1 rounded-full"
                                            style="background: rgb(255 77 77 / .12); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 77 77 / .10);">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="py-3">
                                <span class="panel-muted">{{ $tituloCuota }}</span>
                            </td>

                            <td class="py-3">
                                @if($estadoCuota === 'vigente')
                                    <span class="text-xs px-3 py-1 rounded-full"
                                          style="background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);">
                                        Vigente
                                    </span>
                                @elseif($estadoCuota === 'pendiente')
                                    <span class="text-xs px-3 py-1 rounded-full"
                                          style="background: rgb(255 180 80 / .12); color: rgb(255 205 140); border: 1px solid rgb(255 180 80 / .22);">
                                        Pendiente
                                    </span>
                                @elseif($estadoCuota === 'vencida')
                                    <span class="text-xs px-3 py-1 rounded-full"
                                          style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                                        Vencida
                                    </span>
                                @else
                                    <span class="text-xs px-3 py-1 rounded-full"
                                          style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                                        Sin cuota
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 text-right whitespace-nowrap">
                                <a class="panel-icon-btn px-4 py-2 inline-flex items-center"
                                   href="{{ route('panel.alumnos.show', $a) }}">Ver</a>

                                <a class="panel-icon-btn px-4 py-2 inline-flex items-center ml-2"
                                   href="{{ route('panel.alumnos.edit', $a) }}">Editar</a>

                                @if($a->activo)
                                    <form method="POST" action="{{ route('panel.alumnos.baja', $a) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="panel-icon-btn px-4 py-2 inline-flex items-center ml-2"
                                                onclick="return confirm('¿Dar de baja a este alumno?')">
                                            Baja
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('panel.alumnos.activar', $a) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="panel-icon-btn px-4 py-2 inline-flex items-center ml-2">
                                            Activar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 panel-muted">No hay alumnos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <div class="text-xs panel-muted">
                Mostrando {{ $alumnos->firstItem() ?? 0 }}-{{ $alumnos->lastItem() ?? 0 }} de {{ $alumnos->total() }} resultados
            </div>
            <div>
                {{ $alumnos->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection