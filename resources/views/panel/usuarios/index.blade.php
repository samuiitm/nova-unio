@extends('layouts.panel')

@section('title', 'Usuarios | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Usuarios</h1>
        <p class="mt-1 panel-muted">Gestión interna de accesos al panel.</p>
    </div>

    <a href="{{ route('panel.usuarios.create') }}" class="panel-btn px-5 py-3">
        Crear usuario
    </a>
</div>

@if(session('ok'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('ok') }}</div>
    </div>
@endif

<div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Total</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['total'] }}</div>
    </div>
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Admins</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['admins'] }}</div>
    </div>
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Entrenadores</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['entrenadores'] }}</div>
    </div>
    <div class="panel-card p-5">
        <div class="text-sm panel-muted">Activos</div>
        <div class="mt-2 text-3xl font-semibold">{{ $stats['activos'] }}</div>
    </div>
</div>

<div class="mt-5 panel-card p-5">
    <form method="GET" class="grid gap-3 lg:grid-cols-[1fr,220px,220px,auto]">
        <input name="q" value="{{ $q }}" class="panel-input px-4 py-3" placeholder="Buscar usuario...">

        <select name="rol" class="panel-input px-4 py-3">
            <option value="todos" @selected($rol === 'todos')>Todos los roles</option>
            <option value="admin" @selected($rol === 'admin')>Admin</option>
            <option value="entrenador" @selected($rol === 'entrenador')>Entrenador</option>
        </select>

        <select name="estado" class="panel-input px-4 py-3">
            <option value="todos" @selected($estado === 'todos')>Todos los estados</option>
            <option value="activos" @selected($estado === 'activos')>Activos</option>
            <option value="inactivos" @selected($estado === 'inactivos')>Inactivos</option>
        </select>

        <button class="panel-btn px-5 py-3">Filtrar</button>
    </form>
</div>

<div class="mt-5 panel-card p-5">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left panel-muted">
                <tr>
                    <th class="py-2">Usuario</th>
                    <th class="py-2">Email</th>
                    <th class="py-2">Teléfono</th>
                    <th class="py-2">Rol</th>
                    <th class="py-2">Estado</th>
                    <th class="py-2">Alta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                    <tr class="border-t panel-border">
                        <td class="py-3">
                            <div class="font-medium">{{ $u->nombre }} {{ $u->apellidos }}</div>
                        </td>
                        <td class="py-3">{{ $u->email }}</td>
                        <td class="py-3">{{ $u->telefono ?: '—' }}</td>
                        <td class="py-3">
                            @if($u->esAdmin())
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(var(--p-accent) / .14); color: rgb(var(--p-accent)); border: 1px solid rgb(var(--p-accent) / .25);">
                                    Admin
                                </span>
                            @else
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(255 255 255 / .06); color: rgb(255 255 255 / .70); border: 1px solid rgb(255 255 255 / .10);">
                                    Entrenador
                                </span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($u->activo)
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(80 200 120 / .12); color: rgb(140 255 190); border: 1px solid rgb(80 200 120 / .22);">
                                    Activo
                                </span>
                            @else
                                <span class="text-xs px-3 py-1 rounded-full"
                                      style="background: rgb(255 80 120 / .12); color: rgb(255 130 170); border: 1px solid rgb(255 80 120 / .22);">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="py-3">{{ optional($u->created_at)->format('d/m/Y') ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 panel-muted">No hay usuarios con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5 flex items-center justify-between gap-3">
    <div class="text-xs panel-muted">
        Mostrando {{ $usuarios->firstItem() ?? 0 }}-{{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuarios
    </div>
    <div>
        {{ $usuarios->links() }}
    </div>
</div>
@endsection