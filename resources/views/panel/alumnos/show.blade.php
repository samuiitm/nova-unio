@extends('layouts.panel')

@section('title', 'Ficha alumno | Nova Unió')

@section('content')
@php
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

    $telefonosContacto = $alumno->telefonosContacto ?? collect();

    $relacionTutorTexto = match($alumno->tutor_legal_relacion) {
        'padre' => 'Padre',
        'madre' => 'Madre',
        'tutor' => 'Tutor/a',
        default => '—',
    };

    $seguroActual = $seguroVigente ?: $seguroPendiente ?: $ultimoSeguroPagado;

    $tituloSeguro = $seguroActual?->tipo_nombre ?? 'Sin seguro deportivo';
    $importeSeguro = $seguroActual?->importe ?? null;
    $inicioSeguro = $seguroActual?->fecha_inicio ?? null;
    $finSeguro = $seguroActual?->fecha_fin ?? null;

    $estadoSeguro =
        $seguroVigente ? 'vigente' :
        ($seguroPendiente ? 'pendiente' :
        ($ultimoSeguroPagado ? 'vencido' : 'sin_seguro'));
@endphp

<div class="flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-4">
        <img
            src="{{ $alumno->foto_url }}"
            alt="Foto del alumno"
            class="h-20 w-20 rounded-2xl object-cover border panel-border"
        >

        <div>
            <h1 class="text-2xl font-semibold">Ficha del alumno</h1>

            <div class="mt-1 flex flex-wrap items-center gap-2">
                <p class="panel-muted">{{ $alumno->nombre }} {{ $alumno->apellidos }}</p>

                @if($alumno->activo)
                    <span class="text-xs px-3 py-1 rounded-full"
                        style="background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);">
                        Activo
                    </span>
                @else
                    <span class="text-xs px-3 py-1 rounded-full"
                        style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                        De baja
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="flex gap-2 flex-wrap">
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

@if(!$alumno->activo && $alumno->fecha_baja)
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">
            Este alumno está <span class="font-semibold">de baja</span> desde el {{ $alumno->fecha_baja->format('d/m/Y') }}.
        </div>
    </div>
@endif

