@extends('layouts.panel')

@section('title', 'Dashboard | Nova Unió')

@section('content')
    @php
        $saludo = match($rol) {
            'admin' => 'Vista general de administración del club.',
            'entrenador_admin' => 'Vista general de gestión del club.',
            default => 'Vista operativa para control de clases y asistencias.',
        };

        $iconos = [
            'alumnos' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'cuotas' => '<path d="M21 12a9 9 0 1 1-9-9"/><path d="M21 3v9h-9"/>',
            'calendario' => '<path d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7H4v12a2 2 0 0 0 2 2Z"/>',
            'preinscripciones' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>',
            'asistencias' => '<path d="M8 17l4 4 4-4M12 3v18"/>',
            'nuevos' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>',
            'clases' => '<path d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7H4v12a2 2 0 0 0 2 2Z"/>',
        ];

        $fmtFecha = function ($fecha) {
            return \Carbon\Carbon::parse($fecha)->format('d/m/Y');
        };

        $fmtHora = function ($hora) {
            return $hora ? substr($hora, 0, 5) : '—';
        };

        $labelDia = function ($fecha) {
            $d = \Carbon\Carbon::parse($fecha)->startOfDay();
            if ($d->isToday()) return 'Hoy';
            if ($d->isTomorrow()) return 'Mañana';
            return $d->format('d/m/Y');
        };
    @endphp

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-semibold">Dashboard</h1>
            <p class="mt-1 panel-muted">{{ $saludo }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('panel.calendario') }}" class="panel-icon-btn px-5 py-3">
                Abrir calendario
            </a>

            @if($modo === 'gestion')
                <a href="{{ route('panel.informes.resumen') }}" class="panel-btn px-5 py-3">
                    Ver resumen mensual
                </a>
            @endif
        </div>
    </div>

    {{-- KPIs --}}
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
            <a href="{{ $card['link'] }}"
               class="panel-card p-6 block transition hover:-translate-y-[1px]">
                <div class="flex items-start justify-between gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                         style="background: rgb(var(--p-accent) / .12); border: 1px solid rgb(var(--p-accent) / .12);">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            {!! $iconos[$card['icono']] ?? $iconos['calendario'] !!}
                        </svg>
                    </div>
                </div>

                <div class="mt-10">
                    <div class="text-sm panel-muted">{{ $card['titulo'] }}</div>
                    <div class="mt-2 text-4xl font-semibold leading-none">{{ $card['valor'] }}</div>
                </div>
            </a>
        @endforeach
    </div>

    @if($modo === 'gestion' && count($cardsSecundarias))
        <div class="mt-4 grid gap-4 xl:grid-cols-2">
            @foreach($cardsSecundarias as $card)
                <div class="panel-card p-6">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                         style="background: rgb(var(--p-accent) / .12); border: 1px solid rgb(var(--p-accent) / .12);">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            {!! $iconos[$card['icono']] ?? $iconos['calendario'] !!}
                        </svg>
                    </div>

                    <div class="mt-8">
                        <div class="text-sm panel-muted">{{ $card['titulo'] }}</div>
                        <div class="mt-2 text-4xl font-semibold leading-none">{{ $card['valor'] }}</div>
                        <div class="mt-2 text-sm panel-muted">{{ $card['texto'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($modo === 'gestion')
        <div class="mt-5 grid gap-5 xl:grid-cols-[1.6fr,1fr]">
            <div class="panel-card overflow-hidden">
                <div class="px-6 py-5 border-b panel-border">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">Cuotas pendientes</h2>
                            <p class="mt-1 text-sm panel-muted">Alumnos a revisar o cobrar.</p>
                        </div>

                        <a href="{{ route('panel.pagos.pendientes') }}" class="text-sm" style="color: rgb(var(--p-accent));">
                            Ver todas
                        </a>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left panel-muted">
                            <tr>
                                <th class="py-2">Nombre</th>
                                <th class="py-2">Grupo</th>
                                <th class="py-2">Tipo</th>
                                <th class="py-2 text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cuotasPendientes as $cuota)
                                <tr class="border-t panel-border">
                                    <td class="py-3 font-medium">
                                        {{ $cuota->alumno?->nombre }} {{ $cuota->alumno?->apellidos }}
                                    </td>
                                    <td class="py-3 panel-muted">
                                        {{ optional($cuota->alumno?->gruposActivos->first())->nombre ?: '—' }}
                                    </td>
                                    <td class="py-3 panel-muted">
                                        {{ $cuota->tipoCuota?->nombre ?: '—' }}
                                    </td>
                                    <td class="py-3 text-right">
                                        {{ number_format((float) $cuota->importe, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 panel-muted">No hay cuotas pendientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-5">
                <div class="panel-card overflow-hidden">
                    <div class="px-6 py-5 border-b panel-border">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold">Próximas clases</h2>
                                <p class="mt-1 text-sm panel-muted">Agenda inmediata del club.</p>
                            </div>

                            <a href="{{ route('panel.calendario') }}" class="text-sm" style="color: rgb(var(--p-accent));">
                                Ver calendario
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @forelse($proximasClases as $clase)
                            <a href="{{ route('panel.clases.show', ['clase' => $clase, 'mes' => \Carbon\Carbon::parse($clase->fecha)->format('Y-m')]) }}"
                               class="block">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-medium">{{ $labelDia($clase->fecha) }}</div>
                                        <div class="mt-1 text-sm panel-muted">
                                            {{ $fmtHora($clase->hora_inicio) }} · {{ $clase->grupo?->nombre ?: 'Sin grupo' }}
                                        </div>
                                    </div>

                                    <div class="text-sm panel-muted">
                                        {{ $fmtFecha($clase->fecha) }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-sm panel-muted">No hay próximas clases.</div>
                        @endforelse
                    </div>
                </div>

                <div class="panel-card overflow-hidden">
                    <div class="px-6 py-5 border-b panel-border">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold">Preinscripciones recientes</h2>
                                <p class="mt-1 text-sm panel-muted">Entradas nuevas y en proceso.</p>
                            </div>

                            <a href="{{ route('panel.preinscripciones.index') }}" class="text-sm" style="color: rgb(var(--p-accent));">
                                Ver todas
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @forelse($preinscripcionesRecientes as $pre)
                            <a href="{{ route('panel.preinscripciones.show', $pre) }}" class="block">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-medium">
                                            {{ trim(($pre->nombre ?? '') . ' ' . ($pre->apellidos ?? '')) ?: 'Sin nombre' }}
                                        </div>
                                        <div class="mt-1 text-sm panel-muted">
                                            {{ $pre->modalidad ?: 'Sin modalidad' }}
                                            @if($pre->nivel)
                                                · {{ $pre->nivel }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-xs px-3 py-1 rounded-full"
                                         style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                                        {{ ucfirst(str_replace('_', ' ', $pre->estado ?? 'nueva')) }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-sm panel-muted">No hay preinscripciones abiertas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mt-5 grid gap-5 xl:grid-cols-[1.6fr,1fr]">
            <div class="panel-card overflow-hidden">
                <div class="px-6 py-5 border-b panel-border">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">Próximas clases</h2>
                            <p class="mt-1 text-sm panel-muted">Acceso rápido para pasar lista.</p>
                        </div>

                        <a href="{{ route('panel.calendario') }}" class="text-sm" style="color: rgb(var(--p-accent));">
                            Ver calendario
                        </a>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($proximasClases as $clase)
                        <a href="{{ route('panel.clases.show', ['clase' => $clase, 'mes' => \Carbon\Carbon::parse($clase->fecha)->format('Y-m')]) }}"
                           class="block rounded-2xl border panel-border p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-medium">{{ $clase->grupo?->nombre ?: 'Sin grupo' }}</div>
                                    <div class="mt-1 text-sm panel-muted">
                                        {{ $labelDia($clase->fecha) }} · {{ $fmtHora($clase->hora_inicio) }} - {{ $fmtHora($clase->hora_fin) }}
                                    </div>
                                </div>

                                <div class="text-sm" style="color: rgb(var(--p-accent));">
                                    Abrir
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-sm panel-muted">No hay clases próximas.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel-card overflow-hidden">
                <div class="px-6 py-5 border-b panel-border">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold">Clases sin lista</h2>
                            <p class="mt-1 text-sm panel-muted">Para no dejar asistencias atrás.</p>
                        </div>

                        <a href="{{ route('panel.asistencias.index') }}" class="text-sm" style="color: rgb(var(--p-accent));">
                            Ver historial
                        </a>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($clasesSinLista as $clase)
                        <a href="{{ route('panel.clases.show', ['clase' => $clase, 'mes' => \Carbon\Carbon::parse($clase->fecha)->format('Y-m')]) }}"
                           class="block">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-medium">{{ $clase->grupo?->nombre ?: 'Sin grupo' }}</div>
                                    <div class="mt-1 text-sm panel-muted">
                                        {{ $labelDia($clase->fecha) }} · {{ $fmtHora($clase->hora_inicio) }}
                                    </div>
                                </div>

                                <div class="text-sm panel-muted">
                                    {{ $fmtFecha($clase->fecha) }}
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-sm panel-muted">No hay clases pendientes de lista.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
@endsection