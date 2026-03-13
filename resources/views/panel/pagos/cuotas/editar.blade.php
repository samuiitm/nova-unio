@extends('layouts.panel')

@section('title', 'Editar cuota | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Editar cuota pendiente</h1>
        <p class="mt-1 panel-muted">{{ $cuota->alumno->nombre }} {{ $cuota->alumno->apellidos }}</p>
    </div>

    <a href="{{ route('panel.alumnos.show', $cuota->alumno) }}" class="panel-icon-btn px-5 py-3">Volver</a>
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
    <form method="POST" action="{{ route('panel.pagos.cuotas.update', $cuota) }}" class="grid gap-3 lg:grid-cols-2">
        @csrf
        @method('PATCH')

        <div class="lg:col-span-2">
            <label class="text-sm panel-muted">Tipo de cuota</label>
            <select name="tipo_cuota_id" class="panel-input w-full mt-1 px-4 py-3">
                @foreach($tipos as $t)
                    <option value="{{ $t->id }}" @selected(old('tipo_cuota_id', $cuota->tipo_cuota_id) == $t->id)>
                        @if(($t->tipo_vigencia ?? 'meses') === 'temporada')
                            {{ $t->nombre }} ({{ number_format((float)$t->importe, 2, ',', '.') }}€ · temporada)
                        @elseif(($t->tipo_vigencia ?? 'meses') === 'indefinida')
                            {{ $t->nombre }} ({{ number_format((float)$t->importe, 2, ',', '.') }}€ · indefinida)
                        @else
                            {{ $t->nombre }} ({{ number_format((float)$t->importe, 2, ',', '.') }}€ · {{ $t->duracion_meses }} mes/es)
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="estado" value="pendiente">

        <div class="lg:col-span-2 mt-2">
            <button class="panel-btn px-6 py-3">Guardar</button>
        </div>
    </form>
</div>
@endsection