@extends('layouts.panel')

@section('title', 'Tipos de cuota | Nova Unió')

@section('content')
@php
    $vigenciaEdit = old('tipo_vigencia', $edit->tipo_vigencia ?? 'meses');
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Tipos de cuota</h1>
        <p class="mt-1 panel-muted">Planes de pago (mensual, temporada, beca, etc.).</p>
    </div>

    <a href="{{ route('panel.pagos.tipos') }}" class="panel-icon-btn px-5 py-3">Limpiar</a>
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
    <h2 class="text-lg font-semibold">{{ $edit ? 'Editar tipo' : 'Crear tipo' }}</h2>

    <form class="mt-4 grid gap-3 lg:grid-cols-5"
          method="POST"
          action="{{ $edit ? route('panel.pagos.tipos.update', $edit) : route('panel.pagos.tipos.store') }}">
        @csrf
        @if($edit) @method('PATCH') @endif

        <div class="lg:col-span-2">
            <label class="text-sm panel-muted">Nombre</label>
            <input name="nombre" value="{{ old('nombre', $edit->nombre ?? '') }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="Mensual, Temporada, Beca, etc.">
        </div>

        <div>
            <label class="text-sm panel-muted">Importe</label>
            <input name="importe" value="{{ old('importe', $edit->importe ?? '') }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="30.00">
        </div>

        <div>
            <label class="text-sm panel-muted">Vigencia</label>
            <select name="tipo_vigencia" id="tipo_vigencia" class="panel-input w-full mt-1 px-4 py-3">
                <option value="meses" @selected($vigenciaEdit === 'meses')>Por meses</option>
                <option value="temporada" @selected($vigenciaEdit === 'temporada')>Temporada</option>
                <option value="indefinida" @selected($vigenciaEdit === 'indefinida')>Indefinida</option>
            </select>
        </div>

        <div id="bloque_meses">
            <label class="text-sm panel-muted">Duración (meses)</label>
            <input name="duracion_meses" value="{{ old('duracion_meses', $edit->duracion_meses ?? 1) }}" class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div id="bloque_venta_inicio">
            <label class="text-sm panel-muted">Venta desde (mes)</label>
            <select name="venta_inicio_mes" class="panel-input w-full mt-1 px-4 py-3">
                @for($mes = 1; $mes <= 12; $mes++)
                    <option value="{{ $mes }}" @selected((int) old('venta_inicio_mes', $edit->venta_inicio_mes ?? 8) === $mes)>
                        {{ \Carbon\Carbon::create(null, $mes, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <div id="bloque_venta_fin">
            <label class="text-sm panel-muted">Venta hasta (mes)</label>
            <select name="venta_fin_mes" class="panel-input w-full mt-1 px-4 py-3">
                @for($mes = 1; $mes <= 12; $mes++)
                    <option value="{{ $mes }}" @selected((int) old('venta_fin_mes', $edit->venta_fin_mes ?? 12) === $mes)>
                        {{ \Carbon\Carbon::create(null, $mes, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="lg:col-span-4 flex items-end">
            <label class="inline-flex items-center gap-2 text-sm panel-muted">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $edit->activo ?? true))>
                Activo
            </label>
        </div>

        <div class="flex items-end">
            <button class="panel-btn px-6 py-3 w-full">
                {{ $edit ? 'Guardar' : 'Crear' }}
            </button>
        </div>
    </form>
</div>

<div class="mt-5 panel-card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Nombre</th>
                    <th class="py-2">Importe</th>
                    <th class="py-2">Vigencia</th>
                    <th class="py-2">Duración</th>
                    <th class="py-2">Venta</th>
                    <th class="py-2">Estado</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tipos as $t)
                    <tr class="border-t panel-border">
                        <td class="py-3">{{ $t->nombre }}</td>
                        <td class="py-3">{{ number_format((float)$t->importe, 2, ',', '.') }} €</td>
                        <td class="py-3">
                            @if($t->tipo_vigencia === 'temporada')
                                Temporada
                            @elseif($t->tipo_vigencia === 'indefinida')
                                Indefinida
                            @else
                                Por meses
                            @endif
                        </td>
                        <td class="py-3">
                            @if($t->tipo_vigencia === 'temporada')
                                Hasta 30/06
                            @elseif($t->tipo_vigencia === 'indefinida')
                                Sin vencimiento
                            @else
                                {{ $t->duracion_meses }} mes/es
                            @endif
                        </td>
                        <td class="py-3">
                            @if($t->tipo_vigencia === 'temporada')
                                {{ \Carbon\Carbon::create(null, $t->venta_inicio_mes ?: 8, 1)->translatedFormat('F') }} -
                                {{ \Carbon\Carbon::create(null, $t->venta_fin_mes ?: 12, 1)->translatedFormat('F') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3">{{ $t->activo ? 'Activo' : 'Inactivo' }}</td>
                        <td class="py-3 text-right">
                            <div class="inline-flex gap-2">
                                <a class="panel-icon-btn px-4 py-2" href="{{ route('panel.pagos.tipos', ['edit' => $t->id]) }}">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('panel.pagos.tipos.destroy', $t) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="panel-icon-btn px-4 py-2"
                                            onclick="return confirm('¿Borrar este tipo? Si tiene cuotas se desactivará.')">
                                        Borrar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 panel-muted">No hay tipos de cuota.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    (function () {
        const tipoVigencia = document.getElementById('tipo_vigencia');
        const bloqueMeses = document.getElementById('bloque_meses');
        const bloqueVentaInicio = document.getElementById('bloque_venta_inicio');
        const bloqueVentaFin = document.getElementById('bloque_venta_fin');

        function refrescarFormularioTipoCuota() {
            const valor = tipoVigencia.value;

            bloqueMeses.style.display = valor === 'meses' ? '' : 'none';
            bloqueVentaInicio.style.display = valor === 'temporada' ? '' : 'none';
            bloqueVentaFin.style.display = valor === 'temporada' ? '' : 'none';
        }

        tipoVigencia.addEventListener('change', refrescarFormularioTipoCuota);
        refrescarFormularioTipoCuota();
    })();
</script>
@endsection