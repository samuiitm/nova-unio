<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Nova Unió')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/fxa0uin.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=League+Spartan:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen text-slate-100 bg-svg">
<header class="fixed inset-x-0 top-0 z-50" x-data="{ open: false }">
    <nav class="w-full px-4 lg:px-16 2xl:px-24 py-4 flex items-center justify-between">
        <a href="{{ route('public.home') }}" class="flex items-center gap-3">
        <img src="{{ Vite::asset('resources/img/logo.png') }}" alt="Nova Unió" class="h-10 sm:h-12 w-auto opacity-90">
        </a>

        <div class="flex items-center gap-3">
        <a href="{{ route('public.preinscripcion') }}"
            class="font-brand font-semibold uppercase tracking-wide not-italic bg-yellow-500 text-black px-3 py-2 text-sm sm:text-base">
            Preinscripción
        </a>

        <button class="inline-flex items-center justify-center text-white/80 hover:text-white"
                @click="open = !open" :aria-expanded="open.toString()" aria-label="Abrir menú">
            <svg class="w-8 h-8 sm:w-9 sm:h-9" viewBox="0 0 24 24" fill="none">
            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        </div>
    </nav>

    <!-- Menú -->
    <div x-show="open" x-cloak class="pb-4">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl border border-white/10 bg-black/60 backdrop-blur-sm p-4 grid gap-2 text-base">
            <a class="py-2 text-slate-200/80 hover:text-white" href="{{ route('public.elclub') }}">El club</a>
            <a class="py-2 text-slate-200/80 hover:text-white" href="{{ route('public.profesores') }}">Profesores</a>
            <a class="py-2 text-slate-200/80 hover:text-white" href="{{ route('public.horarios') }}">Horarios</a>
            <a class="py-2 text-slate-200/80 hover:text-white" href="{{ route('public.planes') }}">Planes</a>
            <a class="py-2 text-slate-200/80 hover:text-white" href="{{ route('public.faq') }}">FAQ</a>
            <a class="py-2 text-slate-200/80 hover:text-white" href="{{ route('public.contacto') }}">Contacto</a>
        </div>
        </div>
    </div>
</header>

<main class="mx-auto">
    @yield('content')
</main>

<footer>
    <div class="w-full text-xs px-4 lg:px-16 lg:text-lg 2xl:px-24 py-12 flex items-center justify-between">
        <p>© Club Esportiu Nova Unió {{ date('Y') }}. Diseñado y desarrollado por <a href="https://samuiitm.github.io" class="text-yellow-500 underline">samuiitm</a></p>
        <div class="flex items-center gap-4">
        <a href="{{ route('public.aviso-legal') }}" class="text-slate-200/80 hover:text-slate-200">Aviso Legal</a>
        <a href="{{ route('public.politica-privacidad') }}" class="text-slate-200/80 hover:text-slate-200">Política de Privacidad</a>
        <a href="{{ route('public.politica-cookies') }}" class="text-slate-200/80 hover:text-slate-200">Política de Cookies</a>
    </div>
</footer>
</body>
</html>