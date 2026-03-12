<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" data-area="panel">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inicia sesión | Nova Unió</title>

    <!-- Outfit (solo panel) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="panel-body min-h-screen antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-[620px]">
            {{ $slot }}
        </div>
    </div>
</body>
</html>