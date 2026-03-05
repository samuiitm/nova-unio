@extends('layouts.panel')

@section('title', 'Pendientes de pago | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Pendientes de pago</h1>
        <p class="mt-1 panel-muted">Cuotas pendientes (no vencidas) y alumnos sin cuota.</p>
    </div>

    <a href="{{ route('panel.pagos.pendientes') }}" class="panel-icon-btn px-5 py-3">Limpiar</a>
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
    <form method="GET" class="grid gap-3 lg:grid-cols-4">
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
            <label class="inline-flex items-center gap-2 text-sm panel-muted">
                <input type="checkbox" name="incluir_sin_grupo" value="1" @checked($incluirSinGrupo)>
                Incluir alumnos sin grupo
            </label>
        </div>

        <div class="flex items-end">
            <button class="panel-btn px-6 py-3 w-full">Filtrar</button>
        </div>
    </form>
</div>

<div class="mt-5 panel-card p-6">
    <h2 class="text-lg font-semibold">Cuotas pendientes (no vencidas)</h2>
    <p class="mt-1 panel-muted text-sm">Estas cuotas todavía están dentro del periodo o en futuro.</p>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Alumno</th>
                    <th class="py-2">Grupo</th>
                    <th class="py-2">Periodo</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2">Tipo</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuotasPendientes as $c)
                    @php
                        $grupo = $c->alumno->gruposActivos->first();
                    @endphp
                    <tr class="border-t panel-border">
                        <td class="py-3">{{ $c->alumno->apellidos }}, {{ $c->alumno->nombre }}</td>
                        <td class="py-3">{{ $grupo?->nombre ?? '—' }}</td>
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
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 panel-muted">No hay cuotas pendientes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cuotasPendientes->links() }}
    </div>
</div>

<div class="mt-5 panel-card p-6">
    <h2 class="text-lg font-semibold">Alumnos sin cuota</h2>
    <p class="mt-1 panel-muted text-sm">No tienen cuota actual ni pendiente creada.</p>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Alumno</th>
                    <th class="py-2">Grupo</th>
                    <th class="py-2">Última cuota</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumnosSinCuota as $a)
                    @php
                        $grupo = $a->gruposActivos->first();
                        $u = $a->ultimaCuota;
                    @endphp
                    <tr class="border-t panel-border">
                        <td class="py-3">{{ $a->apellidos }}, {{ $a->nombre }}</td>
                        <td class="py-3">{{ $grupo?->nombre ?? '—' }}</td>
                        <td class="py-3 panel-muted">
                            @if($u)
                                {{ $u->fecha_inicio?->format('d/m/Y') }} - {{ $u->fecha_fin?->format('d/m/Y') }} ({{ $u->estado }})
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.cuotas.crear', $a) }}">
                                    Asignar cuota
                                </a>
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.alumnos.show', $a) }}">
                                    Ver alumno
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 panel-muted">No hay alumnos sin cuota con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection