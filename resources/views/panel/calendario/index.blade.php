@extends('layouts.panel')

@section('title', 'Calendario | Nova Unió')

@section('content')
@php
    $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

    $base = \Carbon\Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
    $mesAnterior = $base->copy()->subMonth()->format('Y-m');
    $mesSiguiente = $base->copy()->addMonth()->format('Y-m');

    $cursor = $inicio->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
    $finCalendario = $fin->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Calendario</h1>
        <p class="mt-1 panel-muted">Pulsa una clase para pasar lista.</p>
    </div>

    <div class="flex items-center gap-2">
        <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.calendario', ['mes' => $mesAnterior]) }}">←</a>
        <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.calendario') }}">Hoy</a>
        <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.calendario', ['mes' => $mesSiguiente]) }}">→</a>
    </div>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 panel-card p-5">
    <div class="flex items-center justify-between gap-3">
        <div class="text-lg font-semibold">
            {{ $base->translatedFormat('F Y') }}
        </div>
        <div class="text-sm panel-muted">
            {{ $inicio->format('d/m/Y') }} - {{ $fin->format('d/m/Y') }}
        </div>
    </div>

    <div class="mt-4 grid grid-cols-7 gap-px rounded-2xl overflow-hidden"
         style="background: rgb(var(--p-border) / var(--p-border-a));">

        @foreach($dias as $d)
            <div class="bg-white/5 px-3 py-2 text-xs font-semibold panel-muted uppercase tracking-wider">
                {{ $d }}
            </div>
        @endforeach

        @while($cursor <= $finCalendario)
            @php
                $esDelMes = $cursor->month === $base->month;
                $fechaStr = $cursor->toDateString();
                $lista = $clases->get($fechaStr, collect());
            @endphp

            <div class="min-h-[120px] bg-white/[0.02] p-2">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold {{ $esDelMes ? '' : 'opacity-40' }}">
                        {{ $cursor->day }}
                    </div>
                    @if(!$esDelMes)
                        <div class="text-xs panel-muted opacity-60">
                            {{ $cursor->translatedFormat('M') }}
                        </div>
                    @endif
                </div>

                <div class="mt-2 space-y-1">
                    @foreach($lista as $clase)
                        @php
                            $esCancelada = $clase->estado === 'cancelada';
                        @endphp

                        <a href="{{ route('panel.clases.show', $clase) }}?mes={{ $mes }}"
                           class="block rounded-xl px-2 py-1 text-xs"
                           style="{{ $esCancelada
                                ? 'background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);'
                                : 'background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);'
                           }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold">{{ substr($clase->hora_inicio,0,5) }}</span>

                                @if($clase->asistencia_cerrada)
                                    <span class="text-[10px] opacity-80">cerrada</span>
                                @endif
                            </div>
                            <div class="opacity-90">{{ $clase->grupo->nombre }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            @php $cursor->addDay(); @endphp
        @endwhile
    </div>
</div>
@endsection