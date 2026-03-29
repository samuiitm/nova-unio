<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Justificante de pago</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.4;
        }

        .header {
            margin-bottom: 24px;
        }

        .titulo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .subtitulo {
            color: #666;
            font-size: 12px;
        }

        .bloque {
            margin-bottom: 18px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        .tabla th {
            background: #f5f5f5;
            width: 32%;
        }

        .total {
            margin-top: 18px;
            font-size: 18px;
            font-weight: bold;
        }

        .footer {
            margin-top: 32px;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="titulo">Justificante de pago</div>
        <div class="subtitulo">Nova Unió</div>
    </div>

    <div class="bloque">
        <table class="tabla">
            <tr>
                <th>Alumno</th>
                <td>{{ $pago->alumno?->nombre }} {{ $pago->alumno?->apellidos }}</td>
            </tr>
            <tr>
                <th>Documento identificativo</th>
                <td>{{ $pago->alumno?->dni ?: '—' }}</td>
            </tr>
            <tr>
                <th>Fecha de pago</th>
                <td>{{ $pago->fecha_pago?->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Concepto</th>
                <td>{{ $pago->tipo_cuota_nombre ?? ($pago->cuota->tipoCuota->nombre ?? 'Cuota') }}</td>
            </tr>
            <tr>
                <th>Vigencia</th>
                <td>
                    {{ $pago->vigencia_inicio ? \Carbon\Carbon::parse($pago->vigencia_inicio)->format('d/m/Y') : '—' }}
                    -
                    {{ $pago->vigencia_fin ? \Carbon\Carbon::parse($pago->vigencia_fin)->format('d/m/Y') : '—' }}
                </td>
            </tr>
            <tr>
                <th>Mes pagado</th>
                <td>{{ $pago->notas ?: '—' }}</td>
            </tr>
            <tr>
                <th>Referencia</th>
                <td>Pago #{{ $pago->id }}</td>
            </tr>
        </table>
    </div>

    <div class="total">
        Total abonado: {{ number_format((float) $pago->importe, 2, ',', '.') }} €
    </div>

    <div class="footer">
        Documento generado automáticamente por Nova Unió.
    </div>
</body>
</html>