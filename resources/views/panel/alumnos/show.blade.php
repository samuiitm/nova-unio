@extends('layouts.panel')

@section('title', 'Ficha alumno | Nova Unió')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Ficha del alumno</h1>
            <p class="mt-1 panel-muted">{{ $alumno->nombre }} {{ $alumno->apellidos }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('panel.alumnos.edit', $alumno) }}" class="panel-btn px-5 py-3">Editar</a>
            <a href="{{ route('panel.asistencias.alumno', $alumno) }}" class="panel-icon-btn px-5 py-3">Asistencias</a>
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

        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">Notas</h2>
            <p class="mt-3 text-sm panel-muted whitespace-pre-line">
                {{ $alumno->notas ?: 'Sin notas.' }}
            </p>
        </div>
    </div>
@endsection