@extends('layouts.public')
@section('title','Contacto · Nova Unió')

@section('meta_description','Contacto Nova Unió. Escríbenos para dudas, pruebas, grupos, planes u horarios y te orientamos según tu edad y objetivo.')
@section('og_image', Vite::asset('resources/img/hero/contacto.webp'))

@section('content')
<section class="relative w-full pt-16 pb-4 sm:pt-24">

    <!-- Fondo -->
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image:
    linear-gradient(to bottom,
    rgba(0,0,0,0) 0%,
    rgba(0,0,0,0.25) 55%,
    rgba(0,0,0,0.75) 80%,
    rgba(0,0,0,1) 100%
    ),
    url('{{ Vite::asset('resources/img/hero/contacto.webp') }}');" aria-hidden="true"></div>

        <!-- Oscurecer general -->
        <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Título -->
        <div class="max-w-3xl">
            <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl md:text-7xl text-white">
                CON<span class="text-accent">TACTO</span>
            </h1>
            <p class="mt-4 text-main italic text-sm sm:text-base md:text-lg">
                Escríbenos y te contestamos. Si nos dices tu edad y tu objetivo, te orientamos con el grupo y el plan.
            </p>
        </div>

        <div class="mt-10 grid gap-10 lg:grid-cols-[1fr_360px] items-start">
            <!-- Formulario -->
            <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6 sm:p-8">

                @if(session('ok'))
                <div class="mb-6 border border-accent bg-black/30 p-4 text-white/90 italic">
                    <span class="text-accent font-bold not-italic">✓</span> {{ session('ok') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 border border-red-500/40 bg-black/30 p-4 text-white/90 italic">
                    <div class="font-bold not-italic text-red-300">Revisa el formulario:</div>
                    <ul class="mt-2 list-disc list-inside text-white/80">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <h2 class="uppercase font-black italic text-white text-2xl">
                    Envíanos un <span class="text-accent">mensaje</span>
                </h2>

                <form action="{{ route('public.contacto.enviar') }}" method="POST" class="mt-6 space-y-5">
                    @csrf

                    <div class="hidden">
                        <label>Empresa</label>
                        <input type="text" name="empresa" value="">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">
                                Nombre *
                            </label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]" placeholder="Tu nombre"
                                required>
                        </div>

                        <div>
                            <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">
                                Email *
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                                placeholder="tucorreo@gmail.com" required>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">
                                Teléfono (opcional)
                            </label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                                placeholder="+34 600 000 000">
                        </div>

                        <div>
                            <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">
                                Asunto *
                            </label>
                            <input type="text" name="asunto" value="{{ old('asunto') }}" class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                                placeholder="Quiero información" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">
                            Mensaje *
                        </label>
                        <textarea name="mensaje" rows="6" class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                     focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                            placeholder="Cuéntanos tu edad, tu objetivo (fitness/competición) y qué te interesa (MMA/Sambo/Kids)..."
                            required>{{ old('mensaje') }}</textarea>
                        <p class="mt-2 text-white/45 italic text-sm">
                            Intentamos contestar lo antes posible.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit"
                            class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-6 py-3 text-sm sm:text-base transition duration-200 ease-out
                     hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                            Enviar
                        </button>

                        <a href="{{ route('public.preinscripcion') }}"
                            class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-transparent border-2 border-accent text-accent px-6 py-3 text-sm sm:text-base transition duration-200 ease-out
                     hover:bg-accent hover:text-black hover:-translate-y-0.5 hover:shadow-[0_14px_40px_-20px_rgba(255,255,0,0.45)] active:translate-y-0">
                            Preinscripción
                        </a>
                    </div>
                </form>
            </div>

            <!-- Info lateral -->
            <aside class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6 sm:p-8">
                <h2 class="uppercase font-black italic text-white text-2xl">
                    Mantente en <span class="text-accent">contacto</span>
                </h2>
                <div class="mt-6">
                    <div class="relative overflow-hidden border border-white/10 bg-black/25 aspect-square">
                        <iframe title="Ubicación Nova Unió" class="absolute inset-0 w-full h-full" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps?q=Nova%20Uni%C3%B3%20Blanes&output=embed"
                            aria-label="Mapa de ubicación"></iframe>

                        <div class="pointer-events-none absolute inset-0 bg-black/10"></div>
                        <div
                            class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/70 to-transparent">
                        </div>
                    </div>

                    <div class="mt-3 text-white/60 italic text-sm">
                        ¿Cómo llegar? <span class="text-accent">Consulta el mapa</span> o escríbenos y te lo pasamos.
                    </div>
                </div>
                <div class="mt-6 space-y-3 text-white/80 italic">
                    <div class="flex items-center gap-3">
                        <span class="text-accent">●</span>
                        <a class="hover:text-white transition"
                            href="mailto:contacto@novaunio.cat">contacto@novaunio.cat</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-accent">●</span>
                        <a class="hover:text-white transition" href="https://www.instagram.com/novaunioteam"
                            target="_blank" rel="noreferrer">Instagram</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-accent">●</span>
                        <a class="hover:text-white transition" href="https://www.tiktok.com/@novaunioteam"
                            target="_blank" rel="noreferrer">TikTok</a>
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-14 h-[4px]
      bg-[radial-gradient(circle,rgba(255,255,255,0.2)_2px,transparent_2px)]
      bg-[length:8px_4px]
      bg-repeat-x"></div>
    </div>
</section>
@endsection