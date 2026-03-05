@extends('layouts.panel')

@section('title', 'Cuotas vencidas | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Cuotas vencidas</h1>
        <p class="mt-1 panel-muted">Cuotas pendientes cuyo periodo ya terminó.</p>
    </div>

    <a href="{{ route('panel.pagos.vencidas') }}" class="panel-icon-btn px-5 py-3">Limpiar</a>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-3">
        <div>
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

        <div class="flex items-end">
            <button class="panel-btn px-6 py-3 w-full">Filtrar</button>
        </div>
    </form>
</div>

<div class="mt-5 panel-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Alumno</th>
                    <th class="py-2">Periodo</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2">Tipo</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuotasVencidas as $c)
                    <tr class="border-t panel-border">
                        <td class="py-3">{{ $c->alumno->apellidos }}, {{ $c->alumno->nombre }}</td>
                        <td class="py-3">
                            {{ $c->fecha_inicio?->format('d/m/Y') }} - {{ $c->fecha_fin?->format('d/m/Y') }}
                        </td>
                        <td class="py-3">{{ number_format((float)$c->importe, 2, ',', '.') }} €</td>
                        <td class="py-3">{{ $c->tipoCuota?->nombre ?? '—' }}</td>
                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.cuotas.cobrar', $c) }}">
                                    Cobrar
                                </a>
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.alumnos.show', $c->alumno) }}">
                                    Ver alumno
                                </a>
                                <form method="POST" action="{{ route('panel.pagos.cuotas.anular', $c) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="panel-icon-btn px-4 py-2"
                                            onclick="return confirm('¿Anular esta cuota?')">
                                        Anular
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 panel-muted">No hay cuotas vencidas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cuotasVencidas->links() }}
    </div>
</div>
@endsection