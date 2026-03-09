@extends('layouts.panel')

@section('title', 'Preinscripciones | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Preinscripciones</h1>
        <p class="mt-1 panel-muted">Revisa solicitudes, mira el contexto completo y conviértelas en alumno cuando toque.</p>
    </div>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Nuevas</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['nuevas'] }}</div>
    </div>
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">En proceso</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['en_proceso'] }}</div>
    </div>
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Resueltas</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['resueltas'] }}</div>
    </div>
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Total</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['total'] }}</div>
    </div>
</div>

<div class="mt-5 panel-card p-5">
    <form method="GET" class="grid gap-3 lg:grid-cols-[1fr,220px,auto]">
        <input name="q" value="{{ $q }}" class="panel-input px-4 py-3" placeholder="Buscar por nombre, email, modalidad, objetivo...">

        <select name="estado" class="panel-input px-4 py-3">
            <option value="todas" @selected($estado === 'todas')>Todas</option>
            <option value="nueva" @selected($estado === 'nueva')>Nuevas</option>
            <option value="en_proceso" @selected($estado === 'en_proceso')>En proceso</option>
            <option value="resuelta" @selected($estado === 'resuelta')>Resueltas</option>
        </select>

        <button class="panel-btn px-5 py-3">Filtrar</button>
    </form>
</div>

<div class="mt-5 space-y-4">
    @forelse($preinscripciones as $p)
        @php
            $estadoColor = match($p->estado) {
                'resuelta' => 'background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);',
                'en_proceso' => 'background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);',
                default => 'background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .75); border: 1px solid rgb(255 255 255 / .10);',
            };
        @endphp

        <div class="panel-card p-5">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold">{{ trim(($p->nombre ?? '') . ' ' . ($p->apellidos ?? '')) ?: 'Sin nombre' }}</h2>
                        <span class="text-xs px-3 py-1 rounded-full" style="{{ $estadoColor }}">
                            {{ ucfirst(str_replace('_', ' ', $p->estado)) }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm panel-muted">Entrada: {{ optional($p->created_at)->format('d/m/Y H:i') ?: '—' }}</p>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('panel.preinscripciones.show', $p) }}" class="panel-icon-btn px-4 py-2">Ver</a>

                    @if($p->estado === 'resuelta' && $p->alumno_id)
                        <a href="{{ route('panel.alumnos.show', $p->alumno_id) }}" class="panel-btn px-4 py-2">Ver alumno</a>
                    @else
                        <a href="{{ route('panel.preinscripciones.convertir', $p) }}" class="panel-btn px-4 py-2">Convertir en alumno</a>
                    @endif
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4 text-sm">
                <div>
                    <div class="panel-muted">Email</div>
                    <div class="mt-1">{{ $p->email ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Teléfono</div>
                    <div class="mt-1">{{ $p->telefono ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Edad</div>
                    <div class="mt-1">{{ $p->edad ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Modalidad</div>
                    <div class="mt-1 font-medium">{{ $p->modalidad ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Nivel</div>
                    <div class="mt-1">{{ $p->nivel ?: '—' }}</div>
                </div>
                <div>
                    <div class="panel-muted">Objetivo</div>
                    <div class="mt-1">{{ $p->objetivo ?: '—' }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="panel-muted">Alumno vinculado</div>
                    <div class="mt-1">
                        @if($p->alumno)
                            {{ $p->alumno->nombre }} {{ $p->alumno->apellidos }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>

            @if($p->mensaje)
                <div class="mt-5 rounded-2xl border panel-border p-4 text-sm" style="background: rgb(255 255 255 / .03);">
                    <div class="panel-muted mb-2">Mensaje</div>
                    <div class="whitespace-pre-line">{{ \Illuminate\Support\Str::limit($p->mensaje, 220) }}</div>
                </div>
            @endif
        </div>
    @empty
        <div class="panel-card p-6 panel-muted">No hay preinscripciones con esos filtros.</div>
    @endforelse
</div>

<div class="mt-5 flex items-center justify-between gap-3">
    <div class="text-xs panel-muted">
        Mostrando {{ $preinscripciones->firstItem() ?? 0 }}-{{ $preinscripciones->lastItem() ?? 0 }} de {{ $preinscripciones->total() }} preinscripciones
    </div>
    <div>
        {{ $preinscripciones->links() }}
    </div>
</div>
@endsection