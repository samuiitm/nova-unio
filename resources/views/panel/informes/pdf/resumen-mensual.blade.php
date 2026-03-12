<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resumen mensual {{ $mes }}</title>
    <style>
        @page { margin: 24px 28px; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.4;
        }
        h1, h2, h3, p { margin: 0; }
        .header {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #d1d5db;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
        }
        .subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 11px;
        }
        .section {
            margin-top: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111827;
        }
        .grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-left: -8px;
            margin-right: -8px;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            vertical-align: top;
            background: #f9fafb;
        }
        .card-label {
            color: #6b7280;
            font-size: 10px;
            margin-bottom: 6px;
        }
        .card-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }
        .card-sub {
            margin-top: 4px;
            color: #6b7280;
            font-size: 10px;
        }
        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.report thead th {
            background: #f3f4f6;
            color: #374151;
            font-size: 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
            padding: 8px 9px;
        }
        table.report tbody td {
            border: 1px solid #e5e7eb;
            padding: 8px 9px;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .muted { color: #6b7280; }
        .two-col {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px;
            margin-left: -12px;
            margin-right: -12px;
        }
        .two-col td {
            vertical-align: top;
            width: 50%;
        }
        .mt-6 { margin-top: 24px; }
    </style>
</head>
<body>
@php
    $tituloMes = ucfirst($inicio->locale('es')->translatedFormat('F Y'));
    $k = $resumen['kpis'];
@endphp

<div class="header">
    <div class="title">Nova Unió - Resumen mensual</div>
    <div class="subtitle">Periodo: {{ $tituloMes }} ({{ $inicio->format('d/m/Y') }} - {{ $fin->format('d/m/Y') }})</div>
</div>

<div class="section">
    <div class="section-title">Resumen general</div>

    <table class="grid">
        <tr>
            <td class="card">
                <div class="card-label">Alumnos nuevos</div>
                <div class="card-value">{{ $k['alumnos_nuevos'] }}</div>
            </td>
            <td class="card">
                <div class="card-label">Activos al cierre</div>
                <div class="card-value">{{ $k['alumnos_activos_cierre'] }}</div>
            </td>
            <td class="card">
                <div class="card-label">Preinscripciones recibidas</div>
                <div class="card-value">{{ $k['preinscripciones_recibidas'] }}</div>
            </td>
            <td class="card">
                <div class="card-label">Preinscripciones convertidas</div>
                <div class="card-value">{{ $k['preinscripciones_convertidas'] }}</div>
            </td>
        </tr>
        <tr>
            <td class="card">
                <div class="card-label">Ingresos cobrados</div>
                <div class="card-value">{{ number_format((float)$k['ingresos'], 2, ',', '.') }} €</div>
            </td>
            <td class="card">
                <div class="card-label">Pagos registrados</div>
                <div class="card-value">{{ $k['pagos_total'] }}</div>
            </td>
            <td class="card">
                <div class="card-label">Ticket medio</div>
                <div class="card-value">{{ number_format((float)$k['ticket_medio'], 2, ',', '.') }} €</div>
            </td>
            <td class="card">
                <div class="card-label">Clases del mes</div>
                <div class="card-value">{{ $k['clases_total'] }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Clases y asistencia</div>

    <table class="grid">
        <tr>
            <td class="card">
                <div class="card-label">Clases canceladas</div>
                <div class="card-value">{{ $k['clases_canceladas'] }}</div>
            </td>
            <td class="card">
                <div class="card-label">Clases con lista</div>
                <div class="card-value">{{ $k['clases_con_lista'] }}</div>
                <div class="card-sub">
                    {{ $k['porcentaje_clases_con_lista'] !== null ? number_format((float)$k['porcentaje_clases_con_lista'], 1, ',', '.') . '%' : '—' }}
                </div>
            </td>
            <td class="card">
                <div class="card-label">Clases sin lista</div>
                <div class="card-value">{{ $k['clases_sin_lista'] }}</div>
            </td>
            <td class="card">
                <div class="card-label">Presencia</div>
                <div class="card-value">
                    {{ $k['porcentaje_presencia'] !== null ? number_format((float)$k['porcentaje_presencia'], 1, ',', '.') . '%' : '—' }}
                </div>
                <div class="card-sub">{{ $k['presentes'] }} presentes · {{ $k['ausentes'] }} ausentes</div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <table class="two-col">
        <tr>
            <td>
                <div class="section-title">Cobros por método</div>
                <table class="report">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Pagos</th>
                            <th class="text-right">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resumen['pagos_por_metodo'] as $fila)
                            <tr>
                                <td>{{ ucfirst($fila->metodo) }}</td>
                                <td>{{ (int) $fila->total_pagos }}</td>
                                <td class="text-right">{{ number_format((float)$fila->total_importe, 2, ',', '.') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="muted">No hay pagos este mes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            <td>
                <div class="section-title">Cobros por tipo de cuota</div>
                <table class="report">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Pagos</th>
                            <th class="text-right">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resumen['pagos_por_tipo'] as $fila)
                            <tr>
                                <td>{{ $fila->tipo }}</td>
                                <td>{{ (int) $fila->total_pagos }}</td>
                                <td class="text-right">{{ number_format((float)$fila->total_importe, 2, ',', '.') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="muted">No hay pagos este mes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Actividad por grupo</div>

    <table class="report">
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Clases</th>
                <th>Canceladas</th>
                <th>Presentes</th>
                <th>Ausentes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumen['grupos'] as $grupo)
                <tr>
                    <td>{{ $grupo->nombre }}</td>
                    <td>{{ (int) $grupo->clases_total }}</td>
                    <td>{{ (int) $grupo->clases_canceladas }}</td>
                    <td>{{ (int) $grupo->presentes }}</td>
                    <td>{{ (int) $grupo->ausentes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="muted">No hay actividad de grupos este mes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">Últimos pagos del mes</div>

    <table class="report">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Alumno</th>
                <th>Tipo</th>
                <th class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumen['ultimos_pagos'] as $p)
                <tr>
                    <td>{{ $p->fecha_pago?->format('d/m/Y') }}</td>
                    <td>
                        {{ $p->alumno?->apellidos ? $p->alumno->apellidos . ', ' . $p->alumno->nombre : ($p->alumno->nombre ?? '—') }}
                    </td>
                    <td>{{ $p->tipo_cuota_nombre ?: '—' }}</td>
                    <td class="text-right">{{ number_format((float)$p->importe, 2, ',', '.') }} €</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">No hay pagos este mes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($resumen['ultimas_preinscripciones']->count())
    <div class="section">
        <div class="section-title">Últimas preinscripciones del mes</div>

        <table class="report">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Modalidad</th>
                    <th>Nivel</th>
                    <th>Objetivo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen['ultimas_preinscripciones'] as $pre)
                    <tr>
                        <td>{{ optional($pre->created_at)->format('d/m/Y H:i') ?: '—' }}</td>
                        <td>{{ trim(($pre->nombre ?? '') . ' ' . ($pre->apellidos ?? '')) ?: 'Sin nombre' }}</td>
                        <td>{{ $pre->modalidad ?: '—' }}</td>
                        <td>{{ $pre->nivel ?: '—' }}</td>
                        <td>{{ $pre->objetivo ?: '—' }}</td>
                        <td>{{ $pre->estado ?: 'nueva' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
</body>
</html>