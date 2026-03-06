@extends('layouts.panel')

@section('title', 'Editar alumno | Nova Unió')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Editar alumno</h1>
            <p class="mt-1 panel-muted">{{ $alumno->nombre }} {{ $alumno->apellidos }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('panel.alumnos.show', $alumno) }}" class="panel-icon-btn px-5 py-3">Ver ficha</a>
            <a href="{{ route('panel.alumnos.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
        </div>
    </div>

    @if($errors->any())
        <div class="mt-5 panel-card p-4">
            <div class="text-sm font-medium">Revisa estos campos:</div>
            <ul class="mt-2 text-sm panel-muted list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('panel.alumnos.update', $alumno) }}" class="mt-5">
        @csrf
        @method('PATCH')

        @include('panel.alumnos._form', [
            'modo' => 'edit',
            'alumno' => $alumno,
            'grupos' => $grupos,
            'gruposSeleccionados' => old('grupos', $gruposSeleccionados),
        ])

        <div class="mt-6 flex gap-3">
            <button class="panel-btn px-6 py-3">Guardar cambios</button>
            <a href="{{ route('panel.alumnos.show', $alumno) }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
        </div>
    </form>
@endsection