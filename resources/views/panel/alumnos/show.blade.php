@extends('layouts.panel')

@section('title', 'Ficha alumno | Nova Unió')

@section('content')
@php

$hoy = now()->toDateString();
    $tituloCuota =
        $estadoCuota === 'vigente' ? ($cuotaVigente->tipoCuota->nombre ?? 'Cuota') :
        ($estadoCuota === 'pendiente' ? ($cuotaPendiente->tipoCuota->nombre ?? 'Cuota pendiente') :
        ($estadoCuota === 'vencida' ? ($ultimaPagada->tipoCuota->nombre ?? 'Cuota vencida') :
        'Sin cuota'));

    $inicio =
        $estadoCuota === 'vigente' ? $cuotaVigente->fecha_inicio :
        ($estadoCuota === 'vencida' ? $ultimaPagada->fecha_inicio : null);

    $fin =
        $estadoCuota === 'vigente' ? $cuotaVigente->fecha_fin :
        ($estadoCuota === 'vencida' ? $ultimaPagada->fecha_fin : null);

    $importe =
        $estadoCuota === 'vigente' ? $cuotaVigente->importe :
        ($estadoCuota === 'pendiente' ? $cuotaPendiente->importe :
        ($estadoCuota === 'vencida' ? $ultimaPagada->importe : null));
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Ficha del alumno</h1>
        <p class="mt-1 panel-muted">{{ $alumno->nombre }} {{ $alumno->apellidos }}</p>
    </div>

    <div class="flex gap-2">
        @if(\Illuminate\Support\Facades\Route::has('panel.pagos.cuotas.crear'))
            <a href="{{ route('panel.pagos.cuotas.crear', $alumno) }}" class="panel-btn px-5 py-3">
                Asignar cuota
            </a>
        @endif
        <a href="{{ route('panel.alumnos.edit', $alumno) }}" class="panel-btn px-5 py-3">Editar</a>
        <a href="{{ route('panel.alumnos.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
    </div>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 grid gap-4 lg:grid-cols-2">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Datos</h2>

        <div class="mt-4 grid gap-3 text-sm">
            <div><span class="panel-muted">Nombre:</span> {{ $alumno->nombre }}</div>
            <div><span class="panel-muted">Apellidos:</span> {{ $alumno->apellidos }}</div>

            <div><span class="panel-muted">CatSalut:</span> {{ $alumno->catsalut ?: '—' }}</div>
            <div><span class="panel-muted">DNI:</span> {{ $alumno->dni ?: '—' }}</div>

            <div><span class="panel-muted">Nacimiento:</span>
                {{ $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('d/m/Y') : '—' }}
            </div>
            <div><span class="panel-muted">Lugar:</span> {{ $alumno->lugar_nacimiento ?: '—' }}</div>

            <div><span class="panel-muted">Dirección:</span> {{ $alumno->direccion ?: '—' }}</div>
            <div><span class="panel-muted">CP:</span> {{ $alumno->cp ?: '—' }}</div>
            <div><span class="panel-muted">Población:</span> {{ $alumno->poblacion ?: '—' }}</div>

            <div><span class="panel-muted">Teléfono:</span> {{ $alumno->telefono ?: '—' }}</div>
            <div><span class="panel-muted">Email:</span> {{ $alumno->email ?: '—' }}</div>

            <div><span class="panel-muted">Inicio actividad:</span>
                {{ $alumno->fecha_inicio_actividad ? $alumno->fecha_inicio_actividad->format('d/m/Y') : '—' }}
            </div>

            <div><span class="panel-muted">Fecha baja:</span>
                {{ $alumno->fecha_baja ? $alumno->fecha_baja->format('d/m/Y') : '—' }}
            </div>

            <div><span class="panel-muted">Estado:</span> {{ $alumno->activo ? 'Activo' : 'Inactivo' }}</div>

            <div class="sm:col-span-2">
                <div class="panel-muted">Grupos activos:</div>

                @if($alumno->gruposActivos->isEmpty())
                    <div class="mt-2 text-sm panel-muted">Sin grupo asignado.</div>
                @else
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($alumno->gruposActivos as $grupo)
                            <span class="text-xs px-3 py-1 rounded-full"
                                  style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .80); border: 1px solid rgb(255 255 255 / .10);">
                                {{ $grupo->nombre }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5 flex gap-2">
            @if($alumno->activo)
                <form method="POST" action="{{ route('panel.alumnos.baja', $alumno) }}">
                    @csrf
                    @method('PATCH')
                    <button class="panel-icon-btn px-5 py-3" onclick="return confirm('¿Dar de baja a este alumno?')">
                        Dar de baja
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('panel.alumnos.activar', $alumno) }}">
                    @csrf
                    @method('PATCH')
                    <button class="panel-icon-btn px-5 py-3">
                        Activar
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- CUOTA --}}
    <div>
        <div class="panel-card p-6"
             style="background: radial-gradient(1200px 600px at 0% 0%, rgba(0,255,160,.06), transparent 60%);">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-semibold" style="color: rgb(60 220 150);">
                        {{ $tituloCuota }}
                    </div>

                    <div class="mt-2 text-sm panel-muted">
                        @if($estadoCuota === 'vigente')
                            Estado: <span class="text-white">Vigente</span>
                        @elseif($estadoCuota === 'pendiente')
                            Estado: <span class="text-white">Pendiente de pago</span>
                        @elseif($estadoCuota === 'vencida')
                            Estado: <span class="text-white">Vencida</span>
                        @else
                            Estado: <span class="text-white">Sin cuota</span>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-xs panel-muted uppercase tracking-wider">TOTAL</div>
                    <div class="text-3xl font-bold">
                        {{ $importe !== null ? number_format((float)$importe, 2, ',', '.') . ' €' : '—' }}
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-between">
                <div class="panel-muted text-sm">
                    Inicio:
                    <span class="text-white">
                        {{ $inicio ? $inicio->format('d/m/Y') : ($estadoCuota === 'pendiente' ? 'Al cobrar' : '—') }}
                    </span>
                </div>
                <div class="panel-muted text-sm">
                    Fin:
                    <span class="text-white">
                        {{ $fin ? $fin->format('d/m/Y') : ($estadoCuota === 'pendiente' ? 'Al cobrar' : '—') }}
                    </span>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                @if($estadoCuota === 'pendiente' && $cuotaPendiente)
                    <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.pagos.cuotas.cobrar', $cuotaPendiente) }}">
                        Cobrar
                    </a>

                    <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.pagos.cuotas.edit', $cuotaPendiente) }}">
                        Editar
                    </a>

                    <form method="POST" action="{{ route('panel.pagos.cuotas.destroy', $cuotaPendiente) }}">
                        @csrf
                        @method('DELETE')
                        <button class="panel-icon-btn px-5 py-3"
                                onclick="return confirm('¿Eliminar esta cuota pendiente?')">
                            Eliminar
                        </button>
                    </form>
                @endif

                @if($estadoCuota === 'vencida')
                    <a class="panel-btn px-5 py-3" href="{{ route('panel.pagos.cuotas.crear', $alumno) }}">
                        Renovar
                    </a>
                @endif

                @if($estadoCuota === 'sin_cuota')
                    <a class="panel-btn px-5 py-3" href="{{ route('panel.pagos.cuotas.crear', $alumno) }}">
                        Asignar cuota
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-4 panel-card p-6">
            <h2 class="text-lg font-semibold">Notas</h2>
            <p class="mt-3 text-sm panel-muted whitespace-pre-line">
                {{ $alumno->notas ?: 'Sin notas.' }}
            </p>
        </div>
    </div>
