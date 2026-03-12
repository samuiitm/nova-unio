@extends('layouts.panel')

@section('title', 'Grupos | Nova Unió')

@php
    $dias = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
        5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];
@endphp

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Grupos</h1>
            <p class="mt-1 panel-muted">Despliega un grupo para ver sus horarios.</p>
            <p class="mt-2 text-sm panel-muted">
                Las clases se generan automáticamente cada domingo para las 2 semanas siguientes.
            </p>
        </div>

        <a href="{{ route('panel.grupos.create') }}" class="panel-btn px-5 py-3">
            Crear grupo
        </a>
    </div>

    @if(session('ok'))
        <div class="mt-5 panel-card p-4">
            <div class="text-sm">{{ session('ok') }}</div>
        </div>
    @endif

    <div class="mt-5 panel-card p-5">
        <form method="GET" class="grid gap-3 sm:grid-cols-[1fr,200px,auto]">
            <input name="q" value="{{ $q }}" class="panel-input px-4 py-3"
                   placeholder="Buscar grupo...">

            <select name="estado" class="panel-input px-4 py-3">
                <option value="todos" @selected($estado==='todos')>Todos</option>
                <option value="activos" @selected($estado==='activos')>Activos</option>
                <option value="inactivos" @selected($estado==='inactivos')>Inactivos</option>
            </select>

            <button class="panel-btn px-5 py-3">Buscar</button>
        </form>

        <div class="mt-5 space-y-3" x-data="{ openId: null }">
            @forelse($grupos as $g)
                <div class="border panel-border rounded-2xl overflow-hidden"
                     style="background: rgb(var(--p-surface) / .06);">

                    <div class="px-4 py-4 flex items-center gap-3">
                        <button type="button"
                                class="panel-icon-btn h-10 w-10 flex items-center justify-center shrink-0"
                                @click="openId = (openId === {{ $g->id }} ? null : {{ $g->id }})"
                                aria-label="Desplegar horarios">
                            <svg class="h-5 w-5 opacity-80 transition-transform"
                                 :class="openId === {{ $g->id }} ? 'rotate-180' : 'rotate-0'"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-block h-3 w-3 rounded-full border panel-border"
                                    style="background: {{ $g->color_hex }};"></span>

                                <div class="font-semibold truncate">{{ $g->nombre }}</div>

                                @if($g->activo)
                                    <span class="text-xs px-3 py-1 rounded-full"
                                        style="background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);">
                                        Activo
                                    </span>
                                @else
                                    <span class="text-xs px-3 py-1 rounded-full panel-muted"
                                        style="background: rgb(var(--p-surface) / .10); border: 1px solid rgb(var(--p-border) / .18);">
                                        Inactivo
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-xs panel-muted">
                                Alumnos: {{ $g->alumnos_count ?? 0 }} · Horarios: {{ $g->horarios_count ?? 0 }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a class="panel-icon-btn px-4 py-2"
                               href="{{ route('panel.grupos.show', $g) }}">
                                Editar
                            </a>

                            <form method="POST" action="{{ route('panel.grupos.destroy', $g) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="panel-icon-btn px-4 py-2"
                                        onclick="return confirm('¿Seguro que quieres borrar este grupo? Se borrarán también sus horarios.')">
                                    Borrar
                                </button>
                            </form>
                        </div>
                    </div>

                    <div x-show="openId === {{ $g->id }}" x-collapse class="px-4 pb-4">
                        @if($g->programaciones->count() === 0)
                            <div class="panel-muted text-sm">Este grupo no tiene horarios todavía.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="text-left panel-muted">
                                        <tr>
                                            <th class="py-2">Día</th>
                                            <th class="py-2">Hora</th>
                                            <th class="py-2">Vigencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($g->programaciones as $p)
                                            <tr class="border-t panel-border">
                                                <td class="py-3">
                                                    {{ $dias[$p->dia_semana] ?? $p->dia_semana }}
                                                </td>
                                                <td class="py-3">
                                                    {{ substr($p->hora_inicio,0,5) }} - {{ substr($p->hora_fin,0,5) }}
                                                </td>
                                                <td class="py-3 panel-muted">
                                                    Desde {{ $p->vigente_desde ? $p->vigente_desde->format('d/m/Y') : '—' }}
                                                    ·
                                                    Hasta {{ $p->vigente_hasta ? $p->vigente_hasta->format('d/m/Y') : 'Sin fin' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-6 panel-muted">No hay grupos.</div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $grupos->links() }}
        </div>
    </div>
@endsection