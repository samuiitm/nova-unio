@extends('layouts.panel')

@section('title', 'Cobrar cuota | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Cobrar cuota</h1>
        <p class="mt-1 panel-muted">
            {{ $cuota->alumno->nombre }} {{ $cuota->alumno->apellidos }} ·
            {{ $cuota->tipoCuota?->nombre ?? 'Sin tipo' }}
        </p>
    </div>

    <a href="{{ route('panel.pagos.pendientes') }}" class="panel-icon-btn px-5 py-3">Volver</a>
</div>

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
    <form method="POST" action="{{ route('panel.pagos.cuotas.cobrar.guardar', $cuota) }}" class="grid gap-3 lg:grid-cols-3">
        @csrf

        <div>
            <label class="text-sm panel-muted">Fecha de pago</label>
            <input type="date" name="fecha_pago" value="{{ old('fecha_pago', now()->toDateString()) }}"
                   class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div>
            <label class="text-sm panel-muted">Importe</label>
            <input name="importe" value="{{ old('importe', $cuota->importe) }}"
                   class="panel-input w-full mt-1 px-4 py-3" disabled>
        </div>

        <div>
            <label class="text-sm panel-muted">Método</label>
            <select name="metodo" class="panel-input w-full mt-1 px-4 py-3">
                @foreach(['efectivo','bizum','tarjeta','transferencia','otro'] as $m)
                    <option value="{{ $m }}" @selected(old('metodo','efectivo')===$m)>{{ ucfirst($m) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm panel-muted">Mes pagado</label>
            <input
                name="notas"
                value="{{ old('notas') }}"
                class="panel-input w-full mt-1 px-4 py-3"
                placeholder="Ej. NOV 2026"
                required
            >
            <p class="mt-1 text-xs panel-muted">Indica el mes o periodo pagado, por ejemplo: NOV 2026.</p>
        </div>

        <div class="lg:col-span-3 mt-2">
            <button class="panel-btn px-6 py-3">Registrar pago</button>
        </div>
    </form>
</div>
@endsection