</div>

{{-- HISTORIAL --}}
<div class="mt-5 grid gap-4 lg:grid-cols-2">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Historial de cuotas</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Plan</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2">Periodo</th>
                        <th class="py-2">Importe</th>
                        <th class="py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cuotas as $c)
                        <tr class="border-t panel-border {{ $c->estado === 'anulada' ? 'opacity-50' : '' }}">
                            <td class="py-3">{{ $c->tipoCuota?->nombre ?? '—' }}</td>
                            <td class="py-3">{{ ucfirst($c->estado) }}</td>
                            @php
                                $hoy = now()->toDateString();

                                if ($c->estado === 'anulada') {
                                    $estadoVisual = 'Anulada';
                                } elseif ($c->estado === 'pendiente') {
                                    $estadoVisual = 'Pendiente';
                                } else {
                                    // pagada
                                    $estadoVisual = ($c->fecha_fin && $c->fecha_fin->toDateString() < $hoy) ? 'Vencida' : 'Vigente';
                                }
                            @endphp

                            <td class="py-3">{{ $estadoVisual }}</td>
                            <td class="py-3">{{ number_format((float)$c->importe, 2, ',', '.') }} €</td>
                            <td class="py-3 text-right">
                                <div class="inline-flex gap-2">
                                    @if($c->estado === 'pendiente')
                                        <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.cuotas.cobrar', $c) }}">Cobrar</a>
                                        <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.cuotas.edit', $c) }}">Editar</a>

                                        <form method="POST" action="{{ route('panel.pagos.cuotas.destroy', $c) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="panel-icon-btn px-4 py-2"
                                                    onclick="return confirm('¿Eliminar esta cuota pendiente?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif

                                    @if($c->estado === 'pagada' && $c->pago)
                                        <form method="POST" action="{{ route('panel.pagos.destroy', $c->pago) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="panel-icon-btn px-4 py-2"
                                                    onclick="return confirm('¿Borrar pago? La cuota volverá a pendiente para poder editarla o eliminarla.')">
                                                Borrar pago
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 panel-muted">Sin cuotas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Historial de pagos</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Fecha</th>
                        <th class="py-2">Plan</th>
                        <th class="py-2">Importe</th>
                        <th class="py-2">Método</th>
                        <th class="py-2">Notas</th>
                        <th class="py-2 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $p)
                        <tr class="border-t panel-border">
                            <td class="py-3">{{ $p->fecha_pago?->format('d/m/Y') }}</td>
                            <td class="py-3">{{ $p->cuota->tipoCuota?->nombre ?? '—' }}</td>
                            <td class="py-3">{{ number_format((float)$p->importe, 2, ',', '.') }} €</td>
                            <td class="py-3">{{ ucfirst($p->metodo) }}</td>
                            <td class="py-3 panel-muted">{{ $p->notas ?: '—' }}</td>
                            <td class="py-3 text-right">
                                <form method="POST" action="{{ route('panel.pagos.destroy', $p) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="panel-icon-btn px-4 py-2"
                                            onclick="return confirm('¿Borrar este pago? La cuota volverá a pendiente.')">
                                        Borrar pago
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 panel-muted">Sin pagos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection