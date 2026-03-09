@extends('layouts.panel')

@section('title', 'Crear alumno | Nova Unió')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">
                {{ isset($preinscripcion) && $preinscripcion ? 'Crear alumno desde preinscripción' : 'Crear alumno' }}
            </h1>
            <p class="mt-1 panel-muted">
                {{ isset($preinscripcion) && $preinscripcion ? 'Los datos básicos ya vienen cargados desde la preinscripción.' : 'Puedes asignarle grupos y cuota desde aquí.' }}
            </p>
        </div>

        <div class="flex gap-2">
            @if(isset($preinscripcion) && $preinscripcion)
                <a href="{{ route('panel.preinscripciones.show', $preinscripcion) }}" class="panel-icon-btn px-5 py-3">Ver preinscripción</a>
            @endif
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

    @if(isset($preinscripcion) && $preinscripcion)
        <div class="mt-5 panel-card p-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="text-lg font-semibold">Contexto de la preinscripción</div>
                    <p class="mt-1 text-sm panel-muted">
                        Esta preinscripción solo pasará a resuelta cuando guardes el alumno.
                    </p>
                </div>

                @php
                    $estadoColor = match($preinscripcion->estado) {
                        'resuelta' => 'background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);',
                        'en_proceso' => 'background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);',
                        default => 'background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .75); border: 1px solid rgb(255 255 255 / .10);',
                    };
                @endphp
                <span class="text-xs px-3 py-1 rounded-full" style="{{ $estadoColor }}">
                    {{ ucfirst(str_replace('_', ' ', $preinscripcion->estado)) }}
                </span>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4 text-sm">
                <div>
                    <div class="panel-muted">Nombre</div>
                    <div class="mt-1 font-medium">{{ trim(($preinscripcion->nombre ?? '') . ' ' . ($preinscripcion->apellidos ?? '')) ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Edad</div>
                    <div class="mt-1 font-medium">{{ $preinscripcion->edad ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Modalidad</div>
                    <div class="mt-1 font-medium">{{ $preinscripcion->modalidad ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Nivel</div>
                    <div class="mt-1 font-medium">{{ $preinscripcion->nivel ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Objetivo</div>
                    <div class="mt-1 font-medium">{{ $preinscripcion->objetivo ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Email</div>
                    <div class="mt-1 font-medium">{{ $preinscripcion->email ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Teléfono</div>
                    <div class="mt-1 font-medium">{{ $preinscripcion->telefono ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Fecha</div>
                    <div class="mt-1 font-medium">{{ optional($preinscripcion->created_at)->format('d/m/Y H:i') ?: '—' }}</div>
                </div>
            </div>

            @if($preinscripcion->mensaje)
                <div class="mt-5 rounded-2xl border panel-border p-4 text-sm" style="background: rgb(255 255 255 / .03);">
                    <div class="panel-muted mb-2">Mensaje</div>
                    <div class="whitespace-pre-line">{{ $preinscripcion->mensaje }}</div>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('panel.alumnos.store') }}" class="mt-5">
        @csrf

        @if(isset($preinscripcion) && $preinscripcion)
            <input type="hidden" name="preinscripcion_id" value="{{ old('preinscripcion_id', $preinscripcion->id) }}">
        @endif

        @include('panel.alumnos._form', [
            'modo' => 'create',
            'alumno' => $alumno ?? null,
            'grupos' => $grupos,
            'tiposCuota' => $tiposCuota,
            'gruposSeleccionados' => old('grupos', []),
        ])

        <div class="mt-6 flex gap-3">
            <button class="panel-btn px-6 py-3">Guardar</button>
            @if(isset($preinscripcion) && $preinscripcion)
                <a href="{{ route('panel.preinscripciones.show', $preinscripcion) }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
            @else
                <a href="{{ route('panel.alumnos.index') }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
            @endif
        </div>
    </form>
@endsection