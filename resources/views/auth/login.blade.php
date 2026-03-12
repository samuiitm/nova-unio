<x-panel-guest-layout>
    <div class="panel-card p-8 sm:p-10">
        <div class="flex justify-center mb-7">
            <img
                src="{{ Vite::asset('resources/img/logo-novaunio.svg') }}"
                alt="Nova Unió"
                class="h-10 w-auto opacity-90"
            >
        </div>

        <h1 class="text-4xl font-semibold tracking-tight text-center">Inicia sesión</h1>
        <p class="mt-2 text-center panel-muted">
            Ingresa tu correo y contraseña para entrar en el área privada de Nova Unió.
        </p>

        <x-auth-session-status class="mt-7 mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="text-sm font-medium">Email <span class="text-red-400">*</span></label>
                <input id="email" name="email" type="email"
                       class="panel-input mt-2 w-full px-4 py-3"
                       placeholder="example@gmail.com"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="text-sm font-medium">Contraseña <span class="text-red-400">*</span></label>
                <input id="password" name="password" type="password"
                       class="panel-input mt-2 w-full px-4 py-3"
                       placeholder="Introduce tu contraseña"
                       required autocomplete="current-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <button type="submit" class="panel-btn w-full py-3 text-sm">
                Iniciar sesión
            </button>
        </form>
    </div>
</x-panel-guest-layout>