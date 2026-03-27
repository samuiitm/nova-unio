@extends('layouts.panel')

@section('title', 'Seguros deportivos | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Seguros deportivos</h1>
        <p class="mt-1 panel-muted">Gestiona los pagos anuales del seguro deportivo de cada alumno.</p>
    </div>

    <a href="{{ route('panel.pagos.seguros.create') }}" class="panel-btn px-5 py-3">Registrar seguro</a>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 grid gap-4 lg:grid-cols-3">
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Seguros vigentes</div>
        <div class="mt-2 text-3xl font-semibold">{{ $resumen['vigentes'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Seguros vencidos</div>
        <div class="mt-2 text-3xl font-semibold">{{ $resumen['vencidos'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Cobrado este año</div>
        <div class="mt-2 text-3xl font-semibold">{{ number_format((float) $resumen['cobrado_ano'], 2, ',', '.') }} €</div>
    </div>
</div>

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-4">
        <div>
            <label class="text-sm panel-muted">Buscar alumno</label>
            <input name="q" value="{{ $q }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="Nombre, apellidos, DNI o email">
        </div>

        <div>
            <label class="text-sm panel-muted">Tipo</label>
            <select name="tipo" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Todos</option>
                @foreach($tiposSeguro as $clave => $tipoSeguro)
                    <option value="{{ $clave }}" @selected($tipo === $clave)>{{ $tipoSeguro['nombre'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm panel-muted">Estado</label>
            <select name="estado" class="panel-input w-full mt-1 px-4 py-3">
                <option value="todos" @selected($estado === 'todos')>Todos</option>
                <option value="vigentes" @selected($estado === 'vigentes')>Vigentes</option>
                <option value="vencidos" @selected($estado === 'vencidos')>Vencidos</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button class="panel-btn px-6 py-3 w-full">Filtrar</button>
            <a href="{{ route('panel.pagos.seguros.index') }}" class="panel-icon-btn px-5 py-3">Limpiar</a>
        </div>
    </form>
</div>

<div class="mt-5 panel-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Alumno</th>
                    <th class="py-2">Tipo</th>
                    <th class="py-2">Pago</th>
                    <th class="py-2">Vigencia</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2">Método</th>
                    <th class="py-2">Estado</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($seguros as $seguro)
                    @php
                        $vigente = $seguro->estado_vigencia === 'vigente';
                    @endphp
                    <tr class="border-t panel-border">
                        <td class="py-3">
                            <div class="font-medium">{{ $seguro->alumno->apellidos }}, {{ $seguro->alumno->nombre }}</div>
                            <div class="text-xs panel-muted">{{ $seguro->alumno->dni ?: 'Sin documento' }}</div>
                        </td>
                        <td class="py-3">{{ $seguro->tipo_nombre }}</td>
                        <td class="py-3">{{ $seguro->fecha_pago?->format('d/m/Y') }}</td>
                        <td class="py-3">
                            {{ $seguro->fecha_inicio?->format('d/m/Y') }} - {{ $seguro->fecha_fin?->format('d/m/Y') }}
                        </td>
                        <td class="py-3">{{ number_format((float) $seguro->importe, 2, ',', '.') }} €</td>
                        <td class="py-3">{{ ucfirst($seguro->metodo) }}</td>
                        <td class="py-3">
                            @if($vigente)
                                <span class="text-xs px-3 py-1 rounded-full"
                                    style="background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);">
                                    Vigente
                                </span>
                            @else
                                <span class="text-xs px-3 py-1 rounded-full"
                                    style="background: rgb(255 170 80 / .12); color: rgb(255 210 150); border: 1px solid rgb(255 170 80 / .22);">
                                    Vencido
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2">
                                @if(!$vigente)
                                    <a class="panel-btn px-4 py-2" href="{{ route('panel.pagos.seguros.create', ['alumno' => $seguro->alumno_id]) }}">
                                        Renovar
                                    </a>
                                @endif

                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.seguros.edit', $seguro) }}">
                                    Editar
                                </a>

                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.alumnos.show', $seguro->alumno) }}">
                                    Ver alumno
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 panel-muted">No hay seguros registrados con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $seguros->links() }}
    </div>
</div>
@endsection