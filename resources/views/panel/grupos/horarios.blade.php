@extends('layouts.panel')

@section('title', 'Horarios | Nova Unió')

@php
    $dias = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
        5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];
@endphp

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Horarios (programaciones)</h1>
            <p class="mt-1 panel-muted">Todos los horarios de todos los grupos.</p>
        </div>

        <a href="{{ route('panel.grupos.index') }}" class="panel-icon-btn px-5 py-3">Ver grupos</a>
    </div>

    <div class="mt-5 panel-card p-5">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left panel-muted">
                    <tr>
                        <th class="py-2">Grupo</th>
                        <th class="py-2">Día</th>
                        <th class="py-2">Horario</th>
                        <th class="py-2">Vigencia</th>
                        <th class="py-2 text-right">Ir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programaciones as $p)
                        <tr class="border-t panel-border">
                            <td class="py-3 font-medium">{{ $p->grupo->nombre ?? '—' }}</td>
                            <td class="py-3">{{ $dias[$p->dia_semana] ?? $p->dia_semana }}</td>
                            <td class="py-3">{{ substr($p->hora_inicio,0,5) }} - {{ substr($p->hora_fin,0,5) }}</td>
                            <td class="py-3 panel-muted">
                                Desde {{ $p->vigente_desde ? $p->vigente_desde->format('d/m/Y') : '—' }}
                                ·
                                Hasta {{ $p->vigente_hasta ? $p->vigente_hasta->format('d/m/Y') : 'Sin fin' }}
                            </td>
                            <td class="py-3 text-right">
                                @if($p->grupo)
                                    <a class="panel-icon-btn px-4 py-2 inline-flex items-center"
                                       href="{{ route('panel.grupos.show', $p->grupo) }}">Ver</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 panel-muted">No hay horarios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $programaciones->links() }}
        </div>
    </div>
@endsection