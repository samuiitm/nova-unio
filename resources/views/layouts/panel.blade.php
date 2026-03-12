<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" data-area="panel">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">

    <title>@yield('title', 'Panel | Nova Unió')</title>

    <!-- Outfit (solo panel) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>[x-cloak]{display:none!important}</style>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

@php
    $authUser = auth()->user();

    $puedeGestionClub = $authUser?->puedeGestionarClub() ?? false;
    $puedeGestionarUsuarios = $authUser?->puedeGestionarUsuarios() ?? false;

    $is = fn($pattern) => request()->routeIs($pattern);

    $aDashboard = $is('panel.home');

    $aAlumnos   = $is('panel.alumnos.*');
    $aPagos     = $is('panel.pagos.*');
    $aGrupos    = $is('panel.grupos.*');

    $aCalendario       = $is('panel.calendario');
    $aAsistencias      = $is('panel.asistencias.*');
    $aPreinscripciones = $is('panel.preinscripciones.*');

    $aInformes  = $is('panel.informes.*');
    $aUsuarios  = $is('panel.usuarios.*');

    // helper: si una ruta no existe aún, devuelve '#'
    $r = fn($name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : '#';
    $esAdmin = auth()->check() && auth()->user()->esAdmin();

    // accordion: abre por defecto el grupo del route actual
    $initialOpen =
        ($puedeGestionClub && $aAlumnos) ? 'alumnos' :
        (($puedeGestionClub && $aPagos) ? 'pagos' :
        (($puedeGestionClub && $aGrupos) ? 'grupos' :
        (($puedeGestionClub && $aInformes) ? 'informes' :
        (($puedeGestionarUsuarios && $aUsuarios) ? 'usuarios' : null))));
@endphp

<body class="panel-body min-h-screen antialiased"
      x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
        openKey: @js($initialOpen),
        toggle(key){
          this.openKey = (this.openKey === key) ? null : key;
        }
      }"
      x-effect="document.documentElement.classList.toggle('overflow-hidden', sidebarOpen)"
      @keydown.escape.window="sidebarOpen=false">

    <!-- Overlay mobile -->
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/60" @click="sidebarOpen=false"></div>

    <!-- Sidebar -->
    <aside
        class="fixed z-50 inset-y-0 left-0 border-r panel-border panel-shell
            transform md:translate-x-0 w-[280px]
            transition-[width,transform] duration-200 ease-in-out"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
            sidebarCollapsed ? 'md:w-[84px]' : 'md:w-[280px]'
        ]">

        <!-- Sidebar top (misma altura que topbar) -->
        <div class="h-20 px-4 border-b panel-border flex items-center">
            <div class="flex items-center gap-3 w-full">
                <img src="{{ Vite::asset('resources/img/logo-novaunio.svg') }}" class="h-12 w-12 opacity-90" alt="Nova Unió">

                <div class="min-w-0" x-show="!sidebarCollapsed" x-cloak>
                    <div class="text-lg font-semibold leading-5">Nova Unió</div>
                    <div class="text-xs panel-muted">Panel privat</div>
                </div>
            </div>
        </div>

        <nav class="px-3 py-4">
            <div class="px-3 pb-2 text-xs uppercase tracking-wider panel-muted"
                 x-show="!sidebarCollapsed" x-cloak>
                Menú
            </div>
            
            <!-- Dashboard -->
            @if($puedeGestionarUsuarios)
                <a href="{{ $r('panel.home') }}" @click="sidebarOpen=false"
                class="flex items-center gap-3 px-3 py-2 panel-nav-item {{ $aDashboard ? 'panel-nav-item-active' : '' }}"
                :class="sidebarCollapsed ? 'justify-center' : ''">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-8h8V3h-8v10z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Dashboard</span>
                </a>
            @endif

            <!-- ALUMNOS -->
            @if($puedeGestionClub) 
                <button type="button"
                        class="mt-1 w-full flex items-center gap-3 px-3 py-2 panel-nav-group-btn {{ $aAlumnos ? 'panel-nav-group-btn-active' : '' }}"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                        @click="if(sidebarCollapsed){ sidebarCollapsed=false } toggle('alumnos')">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <span class="flex-1 text-left text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Alumnos</span>

                    <span class="w-6 shrink-0 flex items-center justify-center" x-show="!sidebarCollapsed" x-cloak>
                        <svg class="h-4 w-4 opacity-70 transition" :class="openKey === 'alumnos' ? 'rotate-90' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </span>
                </button>

                <div x-show="!sidebarCollapsed && openKey === 'alumnos'" x-collapse class="pl-3 pr-1 mt-1 space-y-1">
                    <a href="{{ $r('panel.alumnos.index') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.alumnos.index') ? 'panel-subitem-active' : '' }}">
                        Listado de alumnos
                    </a>
                    <a href="{{ $r('panel.alumnos.create') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.alumnos.create') ? 'panel-subitem-active' : '' }}">
                        Crear alumno
                    </a>
                </div>
            @endif

            <!-- PAGOS Y CUOTAS -->
            @if($puedeGestionClub)
                <button type="button"
                        class="mt-1 w-full flex items-center gap-3 px-3 py-2 panel-nav-group-btn"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                        @click="if(sidebarCollapsed){ sidebarCollapsed=false } toggle('pagos')">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            <path d="M15 9h-3a2 2 0 1 0 0 4h1a2 2 0 1 1 0 4H9"/>
                            <path d="M12 7v2m0 8v2"/>
                        </svg>
                    </span>
                    <span class="flex-1 text-left text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Pagos y cuotas</span>

                    <span class="w-6 shrink-0 flex items-center justify-center" x-show="!sidebarCollapsed" x-cloak>
                        <svg class="h-4 w-4 opacity-70 transition" :class="openKey === 'pagos' ? 'rotate-90' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </span>
                </button>

                <div x-show="!sidebarCollapsed && openKey === 'pagos'" x-collapse class="pl-3 pr-1 mt-1 space-y-1">
                    <a href="{{ $r('panel.pagos.vencidas') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.pagos.vencidas') ? 'panel-subitem-active' : '' }}">
                        Cuotas vencidas
                    </a>
                    <a href="{{ $r('panel.pagos.pendientes') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.pagos.pendientes') ? 'panel-subitem-active' : '' }}">
                        Pendientes de pago
                    </a>
                    <a href="{{ $r('panel.pagos.historial') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.pagos.historial') ? 'panel-subitem-active' : '' }}">
                        Historial de pagos
                    </a>
                    <a href="{{ $r('panel.pagos.tipos') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.pagos.tipos') ? 'panel-subitem-active' : '' }}">
                        Tipos de cuota
                    </a>
                </div>
            @endif

            <!-- GRUPOS -->
            @if($puedeGestionClub)
                <button type="button"
                        class="mt-1 w-full flex items-center gap-3 px-3 py-2 panel-nav-group-btn"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                        @click="if(sidebarCollapsed){ sidebarCollapsed=false } toggle('grupos')">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                    </span>
                    <span class="flex-1 text-left text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Grupos</span>

                    <span class="w-6 shrink-0 flex items-center justify-center" x-show="!sidebarCollapsed" x-cloak>
                        <svg class="h-4 w-4 opacity-70 transition" :class="openKey === 'grupos' ? 'rotate-90' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </span>
                </button>

                <div x-show="!sidebarCollapsed && openKey === 'grupos'" x-collapse class="pl-3 pr-1 mt-1 space-y-1">
                    <a href="{{ $r('panel.grupos.index') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.grupos.index') ? 'panel-subitem-active' : '' }}">
                        Listado de grupos
                    </a>
                    <a href="{{ $r('panel.grupos.create') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.grupos.create') ? 'panel-subitem-active' : '' }}">
                        Crear grupo
                    </a>
                </div>
            @endif

            <!-- Calendario (suelto) -->
            <a href="{{ $r('panel.calendario') }}" @click="sidebarOpen=false"
               class="mt-2 flex items-center gap-3 px-3 py-2 panel-nav-item {{ $aCalendario ? 'panel-nav-item-active' : '' }}"
               :class="sidebarCollapsed ? 'justify-center' : ''">
                <span class="w-6 shrink-0 flex items-center justify-center">
                    <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7H4v12a2 2 0 0 0 2 2Z"/>
                    </svg>
                </span>
                <span class="text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Calendario</span>
            </a>

            <!-- Asistencias (suelto) -->
            @if($puedeGestionClub)
                <a href="{{ $r('panel.asistencias.index') }}" @click="sidebarOpen=false"
                class="mt-1 flex items-center gap-3 px-3 py-2 panel-nav-item {{ $aAsistencias ? 'panel-nav-item-active' : '' }}"
                :class="sidebarCollapsed ? 'justify-center' : ''">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 17l4 4 4-4M12 3v18"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Asistencias</span>
                </a>
            @endif

            <!-- Preinscripciones (suelto) -->
            @if($puedeGestionClub)
                <a href="{{ $r('panel.preinscripciones.index') }}" @click="sidebarOpen=false"
                class="mt-1 flex items-center gap-3 px-3 py-2 panel-nav-item {{ $aPreinscripciones ? 'panel-nav-item-active' : '' }}"
                :class="sidebarCollapsed ? 'justify-center' : ''">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10a4 4 0 0 1-4 4H8l-5 5V6a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Preinscripciones</span>
                </a>
            @endif

            <!-- INFORMES -->
            @if($puedeGestionClub)
                <button type="button"
                        class="mt-2 w-full flex items-center gap-3 px-3 py-2 panel-nav-group-btn"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                        @click="if(sidebarCollapsed){ sidebarCollapsed=false } toggle('informes')">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19V5m4 14V9m4 10V3m4 16v-6m4 6V7"/>
                        </svg>
                    </span>
                    <span class="flex-1 text-left text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Informes</span>

                    <span class="w-6 shrink-0 flex items-center justify-center" x-show="!sidebarCollapsed" x-cloak>
                        <svg class="h-4 w-4 opacity-70 transition" :class="openKey === 'informes' ? 'rotate-90' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </span>
                </button>

                <div x-show="!sidebarCollapsed && openKey === 'informes'" x-collapse class="pl-3 pr-1 mt-1 space-y-1">
                    <a href="{{ $r('panel.informes.resumen') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.informes.resumen') ? 'panel-subitem-active' : '' }}">
                        Resumen mensual
                    </a>
                </div>
            @endif
            
            <!-- USUARIOS -->
            @if($puedeGestionarUsuarios)
                <button type="button"
                        class="mt-2 w-full flex items-center gap-3 px-3 py-2 panel-nav-group-btn"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                        @click="if(sidebarCollapsed){ sidebarCollapsed=false } toggle('usuarios')">
                    <span class="w-6 shrink-0 flex items-center justify-center">
                        <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <span class="flex-1 text-left text-sm font-medium" x-show="!sidebarCollapsed" x-cloak>Usuarios</span>

                    <span class="w-6 shrink-0 flex items-center justify-center" x-show="!sidebarCollapsed" x-cloak>
                        <svg class="h-4 w-4 opacity-70 transition" :class="openKey === 'usuarios' ? 'rotate-90' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </span>
                </button>

                <div x-show="!sidebarCollapsed && openKey === 'usuarios'" x-collapse class="pl-3 pr-1 mt-1 space-y-1">
                    <a href="{{ $r('panel.usuarios.index') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.usuarios.index') ? 'panel-subitem-active' : '' }}">
                        Listado
                    </a>
                    <a href="{{ $r('panel.usuarios.create') }}" @click="sidebarOpen=false"
                    class="block px-3 py-2 panel-subitem {{ $is('panel.usuarios.create') ? 'panel-subitem-active' : '' }}">
                        Crear usuario
                    </a>
                </div>
            @endif

            <div class="pt-4 mt-4 border-t panel-border px-3">
                <a href="{{ route('public.home') }}" @click="sidebarOpen=false"
                   class="text-sm panel-muted hover:text-white"
                   x-show="!sidebarCollapsed" x-cloak>
                    Volver a la web pública →
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main -->
    <div class="min-h-screen transition-[padding] duration-200 ease-in-out"
         :class="sidebarCollapsed ? 'md:pl-[84px]' : 'md:pl-[280px]'">

        <!-- Topbar (altura igual que sidebar top) -->
        <header class="sticky top-0 z-30 h-20 border-b panel-border panel-topbar">
            <div class="h-20 px-4 sm:px-5 flex items-center gap-3 sm:gap-4">

                <!-- Mobile open -->
                <button class="md:hidden panel-icon-btn h-12 w-12 flex items-center justify-center"
                        @click="sidebarOpen=true" aria-label="Abrir menú">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Desktop collapse (izquierda del buscador) -->
                <button type="button"
                        class="hidden md:inline-flex panel-icon-btn h-12 w-12 items-center justify-center"
                        @click="sidebarCollapsed = !sidebarCollapsed"
                        :aria-label="sidebarCollapsed ? 'Expandir sidebar' : 'Colapsar sidebar'">
                    <svg x-show="!sidebarCollapsed" class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h10M4 18h16"/>
                    </svg>
                    <svg x-show="sidebarCollapsed" class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h14M4 18h16"/>
                    </svg>
                </button>

                <!-- Search (altura fija) -->
                <div class="flex-1 min-w-0">
                    <div class="panel-input h-12 flex items-center gap-3 px-4">
                        <svg class="h-5 w-5 opacity-60 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="text"
                            class="w-full min-w-0 bg-transparent border-none text-md focus:outline-none focus:ring-0"
                            placeholder="Busca alumnos, secciones, grupos...">
                    </div>
                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-2 sm:gap-3">

                    <!-- Notificaciones (altura fija) -->
                    <button class="panel-icon-btn h-12 w-12 flex items-center justify-center" aria-label="Notificaciones">
                        <svg class="h-5 w-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8a6 6 0 10-12 0c0 7-3 7-3 7h18s-3 0-3-7"/>
                            <path d="M13.7 21a2 2 0 01-3.4 0"/>
                        </svg>
                    </button>

                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open:false }" @keydown.escape.window="open=false">
                        <button type="button"
                            class="h-12 flex items-centerrounded-xl focus:outline-none focus:ring-0 focus:ring-white/10  md:gap-2 "
                            @click="open = !open"
                            :aria-expanded="open.toString()"
                            aria-label="Menú de usuario">
                            <img
                                src="{{ $authUser->avatar_url }}"
                                alt="Avatar"
                                class="h-12 w-12 rounded-xl object-cover border panel-border"
                            >

                            <div class="flex items-center gap-1">
                                <!-- Nombre (solo una línea, sin rol) -->
                                <div class="hidden sm:block text-left leading-4">
                                    <div class="text-sm font-semibold">
                                        {{ auth()->user()->name ?? 'Usuario' }}
                                    </div>
                                </div>
                                <svg class="h-5 w-5 opacity-70 shrink-0 hidden sm:block transition-transform duration-200"
                                    :class="open ? 'rotate-180' : 'rotate-0'"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                        </button>

                        <!-- Dropdown -->
                        <div x-cloak x-show="open"
                            @click.outside="open=false"
                            x-transition
                            class="absolute panel-popover right-0 mt-2 w-72 panel-card overflow-hidden z-50">
                            <div class="px-4 py-3">
                                <div class="text-sm font-semibold">{{ auth()->user()->name ?? 'Usuario' }}</div>
                                <div class="text-xs panel-muted">{{ auth()->user()->email ?? '' }}</div>
                            </div>

                            <div class="border-t panel-border"></div>

                            <div class="py-2">
                                <a href="{{ \Illuminate\Support\Facades\Route::has('profile.edit') ? route('profile.edit') : '#' }}"
                                class="block px-4 py-2 text-sm hover:bg-white/5"
                                @click="open=false">
                                    Editar perfil
                                </a>
                            </div>

                            <div class="border-t panel-border"></div>

                            <form method="POST" action="{{ route('logout') }}" class="p-2">
                                @csrf
                                <button type="submit"
                                        class="w-full panel-icon-btn h-10 px-3 text-sm flex items-center justify-center">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>