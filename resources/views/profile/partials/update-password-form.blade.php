<div class="panel-card p-6">
    <div>
        <h2 class="text-lg font-semibold">Contraseña</h2>
        <p class="mt-1 text-sm panel-muted">
            Cámbiala desde aquí si quieres actualizar tu acceso.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="mt-5">
        @csrf
        @method('PUT')

        <div class="grid gap-4">
            <div>
                <label class="block text-sm panel-muted mb-2">Contraseña actual</label>
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="panel-input w-full px-4 py-3"
                    autocomplete="current-password"
                >
                @if($errors->updatePassword->has('current_password'))
                    <p class="mt-2 text-sm">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Nueva contraseña</label>
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="panel-input w-full px-4 py-3"
                    autocomplete="new-password"
                >
                @if($errors->updatePassword->has('password'))
                    <p class="mt-2 text-sm">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Confirmar nueva contraseña</label>
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="panel-input w-full px-4 py-3"
                    autocomplete="new-password"
                >
                @if($errors->updatePassword->has('password_confirmation'))
                    <p class="mt-2 text-sm">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>

            <div class="text-xs panel-muted">
                Usa una contraseña larga y segura. Si un usuario la olvida, el admin puede resetearla desde la sección de usuarios.
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button class="panel-btn px-6 py-3">Actualizar contraseña</button>
        </div>
    </form>
</div>