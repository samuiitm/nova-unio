@extends('layouts.panel')

@section('title', 'Horarios | Nova Unió')

@php
    $dias = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
        5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];

    // Agrupamos por grupo (solo lo de esta página de paginación)
    $porGrupo = $programaciones->getCollection()->groupBy('grupo_id');
@endphp

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Horarios (programaciones)</h1>
            <p class="mt-1 panel-muted">Agrupado por grupo.</p>
        </div>

        <a href="{{ route('panel.grupos.index') }}" class="panel-icon-btn px-5 py-3">Ver grupos</a>
    </div>

    <div class="mt-5 panel-card p-5">
        @if($programaciones->count() === 0)
            <div class="py-6 panel-muted">No hay horarios.</div>
        @else
            <div class="space-y-6">
                @foreach($porGrupo as $grupoId => $items)
                    @php
                        $grupo = $items->first()->grupo ?? null;

                        $itemsOrdenados = $items->sortBy(function ($p) {
                            return str_pad((string)$p->dia_semana, 2, '0', STR_PAD_LEFT) . '|' . (string)$p->hora_inicio;
                        })->values();
                    @endphp

                    <div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">
                                    {{ $grupo?->nombre ?? 'Grupo eliminado' }}
                                </h2>
                                <div class="text-xs panel-muted">
                                    {{ $itemsOrdenados->count() }} horario(s)
                                </div>
                            </div>

                            @if($grupo)
                                <a href="{{ route('panel.grupos.show', $grupo) }}"
                                   class="panel-icon-btn px-4 py-2">
                                    Ver grupo
                                </a>
                            @endif
                        </div>

                        <div class="mt-3 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-left panel-muted">
                                    <tr>
                                        <th class="py-2">Día</th>
                                        <th class="py-2">Hora</th>
                                        <th class="py-2">Vigencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemsOrdenados as $p)
                                        <tr class="border-t panel-border">
                                            <td class="py-3">
                                                {{ $dias[$p->dia_semana] ?? $p->dia_semana }}
                                            </td>

                                            <td class="py-3">
                                                {{ substr($p->hora_inicio, 0, 5) }} - {{ substr($p->hora_fin, 0, 5) }}
                                            </td>

                                            <td class="py-3 panel-muted">
                                                Desde {{ $p->vigente_desde ? $p->vigente_desde->format('d/m/Y') : '—' }}
                                                ·
                                                Hasta {{ $p->vigente_hasta ? $p->vigente_hasta->format('d/m/Y') : 'Sin fin' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $programaciones->links() }}
            </div>
        @endif
    </div>
@endsection