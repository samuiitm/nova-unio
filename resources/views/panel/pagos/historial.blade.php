@extends('layouts.panel')

@section('title', 'Historial de pagos | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Historial de pagos</h1>
        <p class="mt-1 panel-muted">Registro de pagos realizados.</p>
    </div>

    <a href="{{ route('panel.pagos.historial') }}" class="panel-icon-btn px-5 py-3">Limpiar</a>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label class="text-sm panel-muted">Buscar alumno</label>
            <input name="q" value="{{ $q }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="Nombre o apellidos">
        </div>

        <div>
            <label class="text-sm panel-muted">Método</label>
            <select name="metodo" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Todos</option>
                @foreach(['efectivo','bizum','tarjeta','transferencia','otro'] as $m)
                    <option value="{{ $m }}" @selected($metodo===$m)>{{ ucfirst($m) }}</option>
                @endforeach
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

        <div class="lg:col-span-3">
            <label class="text-sm panel-muted">Tipo de cuota</label>
            <select name="tipo_cuota_id" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Todos</option>
                @foreach($tipos as $t)
                    <option value="{{ $t->id }}" @selected((string)$tipo_cuota_id === (string)$t->id)>{{ $t->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="lg:col-span-2 flex items-end">
            <button class="panel-btn px-6 py-3 w-full">Filtrar</button>
        </div>

        <div class="lg:col-span-5 text-sm panel-muted mt-1">
            Total pagos: <span class="font-semibold">{{ (int)($totales->total ?? 0) }}</span> ·
            Suma: <span class="font-semibold">{{ number_format((float)($totales->suma ?? 0), 2, ',', '.') }} €</span>
        </div>
    </form>
</div>

<div class="mt-5 panel-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Fecha</th>
                    <th class="py-2">Alumno</th>
                    <th class="py-2">Periodo</th>
                    <th class="py-2">Tipo</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2">Método</th>
                    <th class="py-2">Mes pagado</th>
                    <th class="py-2 text-right">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $p)
                    <tr class="border-t panel-border">
                        <td class="py-3">{{ $p->fecha_pago?->format('d/m/Y') }}</td>
                        <td class="py-3">{{ $p->alumno->apellidos }}, {{ $p->alumno->nombre }}</td>
                        <td class="py-3">
                            {{ $p->cuota->fecha_inicio?->format('d/m/Y') }} - {{ $p->cuota->fecha_fin?->format('d/m/Y') }}
                        </td>
                        <td class="py-3">{{ $p->tipo_cuota_nombre ?? ($p->cuota->tipoCuota?->nombre ?? '—') }}</td>
                        <td class="py-3">{{ number_format((float)$p->importe, 2, ',', '.') }} €</td>
                        <td class="py-3">{{ ucfirst($p->metodo) }}</td>
                        <td class="py-3 panel-muted">{{ $p->notas ?: '—' }}</td>
                        <td class="py-3 text-right">
                            <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.alumnos.show', $p->alumno) }}">Ver alumno</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 panel-muted">No hay pagos con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pagos->links() }}
    </div>
</div>
@endsection