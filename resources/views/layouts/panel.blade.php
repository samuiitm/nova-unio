<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">

    <title>@yield('title', 'Dashboard | Nova Unió')</title>

    <!-- Outfit (solo panel) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>[x-cloak]{display:none!important}</style>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="panel-body min-h-screen antialiased" x-data="{ sidebarOpen:false }">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/60" @click="sidebarOpen=false"></div>

    <aside
        class="fixed z-50 inset-y-0 left-0 w-[280px] border-r border-white/10 bg-[#070815]/80 backdrop-blur
               transform transition md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        <div class="px-5 py-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <img src="{{ Vite::asset('resources/img/logo-novaunio.svg') }}" class="h-10 w-10 opacity-90" alt="Nova Unió">
                <div>
                    <div class="text-xl font-semibold leading-5">Nova Unió</div>
                    <div class="text-sm panel-muted">Área privada</div>
                </div>
            </div>
        </div>

        @php
            $item = fn($route) => request()->routeIs($route)
                ? 'bg-indigo-500/15 text-indigo-200 border border-indigo-500/25'
                : 'text-white/75 hover:bg-white/5 border border-transparent';
        @endphp

        <nav class="px-3 py-4 space-y-1">
            <div class="px-3 pb-2 text-xs uppercase tracking-wider text-white/35">Menu</div>

            <a href="{{ route('panel.home') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl {{ $item('panel.home') }}">
                <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-8h8V3h-8v10z"/>
                </svg>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span class="text-sm font-medium">Alumnos</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    <path d="M15 9h-3a2 2 0 1 0 0 4h1a2 2 0 1 1 0 4H9"/>
                    <path d="M12 7v2m0 8v2"/>
                </svg>
                <span class="text-sm font-medium">Pagos y cuotas</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                <span class="text-sm font-medium">Grupos</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7H4v12a2 2 0 0 0 2 2Z"/>
                </svg>
                <span class="text-sm font-medium">Calendario</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 17l4 4 4-4M12 3v18"/>
                </svg>
                <span class="text-sm font-medium">Asistencias</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19V5m4 14V9m4 10V3m4 16v-6m4 6V7"/>
                </svg>
                <span class="text-sm font-medium">Informes</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/40 cursor-not-allowed">
                <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="text-sm font-medium">Usuarios</span>
            </a>

            <div class="pt-3 px-3 border-t border-white/10">
                <a href="{{ route('public.home') }}" class="text-sm text-white/60 hover:text-white">
                    Volver a la web pública →
                </a>
            </div>
        </nav>
    </aside>

    <div class="md:pl-[280px] min-h-screen">
        <header class="sticky top-0 z-30 border-b border-white/10 bg-[#070815]/65 backdrop-blur">
            <div class="px-5 py-4 flex items-center gap-4">
                <button class="md:hidden panel-input p-2" @click="sidebarOpen=true" aria-label="Abrir menú">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1">
                    <div class="panel-input flex items-center gap-3 px-4 py-2.5">
                        <svg class="h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="text"
                               class="w-full bg-transparent outline-none text-sm"
                               placeholder="Busca alumnos, secciones, grupos...">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="panel-input p-2" aria-label="Tema">
                        <svg class="h-5 w-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3a6 6 0 0 0 0 12 6 6 0 0 0 0-12Z"/>
                            <path d="M12 3v12"/>
                        </svg>
                    </button>

                    <button class="panel-input p-2" aria-label="Notificaciones">
                        <svg class="h-5 w-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8a6 6 0 10-12 0c0 7-3 7-3 7h18s-3 0-3-7"/>
                            <path d="M13.7 21a2 2 0 01-3.4 0"/>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="h-9 w-9 rounded-full bg-white/10 border border-white/10"></div>
                        <div class="hidden sm:block">
                            <div class="text-sm font-semibold leading-4">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-xs panel-muted">{{ auth()->user()->role ?? 'admin' }}</div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="ml-2">
                            @csrf
                            <button class="panel-input px-3 py-2 text-sm">Salir</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>