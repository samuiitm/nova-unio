@extends('layouts.panel')

@section('title', 'Grupos | Nova Unió')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Grupos</h1>
            <p class="mt-1 panel-muted">Crea grupos y gestiona horarios.</p>
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

        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Grupo</th>
                        <th class="py-2">Alumnos</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grupos as $g)
                        <tr class="border-t panel-border">
                            <td class="py-3">
                                <div class="font-medium">{{ $g->nombre }}</div>
                                <div class="text-xs panel-muted">ID: {{ $g->id }}</div>
                            </td>

                            <td class="py-3">
                                <span class="text-sm">{{ $g->alumnos_count ?? 0 }}</span>
                            </td>

                            <td class="py-3">
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
                            </td>

                            <td class="py-3 text-right whitespace-nowrap">
                                <a class="panel-icon-btn px-4 py-2 inline-flex items-center"
                                   href="{{ route('panel.grupos.show', $g) }}">Ver</a>

                                <a class="panel-icon-btn px-4 py-2 inline-flex items-center ml-2"
                                   href="{{ route('panel.grupos.edit', $g) }}">Editar</a>

                                <form method="POST" action="{{ route('panel.grupos.destroy', $g) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="panel-icon-btn px-4 py-2 inline-flex items-center ml-2"
                                            onclick="return confirm('¿Seguro que quieres borrar este grupo? Se borrarán también sus horarios.')">
                                        Borrar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 panel-muted">No hay grupos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $grupos->links() }}
        </div>
    </div>
@endsection