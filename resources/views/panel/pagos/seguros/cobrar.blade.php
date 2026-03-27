@extends('layouts.panel')

@section('title', 'Cobrar seguro deportivo | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Cobrar seguro deportivo</h1>
        <p class="mt-1 panel-muted">{{ $seguro->alumno->nombre }} {{ $seguro->alumno->apellidos }}</p>
    </div>

    <a href="{{ route('panel.alumnos.show', $seguro->alumno) }}" class="panel-icon-btn px-5 py-3">
        Volver
    </a>
</div>

@if($errors->any())
    <div class="mt-5 panel-card p-4">
        <div class="text-sm font-medium">Hay errores:</div>
        <ul class="mt-2 text-sm panel-muted list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-5 grid gap-5 lg:grid-cols-2">
    <div class="panel-card p-6">
        <form method="POST" action="{{ route('panel.pagos.seguros.cobrar.guardar', $seguro) }}" class="grid gap-3">
            @csrf

            <div>
                <label class="text-sm panel-muted">Tipo de seguro</label>
                <input class="panel-input w-full mt-1 px-4 py-3" value="{{ $seguro->tipo_nombre }}" disabled>
            </div>

            <div>
                <label class="text-sm panel-muted">Importe</label>
                <input class="panel-input w-full mt-1 px-4 py-3" value="{{ number_format((float) $seguro->importe, 2, ',', '.') }} €" disabled>
            </div>

            <div class="grid gap-3 lg:grid-cols-2">
                <div>
                    <label class="text-sm panel-muted">Fecha de pago</label>
                    <input type="date" name="fecha_pago" value="{{ old('fecha_pago', now()->toDateString()) }}" class="panel-input w-full mt-1 px-4 py-3">
                </div>

                <div>
                    <label class="text-sm panel-muted">Método</label>
                    <select name="metodo" class="panel-input w-full mt-1 px-4 py-3">
                        @foreach(['efectivo','bizum','tarjeta','transferencia','otro'] as $metodo)
                            <option value="{{ $metodo }}" @selected(old('metodo', 'efectivo') === $metodo)>
                                {{ ucfirst($metodo) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-sm panel-muted">Notas</label>
                <textarea name="notas" rows="4" class="panel-input w-full mt-1 px-4 py-3" placeholder="Opcional">{{ old('notas') }}</textarea>
            </div>

            <div class="mt-2">
                <button class="panel-btn px-6 py-3">Registrar pago</button>
            </div>
        </form>
    </div>
</div>
@endsection