<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nova Unió')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
<header class="border-b border-slate-800">
    <nav class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between">
        <a href="{{ route('public.home') }}" class="font-semibold tracking-wide">Nova Unió</a>

        <div class="flex gap-4 text-sm">
            <a class="text-slate-300 hover:text-white" href="{{ route('public.elclub') }}">El club</a>
            <a class="text-slate-300 hover:text-white" href="{{ route('public.profesores') }}">Profesores</a>
            <a class="text-slate-300 hover:text-white" href="{{ route('public.horarios') }}">Horarios</a>
            <a class="text-slate-300 hover:text-white" href="{{ route('public.planes') }}">Planes</a>
            <a class="text-slate-300 hover:text-white" href="{{ route('public.faq') }}">FAQ</a>
            <a class="text-slate-300 hover:text-white" href="{{ route('public.contacto') }}">Contacto</a>
            <a class="px-3 py-1 rounded-md bg-slate-800 hover:bg-slate-700" href="{{ route('public.preinscripcion') }}">
                Preinscripción
            </a>
        </div>
    </nav>
</header>

<main class="mx-auto max-w-6xl px-4 py-10">
    @yield('content')
</main>

<footer class="border-t border-slate-800">
    <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-slate-400">
        © {{ date('Y') }} Nova Unió · MMA & Sambo
    </div>
</footer>
</body>
</html>