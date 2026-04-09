@extends('layouts.panel')

@section('title', 'Búsqueda global | Panel Nova Unió')

@section('content')
    <section class="space-y-6">
        <div class="panel-card p-5 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] panel-muted">Búsqueda global</p>
                    <h1 class="mt-2 text-2xl sm:text-3xl font-semibold">Resultados de búsqueda</h1>
                    <p class="mt-2 panel-muted">
                        @if($q !== '')
                            {{ $resultados['total'] }} resultado(s) para <span class="text-white font-medium">“{{ $q }}”</span>.
                        @else
                            Escribe algo para buscar alumnos, grupos, secciones y más.
                        @endif
                    </p>
                </div>

                <form method="GET" action="{{ route('panel.busqueda.index') }}" class="w-full sm:w-auto sm:min-w-[360px]">
                    <label for="busqueda-global-page" class="sr-only">Buscar en el panel</label>
                    <div class="panel-input h-12 flex items-center gap-3 px-4">
                        <svg class="h-5 w-5 opacity-60 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input
                            id="busqueda-global-page"
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            class="w-full min-w-0 bg-transparent border-none text-md focus:outline-none focus:ring-0"
                            placeholder="Busca alumnos, grupos, secciones..."
                            autocomplete="off"
                        >
                    </div>
                </form>
            </div>
        </div>

        @if($q !== '' && mb_strlen($q) < 2)
            <div class="panel-card p-5 sm:p-6">
                <p class="panel-muted">Escribe al menos 2 caracteres para lanzar la búsqueda.</p>
            </div>
        @elseif($q !== '' && empty($resultados['groups']))
            <div class="panel-card p-5 sm:p-6">
                <p class="text-base font-medium">No hay resultados para “{{ $q }}”.</p>
                <p class="mt-2 panel-muted">Prueba con otro nombre, teléfono, documento, email o sección del panel.</p>
            </div>
        @endif

        @foreach($resultados['groups'] as $grupo)
            <section class="panel-card p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $grupo['label'] }}</h2>
                        <p class="panel-muted text-sm">{{ $grupo['count'] }} resultado(s)</p>
                    </div>
                </div>

                <div class="divide-y divide-white/10">
                    @foreach($grupo['items'] as $item)
                        <a href="{{ $item['url'] }}" class="panel-search-result-item py-4 first:pt-0 last:pb-0 flex items-start gap-3">
                            @if(!empty($item['image_url']))
                                <img src="{{ $item['image_url'] }}" alt="" class="h-11 w-11 rounded-xl object-cover border panel-border shrink-0">
                            @elseif(!empty($item['color']))
                                <span class="mt-1 h-4 w-4 rounded-full shrink-0 border border-white/20" style="background-color: {{ $item['color'] }}"></span>
                            @else
                                <span class="mt-1 h-10 w-10 rounded-xl panel-icon-btn shrink-0 flex items-center justify-center text-xs uppercase tracking-wide">
                                    {{ mb_substr($item['title'], 0, 1) }}
                                </span>
                            @endif

                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-white truncate">{{ $item['title'] }}</span>
                                @if(!empty($item['subtitle']))
                                    <span class="mt-1 block text-sm panel-muted break-words">{{ $item['subtitle'] }}</span>
                                @endif
                                @if(!empty($item['meta']))
                                    <span class="mt-1 block text-xs text-white/70 break-words">{{ $item['meta'] }}</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </section>
@endsection