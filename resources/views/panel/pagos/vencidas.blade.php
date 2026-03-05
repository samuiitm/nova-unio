@extends('layouts.panel')

@section('title', 'Cuotas vencidas | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Cuotas vencidas</h1>
        <p class="mt-1 panel-muted">Alumnos cuya última cuota pagada ya ha vencido y deben renovar.</p>
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
                    <th class="py-2">Grupo</th>
                    <th class="py-2">Última cuota</th>
                    <th class="py-2">Fin</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($alumnosVencidos as $a)
                    @php
                        $grupo = $a->gruposActivos->first();
                        $u = $a->ultimaCuotaPagada;
                    @endphp

                    <tr class="border-t panel-border">
                        <td class="py-3">{{ $a->apellidos }}, {{ $a->nombre }}</td>
                        <td class="py-3">{{ $grupo?->nombre ?? '—' }}</td>

                        <td class="py-3">
                            {{ $u?->tipoCuota?->nombre ?? '—' }}
                            @if($u)
                                <div class="text-xs panel-muted">
                                    {{ $u->fecha_inicio?->format('d/m/Y') }} - {{ $u->fecha_fin?->format('d/m/Y') }}
                                </div>
                            @endif
                        </td>

                        <td class="py-3">
                            {{ $u?->fecha_fin?->format('d/m/Y') ?? '—' }}
                        </td>

                        <td class="py-3">
                            {{ $u ? number_format((float)$u->importe, 2, ',', '.') . ' €' : '—' }}
                        </td>

                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.cuotas.crear', $a) }}">
                                    Renovar
                                </a>
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.alumnos.show', $a) }}">
                                    Ver alumno
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 panel-muted">No hay cuotas vencidas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $alumnosVencidos->links() }}
    </div>
</div>
@endsection