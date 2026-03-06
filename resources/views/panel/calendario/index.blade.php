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

    // reglas
    $limiteSinLista = now()->subDay()->toDateString();   // ayer (>= 1 día sin lista)
    $limiteBloqueo  = now()->subDays(2)->toDateString(); // hace 2 días (bloquear si sigue sin lista)
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Calendario</h1>
        <p class="mt-1 panel-muted">Pulsa una clase para pasar lista.</p>
    </div>

    <div class="flex items-center gap-2">
        <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.calendario', ['mes' => $mesAnterior]) }}">← Mes anterior</a>
        <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.calendario') }}">Hoy</a>
        <a class="panel-icon-btn px-5 py-3" href="{{ route('panel.calendario', ['mes' => $mesSiguiente]) }}">Mes siguiente →</a>
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
                $esHoy = $cursor->isToday();
                $esDelMes = $cursor->month === $base->month;
                $fechaStr = $cursor->toDateString();
                $lista = $clases->get($fechaStr, collect());

                $haySinLista = $lista->contains(function($c) use ($limiteSinLista) {
                    $total = (int) ($c->asistencias_total ?? 0);
                    return $c->estado !== 'cancelada'
                        && !$c->asistencia_cerrada
                        && $c->fecha <= $limiteSinLista
                        && $total === 0;
                });

                $hayPasada = $lista->contains(function($c) {
                    $total = (int) ($c->asistencias_total ?? 0);
                    return $c->estado !== 'cancelada'
                        && !$c->asistencia_cerrada
                        && $total > 0;
                });
            @endphp

            <div class="min-h-[120px] bg-white/[0.02] p-2" 
                    style="background: {{ $esDelMes ? 'rgb(255 255 255 / 0.02)' : 'rgb(255 255 255 / 0.01)' }};
                    {{ $esHoy ? 'outline: 2px solid rgb(var(--p-accent) / .90); outline-offset: -2px;' : '' }}">
                <div class="flex items-center justify-between">                    
                    <div class="text-sm font-semibold {{ $esDelMes ? '' : 'opacity-40' }} flex items-center gap-2">
                        <span>{{ $cursor->day }}</span>

                        @if($haySinLista)
                            <span class="inline-block w-2 h-2 rounded-full"
                                  style="background: rgb(255 205 140);"></span>
                        @elseif($hayPasada)
                            <span class="inline-block w-2 h-2 rounded-full"
                                  style="background: rgb(140 255 190);"></span>
                        @endif
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
                            $cerradaManual = (bool) $clase->asistencia_cerrada;

                            $total = (int) ($clase->asistencias_total ?? 0);

                            $bloqueadaSinLista = !$esCancelada && !$cerradaManual && ($clase->fecha <= $limiteBloqueo) && $total === 0;
                            $sinLista = !$esCancelada && !$cerradaManual && ($clase->fecha <= $limiteSinLista) && $total === 0 && !$bloqueadaSinLista;

                            $pasada = !$esCancelada && !$cerradaManual && $total > 0;

                            if ($esCancelada) {
                                $style = 'background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);';
                            } elseif ($cerradaManual) {
                                $style = 'background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);';
                            } elseif ($bloqueadaSinLista) {
                                $style = 'background: rgb(255 180 80 / .08); color: rgb(255 205 140 / .85); border: 1px solid rgb(255 180 80 / .18); opacity: .75;';
                            } elseif ($sinLista) {
                                $style = 'background: rgb(255 180 80 / .12); color: rgb(255 205 140); border: 1px solid rgb(255 180 80 / .22);';
                            } elseif ($pasada) {
                                $style = 'background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);';
                            } else {
                                $style = 'background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);';
                            }
                        @endphp

                        <a href="{{ route('panel.clases.show', $clase) }}?mes={{ $mes }}"
                           class="block rounded-xl px-2 py-1 text-xs"
                           style="{{ $style }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold">{{ substr($clase->hora_inicio,0,5) }}</span>

                                @if($cerradaManual)
                                    <span class="text-[10px] opacity-80">cerrada</span>
                                @elseif($bloqueadaSinLista)
                                    <span class="text-[10px] opacity-80">sin lista</span>
                                    <span class="text-[10px] opacity-70">bloq.</span>
                                @elseif($sinLista)
                                    <span class="text-[10px] opacity-80">sin lista</span>
                                @elseif($pasada)
                                    <span class="text-[10px] opacity-80">pasada</span>
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