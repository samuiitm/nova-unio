@extends('layouts.panel')

@section('title', 'Seguros deportivos | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Seguros deportivos</h1>
        <p class="mt-1 panel-muted">
            Gestiona los seguros deportivos de los alumnos de forma independiente a las cuotas.
        </p>
    </div>

    <a href="{{ route('panel.pagos.seguros.create') }}" class="panel-btn px-5 py-3">
        Registrar seguro
    </a>
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
        <div class="text-sm panel-muted">Seguros pendientes</div>
        <div class="mt-2 text-3xl font-semibold">{{ $resumen['pendientes'] }}</div>
    </div>

    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Seguros vencidos</div>
        <div class="mt-2 text-3xl font-semibold">{{ $resumen['vencidos'] }}</div>
    </div>
</div>

<div class="mt-5 panel-card p-6">
    <form method="GET" class="grid gap-3 lg:grid-cols-4">
        <div>
            <label class="text-sm panel-muted">Buscar alumno</label>
            <input
                name="q"
                value="{{ $q }}"
                class="panel-input w-full mt-1 px-4 py-3"
                placeholder="Nombre, apellidos, DNI o email"
            >
        </div>

        <div>
            <label class="text-sm panel-muted">Tipo</label>
            <select name="tipo" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Todos</option>
                @foreach($tiposSeguro as $clave => $tipoSeguro)
                    <option value="{{ $clave }}" @selected($tipo === $clave)>
                        {{ $tipoSeguro['nombre'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm panel-muted">Estado</label>
            <select name="estado" class="panel-input w-full mt-1 px-4 py-3">
                <option value="todos" @selected($estado === 'todos')>Todos</option>
                <option value="vigentes" @selected($estado === 'vigentes')>Vigentes</option>
                <option value="pendientes" @selected($estado === 'pendientes')>Pendientes</option>
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
                    <th class="py-2">Estado</th>
                    <th class="py-2">Vigencia</th>
                    <th class="py-2">Fecha pago</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2">Método</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($seguros as $seguro)
                    @php
                        $estadoVisual = $seguro->estado_visual;
                    @endphp

                    <tr class="border-t panel-border">
                        <td class="py-3">
                            <div class="font-medium">
                                {{ $seguro->alumno->apellidos }}, {{ $seguro->alumno->nombre }}
                            </div>
                            <div class="text-xs panel-muted">
                                {{ $seguro->alumno->dni ?: 'Sin documento' }}
                            </div>
                        </td>

                        <td class="py-3">{{ $seguro->tipo_nombre }}</td>

                        <td class="py-3">
                            @if($estadoVisual === 'Vigente')
                                <span class="text-xs px-3 py-1 rounded-full"
                                    style="background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);">
                                    Vigente
                                </span>
                            @elseif($estadoVisual === 'Pendiente')
                                <span class="text-xs px-3 py-1 rounded-full"
                                    style="background: rgb(255 170 80 / .12); color: rgb(255 210 150); border: 1px solid rgb(255 170 80 / .22);">
                                    Pendiente
                                </span>
                            @else
                                <span class="text-xs px-3 py-1 rounded-full"
                                    style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                                    Vencido
                                </span>
                            @endif
                        </td>

                        <td class="py-3">
                            @if($seguro->fecha_inicio || $seguro->fecha_fin)
                                {{ $seguro->fecha_inicio?->format('d/m/Y') ?: '—' }}
                                -
                                {{ $seguro->fecha_fin?->format('d/m/Y') ?: '—' }}
                            @else
                                —
                            @endif
                        </td>

                        <td class="py-3">{{ $seguro->fecha_pago?->format('d/m/Y') ?: '—' }}</td>

                        <td class="py-3">{{ number_format((float) $seguro->importe, 2, ',', '.') }} €</td>

                        <td class="py-3">{{ $seguro->metodo ? ucfirst($seguro->metodo) : '—' }}</td>

                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2 flex-wrap justify-end">
                                @if($seguro->estado === 'pendiente')
                                    <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.seguros.cobrar', $seguro) }}">
                                        Cobrar
                                    </a>

                                    <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.seguros.edit', $seguro) }}">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('panel.pagos.seguros.destroy', $seguro) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="panel-icon-btn px-4 py-2"
                                                onclick="return confirm('¿Eliminar este seguro pendiente?')">
                                            Eliminar
                                        </button>
                                    </form>
                                @elseif($seguro->estado === 'pagado')
                                    <form method="POST" action="{{ route('panel.pagos.seguros.pago.destroy', $seguro) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="panel-icon-btn px-4 py-2"
                                                onclick="return confirm('¿Borrar pago? El seguro volverá a pendiente para poder cobrarlo, editarlo o eliminarlo.')">
                                            Borrar pago
                                        </button>
                                    </form>
                                @endif

                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.alumnos.show', $seguro->alumno) }}">
                                    Ver alumno
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 panel-muted">
                            No hay seguros registrados con esos filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $seguros->onEachSide(1)->links() }}
    </div>
</div>
@endsection