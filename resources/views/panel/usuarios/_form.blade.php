@php
    $rolActual = old('rol', isset($usuario) ? ($usuario->rolEnum()?->value ?? 'entrenador') : 'entrenador');
    $activoActual = old('activo', isset($usuario) ? ($usuario->activo ? 1 : 0) : 1);
@endphp

<div class="grid gap-5">
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Datos</h2>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm panel-muted mb-2">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre ?? '') }}" class="panel-input w-full px-4 py-3">
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos ?? '') }}" class="panel-input w-full px-4 py-3">
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" class="panel-input w-full px-4 py-3">
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono ?? '') }}" class="panel-input w-full px-4 py-3">
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">Rol y acceso</h2>

            <div class="mt-5 grid gap-4">
                <div>
                    <label class="block text-sm panel-muted mb-2">Rol</label>
                    <select name="rol" class="panel-input w-full px-4 py-3">
                        <option value="admin" @selected($rolActual === 'admin')>Admin</option>
                        <option value="entrenador_admin" @selected($rolActual === 'entrenador_admin')>Entrenador admin</option>
                        <option value="entrenador" @selected($rolActual === 'entrenador')>Entrenador</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="activo" name="activo" value="1" @checked((bool) $activoActual)>
                    <label for="activo" class="text-sm">Usuario activo</label>
                </div>

                <div class="rounded-2xl border panel-border p-4 text-sm" style="background: rgb(255 255 255 / .03);">
                    <div><span class="font-medium">Admin:</span> acceso total, incluida la sección Usuarios.</div>
                    <div class="mt-2"><span class="font-medium">Entrenador admin:</span> gestiona club, alumnos, pagos, informes, grupos y preinscripciones, pero no Usuarios.</div>
                    <div class="mt-2"><span class="font-medium">Entrenador:</span> solo calendario y pasar lista.</div>
                </div>
            </div>
        </div>

        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">
                {{ isset($usuario) ? 'Cambiar contraseña' : 'Contraseña' }}
            </h2>

            <div class="mt-5 grid gap-4">
                <div>
                    <label class="block text-sm panel-muted mb-2">
                        {{ isset($usuario) ? 'Nueva contraseña' : 'Contraseña' }}
                    </label>
                    <input type="password" name="password" class="panel-input w-full px-4 py-3">
                </div>

                <div>
                    <label class="block text-sm panel-muted mb-2">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="panel-input w-full px-4 py-3">
                </div>

                <div class="text-xs panel-muted">
                    Mínimo 12 caracteres, con mayúsculas, minúsculas, números y símbolos.
                    @if(isset($usuario))
                        Déjala vacía si no quieres cambiarla.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>