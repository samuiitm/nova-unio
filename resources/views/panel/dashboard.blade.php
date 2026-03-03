@extends('layouts.panel')

@section('title', 'Dashboard | Nova Unió')

@section('content')
    <div class="grid gap-5 lg:grid-cols-3">
        <div class="panel-card p-5">
            <div class="flex items-start justify-between">
                <div class="h-11 w-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                    <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-6 panel-muted text-sm">Alumnos</div>
            <div class="mt-1 flex items-end justify-between">
                <div class="text-4xl font-semibold">67</div>
                <div class="px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-400/20 text-emerald-300 text-sm">
                    ↑ 18.09%
                </div>
            </div>
        </div>

        <div class="panel-card p-5">
            <div class="h-11 w-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    <path d="M12 7v10"/>
                    <path d="M9 10h6"/>
                </svg>
            </div>
            <div class="mt-6 panel-muted text-sm">Cuotas pendientes</div>
            <div class="mt-1 text-4xl font-semibold">3</div>
        </div>

        <div class="panel-card p-5">
            <div class="h-11 w-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 0 0 2-2V7H4v12a2 2 0 0 0 2 2Z"/>
                </svg>
            </div>
            <div class="mt-6 panel-muted text-sm">Clases programadas hoy</div>
            <div class="mt-1 text-4xl font-semibold">3</div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="panel-card p-5 lg:col-span-2">
            <div class="h-11 w-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19V5m4 14V9m4 10V3m4 16v-6m4 6V7"/>
                </svg>
            </div>
            <div class="mt-6 panel-muted text-sm">Asistencias esta semana</div>
            <div class="mt-1 text-4xl font-semibold">98</div>
        </div>

        <div class="panel-card p-5">
            <div class="h-11 w-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M19 8v6"/>
                    <path d="M22 11h-6"/>
                </svg>
            </div>
            <div class="mt-6 panel-muted text-sm">Nuevos alumnos este mes</div>
            <div class="mt-1 text-4xl font-semibold">+6</div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="panel-card p-5 lg:col-span-2">
            <div class="text-sm font-semibold">Alumnos con cuota pendiente</div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-white/45">
                        <tr class="border-b border-white/10">
                            <th class="text-left font-medium py-3">Nombre</th>
                            <th class="text-left font-medium py-3">Grupo</th>
                            <th class="text-left font-medium py-3">Teléfono</th>
                            <th class="text-right font-medium py-3"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10">
                        @foreach ([
                            ['Samuel Cañadas','MMA Adultos','621 30 97 80'],
                            ['Samuel Cañadas','Sambo Kids','678 58 48 92'],
                            ['Samuel Cañadas','MMA Adultos','685 22 11 25'],
                        ] as $row)
                            <tr>
                                <td class="py-4 font-semibold">{{ $row[0] }}</td>
                                <td class="py-4 text-white/70">{{ $row[1] }}</td>
                                <td class="py-4 text-white/70">{{ $row[2] }}</td>
                                <td class="py-4 text-right">
                                    <button class="px-4 py-2 rounded-xl bg-emerald-500/15 border border-emerald-400/20 text-emerald-200 hover:bg-emerald-500/20">
                                        Marcar pagado
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-right">
                <a href="#" class="text-sm text-indigo-300 hover:text-indigo-200">Ver todos</a>
            </div>
        </div>

        <div class="panel-card p-5">
            <div class="text-sm font-semibold">Alumnos con cuota pendiente</div>

            <div class="mt-4 space-y-3">
                @foreach ([
                    ['Hoy','17:30','Sambo Kids'],
                    ['Hoy','18:30','Sambo Adultos'],
                    ['Mañana','17:30','MMA Youth'],
                    ['Mañana','18:30','MMA Adultos'],
                    ['23/11/2025','18:30','Sparring'],
                ] as $c)
                    <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-3 last:border-0 last:pb-0">
                        <div class="text-white/80">{{ $c[0] }}</div>
                        <div class="text-white/50">|</div>
                        <div class="text-white/80">{{ $c[1] }}</div>
                        <div class="flex-1 text-right text-white/60">{{ $c[2] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-right">
                <a href="#" class="text-sm text-indigo-300 hover:text-indigo-200">Ver calendario completo</a>
            </div>
        </div>
    </div>
@endsection