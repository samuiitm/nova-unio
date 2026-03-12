<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" data-area="panel">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">

    <title>@yield('title', 'Error del panel | Nova Unió')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

@php
    $panelHome = \Illuminate\Support\Facades\Route::has('panel.home') ? route('panel.home') : url('/panel');
    $publicHome = \Illuminate\Support\Facades\Route::has('public.home') ? route('public.home') : url('/');
@endphp

<body class="panel-body min-h-screen antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="h-20 border-b panel-border panel-topbar">
            <div class="h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ Vite::asset('resources/img/logo-novaunio.svg') }}" class="h-12 w-12 opacity-90" alt="Nova Unió">

                    <div class="min-w-0">
                        <div class="text-lg font-semibold leading-5">Nova Unió</div>
                        <div class="text-xs panel-muted">Panel privat</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ $publicHome }}" class="panel-icon-btn px-4 py-2 text-sm">
                        Web pública
                    </a>

                    <a href="{{ $panelHome }}" class="panel-btn px-4 py-2 text-sm">
                        Ir al panel
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>
</html>