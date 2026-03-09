@extends('layouts.panel')

@section('title', 'Crear usuario | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Crear usuario</h1>
        <p class="mt-1 panel-muted">Alta de administradores y entrenadores con acceso al panel.</p>
    </div>

    <a href="{{ route('panel.usuarios.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
</div>

@if($errors->any())
    <div class="mt-5 panel-card p-4">
        <div class="text-sm font-medium">Revisa estos campos:</div>
        <ul class="mt-2 text-sm panel-muted list-disc pl-5">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('panel.usuarios.store') }}" class="mt-5">
    @csrf

    <div class="grid gap-5">
        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">Datos</h2>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm panel-muted mb-2">Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="panel-input w-full px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm panel-muted mb-2">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="panel-input w-full px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm panel-muted mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="panel-input w-full px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm panel-muted mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" class="panel-input w-full px-4 py-3">
                </div>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div class="panel-card p-6">
                <h2 class="text-lg font-semibold">Acceso</h2>

                <div class="mt-5 grid gap-4">
                    <div>
                        <label class="block text-sm panel-muted mb-2">Rol</label>
                        <select name="rol" class="panel-input w-full px-4 py-3">
                            <option value="entrenador" @selected(old('rol') === 'entrenador')>Entrenador</option>
                            <option value="admin" @selected(old('rol') === 'admin')>Admin</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', '1'))>
                        <label for="activo" class="text-sm">Usuario activo</label>
                    </div>
                </div>
            </div>

            <div class="panel-card p-6">
                <h2 class="text-lg font-semibold">Contraseña</h2>

                <div class="mt-5 grid gap-4">
                    <div>
                        <label class="block text-sm panel-muted mb-2">Contraseña</label>
                        <input type="password" name="password" class="panel-input w-full px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm panel-muted mb-2">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="panel-input w-full px-4 py-3">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button class="panel-btn px-6 py-3">Guardar</button>
        <a href="{{ route('panel.usuarios.index') }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
    </div>
</form>
@endsection