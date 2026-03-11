<div class="panel-card p-6">
    <div>
        <h2 class="text-lg font-semibold">Datos personales</h2>
        <p class="mt-1 text-sm panel-muted">
            Estos datos identifican tu usuario dentro del panel.
        </p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-5" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="mb-6 rounded-2xl border panel-border p-4" style="background: rgb(255 255 255 / .03);">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <img
                    src="{{ $user->avatar_url }}"
                    alt="Foto de perfil"
                    class="h-24 w-24 rounded-2xl object-cover border panel-border"
                >

                <div class="flex-1">
                    <label class="text-sm font-medium">Foto de perfil</label>

                    <input
                        type="file"
                        name="foto"
                        accept=".jpg,.jpeg,.png,.webp,image/*"
                        class="mt-2 w-full panel-input px-4 py-3"
                    >

                    <p class="mt-2 text-xs panel-muted">
                        La imagen se reduce automáticamente y se guarda optimizada.
                    </p>

                    @error('foto')
                        <p class="mt-2 text-sm">{{ $message }}</p>
                    @enderror

                    @if($user->foto_perfil)
                        <label class="mt-3 inline-flex items-center gap-2 text-sm panel-muted">
                            <input type="checkbox" name="quitar_foto" value="1">
                            Quitar foto actual
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm panel-muted mb-2">Nombre</label>
                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre', $user->nombre) }}"
                    class="panel-input w-full px-4 py-3"
                    required
                    autofocus
                >
                @error('nombre')
                    <p class="mt-2 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Apellidos</label>
                <input
                    type="text"
                    name="apellidos"
                    value="{{ old('apellidos', $user->apellidos) }}"
                    class="panel-input w-full px-4 py-3"
                >
                @error('apellidos')
                    <p class="mt-2 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="panel-input w-full px-4 py-3"
                    required
                >
                @error('email')
                    <p class="mt-2 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm panel-muted mb-2">Teléfono</label>
                <input
                    type="text"
                    name="telefono"
                    value="{{ old('telefono', $user->telefono) }}"
                    class="panel-input w-full px-4 py-3"
                >
                @error('telefono')
                    <p class="mt-2 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button class="panel-btn px-6 py-3">Guardar cambios</button>
        </div>
    </form>
</div>