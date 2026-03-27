@extends('layouts.panel')

@section('title', 'Editar seguro deportivo | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Editar seguro deportivo</h1>
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

@include('panel.pagos.seguros._form', [
    'action' => route('panel.pagos.seguros.update', $seguro),
    'method' => 'PATCH',
    'submitLabel' => 'Guardar cambios',
    'alumnoPreseleccionado' => null,
])
@endsection