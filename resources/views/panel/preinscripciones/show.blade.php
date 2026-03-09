@extends('layouts.panel')

@section('title', 'Preinscripción | Nova Unió')

@section('content')
@php
    $estadoColor = match($preinscripcion->estado) {
        'resuelta' => 'background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);',
        'en_proceso' => 'background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);',
        default => 'background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .75); border: 1px solid rgb(255 255 255 / .10);',
    };
@endphp

<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="text-2xl font-semibold">{{ trim(($preinscripcion->nombre ?? '') . ' ' . ($preinscripcion->apellidos ?? '')) ?: 'Preinscripción' }}</h1>
            <span class="text-xs px-3 py-1 rounded-full" style="{{ $estadoColor }}">
                {{ ucfirst(str_replace('_', ' ', $preinscripcion->estado)) }}
            </span>
        </div>
        <p class="mt-1 panel-muted">Recibida el {{ optional($preinscripcion->created_at)->format('d/m/Y H:i') ?: '—' }}</p>
    </div>

    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('panel.preinscripciones.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>

        @if($preinscripcion->estado === 'resuelta' && $preinscripcion->alumno_id)
            <a href="{{ route('panel.alumnos.show', $preinscripcion->alumno_id) }}" class="panel-btn px-5 py-3">Ver alumno</a>
        @else
            <a href="{{ route('panel.preinscripciones.convertir', $preinscripcion) }}" class="panel-btn px-5 py-3">Convertir en alumno</a>
        @endif
    </div>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 grid gap-4 lg:grid-cols-2">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Datos de contacto</h2>

        <div class="mt-4 grid gap-3 text-sm">
            <div><span class="panel-muted">Nombre:</span> {{ $preinscripcion->nombre ?: '—' }}</div>
            <div><span class="panel-muted">Apellidos:</span> {{ $preinscripcion->apellidos ?: '—' }}</div>
            <div><span class="panel-muted">Email:</span> {{ $preinscripcion->email ?: '—' }}</div>
            <div><span class="panel-muted">Teléfono:</span> {{ $preinscripcion->telefono ?: '—' }}</div>
            <div><span class="panel-muted">Edad:</span> {{ $preinscripcion->edad ?: '—' }}</div>
        </div>
    </div>

    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Orientación deportiva</h2>

        <div class="mt-4 grid gap-3 text-sm">
            <div><span class="panel-muted">Modalidad:</span> {{ $preinscripcion->modalidad ?: '—' }}</div>
            <div><span class="panel-muted">Nivel:</span> {{ $preinscripcion->nivel ?: '—' }}</div>
            <div><span class="panel-muted">Objetivo:</span> {{ $preinscripcion->objetivo ?: '—' }}</div>
            <div><span class="panel-muted">Estado:</span> {{ ucfirst(str_replace('_', ' ', $preinscripcion->estado)) }}</div>
            <div><span class="panel-muted">Resuelta el:</span> {{ $preinscripcion->resuelta_at ? $preinscripcion->resuelta_at->format('d/m/Y H:i') : '—' }}</div>
        </div>
    </div>
</div>

<div class="mt-5 grid gap-4 lg:grid-cols-[1.5fr,.9fr]">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Mensaje</h2>
        <p class="mt-4 text-sm whitespace-pre-line">{{ $preinscripcion->mensaje ?: 'Sin mensaje.' }}</p>
    </div>

    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Conversión</h2>

        @if($preinscripcion->alumno)
            <div class="mt-4 text-sm">
                <div><span class="panel-muted">Alumno vinculado:</span></div>
                <div class="mt-1 font-medium">{{ $preinscripcion->alumno->nombre }} {{ $preinscripcion->alumno->apellidos }}</div>
            </div>

            <div class="mt-5">
                <a href="{{ route('panel.alumnos.show', $preinscripcion->alumno) }}" class="panel-btn px-5 py-3 inline-flex">Abrir ficha</a>
            </div>
        @else
            <p class="mt-4 text-sm panel-muted">
                Todavía no se ha convertido en alumno. Puedes abrir el formulario de alta con los datos ya cargados.
            </p>

            <div class="mt-5">
                <a href="{{ route('panel.preinscripciones.convertir', $preinscripcion) }}" class="panel-btn px-5 py-3 inline-flex">Convertir en alumno</a>
            </div>
        @endif
    </div>
</div>
@endsection