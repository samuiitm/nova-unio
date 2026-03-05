@extends('layouts.panel')

@section('title', 'Asignar cuota | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Asignar cuota</h1>
        <p class="mt-1 panel-muted">{{ $alumno->nombre }} {{ $alumno->apellidos }}</p>
    </div>

    <a href="{{ route('panel.alumnos.show', $alumno) }}" class="panel-icon-btn px-5 py-3">Volver</a>
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

<div class="mt-5 panel-card p-6" x-data="{ estado: '{{ old('estado','pagada') }}' }">
    <form method="POST" action="{{ route('panel.pagos.cuotas.store', $alumno) }}" class="grid gap-3 lg:grid-cols-2">
        @csrf

        <div class="lg:col-span-2">
            <label class="text-sm panel-muted">Tipo de cuota (opcional)</label>
            <select name="tipo_cuota_id" class="panel-input w-full mt-1 px-4 py-3">
                <option value="">Sin tipo (manual)</option>
                @foreach($tipos as $t)
                    <option value="{{ $t->id }}" @selected(old('tipo_cuota_id') == $t->id)>
                        {{ $t->nombre }} ({{ number_format((float)$t->importe, 2, ',', '.') }}€ · {{ $t->duracion_meses }} mes/es)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm panel-muted">Fecha inicio</label>
            <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $fechaInicio) }}"
                   class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div>
            <label class="text-sm panel-muted">Fecha fin (si no eliges tipo)</label>
            <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                   class="panel-input w-full mt-1 px-4 py-3">
        </div>

        <div>
            <label class="text-sm panel-muted">Importe (si no eliges tipo)</label>
            <input name="importe" value="{{ old('importe') }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="30.00">
        </div>

        <div>
            <label class="text-sm panel-muted">Estado</label>
            <select name="estado" class="panel-input w-full mt-1 px-4 py-3" x-model="estado">
                <option value="pagada">Pagada</option>
                <option value="pendiente">Pendiente</option>
            </select>
        </div>

        <div x-show="estado === 'pagada'" class="lg:col-span-2 grid gap-3 lg:grid-cols-3">
            <div>
                <label class="text-sm panel-muted">Fecha de pago</label>
                <input type="date" name="fecha_pago" value="{{ old('fecha_pago', now()->toDateString()) }}"
                       class="panel-input w-full mt-1 px-4 py-3">
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
                <label class="text-sm panel-muted">Notas</label>
                <input name="notas" value="{{ old('notas') }}" class="panel-input w-full mt-1 px-4 py-3" placeholder="Opcional">
            </div>
        </div>

        <div class="lg:col-span-2 mt-2">
            <button class="panel-btn px-6 py-3">Guardar</button>
        </div>
    </form>
</div>
@endsection