<div class="mt-5 grid gap-4 lg:grid-cols-4">
    <div class="panel-card p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold">Datos del alumno</h2>

        <div class="mt-4 grid gap-3 text-sm lg:grid-cols-2">
            <div><span class="panel-muted">Nombre:</span> {{ $alumno->nombre }}</div>
            <div><span class="panel-muted">Apellidos:</span> {{ $alumno->apellidos }}</div>

            <div><span class="panel-muted">Documento identificativo:</span> {{ $alumno->dni ?: '—' }}</div>
            <div><span class="panel-muted">CatSalut:</span> {{ $alumno->catsalut ?: '—' }}</div>

            <div>
                <span class="panel-muted">Nacimiento:</span>
                {{ $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('d/m/Y') : '—' }}
            </div>

            <div><span class="panel-muted">Lugar de nacimiento:</span> {{ $alumno->lugar_nacimiento ?: '—' }}</div>
            <div><span class="panel-muted">Dirección:</span> {{ $alumno->direccion ?: '—' }}</div>
            <div><span class="panel-muted">CP:</span> {{ $alumno->cp ?: '—' }}</div>
            <div><span class="panel-muted">Población:</span> {{ $alumno->poblacion ?: '—' }}</div>

            <div>
                <span class="panel-muted">Inicio actividad:</span>
                {{ $alumno->fecha_inicio_actividad ? $alumno->fecha_inicio_actividad->format('d/m/Y') : '—' }}
            </div>

            <div>
                <span class="panel-muted">Fecha baja:</span>
                {{ $alumno->fecha_baja ? $alumno->fecha_baja->format('d/m/Y') : '—' }}
            </div>

            <div>
                <span class="panel-muted">Estado:</span>
                @if($alumno->activo)
                    Activo
                @else
                    De baja
                    @if($alumno->fecha_baja)
                        desde el {{ $alumno->fecha_baja->format('d/m/Y') }}
                    @endif
                @endif
            </div>

            <div>
                <span class="panel-muted">Grupos:</span>
                @if(($gruposActivos ?? collect())->isEmpty())
                    —
                @else
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($gruposActivos as $g)
                            <span class="text-xs px-3 py-1 rounded-full"
                                style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                                {{ $g->nombre }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 border-t panel-border pt-5">
            <h3 class="text-base font-semibold">Contacto y tutor legal</h3>

            <div class="mt-4 grid gap-3 text-sm lg:grid-cols-2">
                <div><span class="panel-muted">Teléfono principal:</span> {{ $alumno->telefono ?: '—' }}</div>
                <div><span class="panel-muted">Email:</span> {{ $alumno->email ?: '—' }}</div>

                <div><span class="panel-muted">Tutor legal:</span> {{ $alumno->tutor_legal_nombre ?: '—' }}</div>
                <div><span class="panel-muted">Documento tutor legal:</span> {{ $alumno->tutor_legal_dni ?: '—' }}</div>
                <div><span class="panel-muted">Relación:</span> {{ $relacionTutorTexto }}</div>

                <div class="lg:col-span-2">
                    <span class="panel-muted">Otros teléfonos:</span>
                    @if($telefonosContacto->isEmpty())
                        —
                    @else
                        <div class="mt-2 space-y-1">
                            @foreach($telefonosContacto as $telefonoExtra)
                                <div>
                                    <span class="text-white">{{ $telefonoExtra->contacto }}</span>
                                    <span class="panel-muted">·</span>
                                    <span>{{ $telefonoExtra->telefono }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 border-t panel-border pt-5">
            <h3 class="text-base font-semibold">Notas</h3>
            <p class="mt-3 text-sm panel-muted whitespace-pre-line">
                {{ $alumno->notas ?: 'Sin notas.' }}
            </p>
        </div>

        <div class="mt-5 flex gap-2">
            @if($alumno->activo)
                <form method="POST" action="{{ route('panel.alumnos.baja', $alumno) }}">
                    @csrf
                    @method('PATCH')

                    <button class="panel-icon-btn px-5 py-3">
                        Dar de baja
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('panel.alumnos.activar', $alumno) }}">
                    @csrf
                    @method('PATCH')

                    <button class="panel-btn px-5 py-3">
                        Reactivar
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="space-y-4 lg:col-span-2">
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
                        {{ $importe !== null ? number_format((float) $importe, 2, ',', '.') . ' €' : '—' }}
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

        <div class="panel-card p-6"
             style="background: radial-gradient(1200px 600px at 0% 0%, rgba(90,155,255,.10), transparent 60%);">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-semibold" style="color: rgb(145 190 255);">
                        {{ $tituloSeguro }}
                    </div>

                    <div class="mt-2 text-sm panel-muted">
                        @if($estadoSeguro === 'vigente')
                            Estado: <span class="text-white">Vigente</span>
                        @elseif($estadoSeguro === 'pendiente')
                            Estado: <span class="text-white">Pendiente de pago</span>
                        @elseif($estadoSeguro === 'vencido')
                            Estado: <span class="text-white">Vencido</span>
                        @else
                            Estado: <span class="text-white">Sin seguro</span>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-xs panel-muted uppercase tracking-wider">TOTAL</div>
                    <div class="text-3xl font-bold">
                        {{ $importeSeguro !== null ? number_format((float) $importeSeguro, 2, ',', '.') . ' €' : '—' }}
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-between">
                <div class="panel-muted text-sm">
                    Inicio:
                    <span class="text-white">
                        {{ $inicioSeguro ? $inicioSeguro->format('d/m/Y') : ($estadoSeguro === 'pendiente' ? 'Al cobrar' : '—') }}
                    </span>
                </div>
                <div class="panel-muted text-sm">
                    Fin:
                    <span class="text-white">
                        {{ $finSeguro ? $finSeguro->format('d/m/Y') : ($estadoSeguro === 'pendiente' ? 'Al cobrar' : '—') }}
                    </span>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                @if($seguroPendiente)
                    <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.pagos.seguros.cobrar', $seguroPendiente) }}">
                        Cobrar
                    </a>

                    <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.pagos.seguros.edit', $seguroPendiente) }}">
                        Editar
                    </a>

                    <form method="POST" action="{{ route('panel.pagos.seguros.destroy', $seguroPendiente) }}">
                        @csrf
                        @method('DELETE')
                        <button class="panel-icon-btn px-5 py-3"
                                onclick="return confirm('¿Eliminar este seguro pendiente?')">
                            Eliminar
                        </button>
                    </form>
                @elseif($seguroVigente)
                    <form method="POST" action="{{ route('panel.pagos.seguros.pago.destroy', $seguroVigente) }}">
                        @csrf
                        @method('DELETE')
                        <button class="panel-icon-btn px-5 py-3"
                                onclick="return confirm('¿Borrar pago? El seguro volverá a pendiente para poder cobrarlo, editarlo o eliminarlo.')">
                            Borrar pago
                        </button>
                    </form>
                @else
                    <a class="panel-btn px-5 py-3" href="{{ route('panel.pagos.seguros.create', ['alumno' => $alumno->id]) }}">
                        {{ $ultimoSeguroPagado ? 'Renovar seguro' : 'Registrar seguro' }}
                    </a>
                @endif

                <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.pagos.seguros.index') }}">
                    Ver seguros
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mt-5 space-y-4">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Historial de pagos</h2>
        <p class="mt-1 text-sm panel-muted">Aquí se ve cada cobro realizado, con el mes pagado indicado para cada pago.</p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Fecha</th>
                        <th class="py-2">Concepto</th>
                        <th class="py-2">Vigencia</th>
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
                            <td class="py-3">{{ $p->tipo_cuota_nombre ?? ($p->cuota->tipoCuota->nombre ?? '—') }}</td>
                            <td class="py-3">
                                {{ $p->vigencia_inicio ? \Carbon\Carbon::parse($p->vigencia_inicio)->format('d/m/Y') : '—' }}
                                -
                                {{ $p->vigencia_fin ? \Carbon\Carbon::parse($p->vigencia_fin)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-3">{{ number_format((float) $p->importe, 2, ',', '.') }} €</td>
                            <td class="py-3">{{ ucfirst($p->metodo) }}</td>
                            <td class="py-3">{{ $p->notas ?: '—' }}</td>
                            <td class="py-3 text-right">
                                <div class="inline-flex gap-2 flex-wrap justify-end">
                                    <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.recibo', $p) }}">
                                        Descargar recibo
                                    </a>

                                    <form method="POST" action="{{ route('panel.pagos.destroy', $p) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="panel-icon-btn px-4 py-2"
                                                onclick="return confirm('¿Borrar este pago? La cuota volverá a pendiente.')">
                                            Borrar pago
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 panel-muted">Sin pagos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Historial de seguros deportivos</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
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
                        <tr class="border-t panel-border">
                            <td class="py-3">{{ $seguro->tipo_nombre }}</td>
                            <td class="py-3">{{ $seguro->estado_visual }}</td>
                            <td class="py-3">
                                @if($seguro->fecha_inicio || $seguro->fecha_fin)
                                    {{ $seguro->fecha_inicio?->format('d/m/Y') ?: '—' }} - {{ $seguro->fecha_fin?->format('d/m/Y') ?: '—' }}
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
                                        <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.seguros.cobrar', $seguro) }}">Cobrar</a>
                                        <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.seguros.edit', $seguro) }}">Editar</a>

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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 panel-muted">Sin seguros registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection