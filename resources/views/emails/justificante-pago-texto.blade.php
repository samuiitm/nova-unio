<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Justificante de pago</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222;">
    <p>Hola{{ $pago->alumno?->nombre ? ' ' . $pago->alumno->nombre : '' }},</p>

    <p>Adjuntamos el justificante en PDF del pago registrado.</p>

    <p>Gracias,<br>Nova Unió</p>
</body>
</html>