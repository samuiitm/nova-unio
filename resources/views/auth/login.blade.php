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

        <div class="mt-7 grid gap-3 sm:grid-cols-2">
            <button type="button" disabled
                class="panel-input px-4 py-3 flex items-center justify-center gap-3 opacity-60 cursor-not-allowed">
                <span class="inline-flex h-5 w-5 items-center justify-center">
                    <svg viewBox="0 0 48 48" class="h-5 w-5">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.9 32.7 29.4 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.9 6.1 29.7 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34.9 6.1 29.7 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/>
                        <path fill="#4CAF50" d="M24 44c5.3 0 10.2-2 13.9-5.2l-6.4-5.3C29.5 35 26.9 36 24 36c-5.4 0-9.9-3.5-11.5-8.3l-6.6 5.1C9.1 39.5 16 44 24 44z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.4 5.4-6.3 6.8l6.4 5.3C39.1 36.7 44 31 44 24c0-1.2-.1-2.3-.4-3.5z"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Regístrate con Google</span>
            </button>

            <button type="button" disabled
                class="panel-input px-4 py-3 flex items-center justify-center gap-3 opacity-60 cursor-not-allowed">
                <span class="inline-flex h-5 w-5 items-center justify-center">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="5"/>
                        <path d="M16 11.37a4 4 0 1 1-7.87 1.26A4 4 0 0 1 16 11.37z"/>
                        <path d="M17.5 6.5h.01"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Regístrate con Instagram</span>
            </button>
        </div>

        <div class="my-7 flex items-center gap-4">
            <div class="h-px flex-1 bg-white/10"></div>
            <div class="text-xs panel-muted">O bien</div>
            <div class="h-px flex-1 bg-white/10"></div>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

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

            <div class="flex items-center justify-between gap-4">
                <label class="inline-flex items-center gap-2 text-sm panel-muted">
                    <input id="remember_me" name="remember" type="checkbox"
                           class="h-4 w-4 rounded border-white/20 bg-white/5 text-indigo-500 focus:ring-indigo-500/30">
                    Mantener sesión iniciada
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-indigo-300 hover:text-indigo-200">
                        Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="panel-btn w-full py-3 text-sm">
                Iniciar sesión
            </button>
        </form>
    </div>
</x-panel-guest-layout>