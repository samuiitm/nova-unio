@extends('layouts.public')
@section('title','Preinscripción · Nova Unió')

@section('meta_description','Haz la preinscripción para entrenar en Nova Unió. Envíanos tus datos y te proponemos el grupo y plan más adecuados para ti.')
@section('og_image', Vite::asset('resources/img/hero/preinscripcion.webp'))

@section('content')
<section class="relative w-full py-16 sm:pt-24">

  <!-- Fondo -->
  <div class="fixed inset-0 -z-10 pointer-events-none">
    <div class="absolute inset-0 bg-cover bg-center opacity-60" style="background-image:
    linear-gradient(to bottom,
    rgba(0,0,0,0) 0%,
    rgba(0,0,0,0.25) 55%,
    rgba(0,0,0,0.75) 80%,
    rgba(0,0,0,1) 100%
    ),
    url('{{ Vite::asset('resources/img/hero/preinscripcion.webp') }}');" aria-hidden="true"></div>

    <!-- Oscurecer general -->
    <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
  </div>

  <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
    <!-- Título -->
    <div class="max-w-3xl">
      <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl md:text-7xl text-white">
        PREINS<span class="text-accent">CRIPCIÓN</span>
      </h1>
      <p class="mt-4 text-main italic text-sm sm:text-base md:text-lg">
        Rellena esto y te contactamos para confirmar grupo, horario y cómo empezar.
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
          Datos de <span class="text-accent">preinscripción</span>
        </h2>

        <form action="{{ route('public.preinscripcion.enviar') }}" method="POST" class="mt-6 space-y-5">
          @csrf

          <div class="hidden">
            <label>Empresa</label>
            <input type="text" name="empresa" value="">
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Nombre *</label>
              <input
                type="text"
                name="nombre"
                value="{{ old('nombre') }}"
                required
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                placeholder="Tu nombre"
              >
            </div>

            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Apellidos</label>
              <input
                type="text"
                name="apellidos"
                value="{{ old('apellidos') }}"
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                placeholder="Tus apellidos"
              >
            </div>
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Email *</label>
              <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                placeholder="tucorreo@gmail.com"
              >
            </div>

            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Teléfono</label>
              <input
                type="text"
                name="telefono"
                value="{{ old('telefono') }}"
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                placeholder="+34 600 000 000"
              >
            </div>
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Edad</label>
              <input
                type="number"
                name="edad"
                value="{{ old('edad') }}"
                min="3"
                max="80"
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
                placeholder="Ej: 18"
              >
            </div>

            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Modalidad *</label>
              <select
                name="modalidad"
                required
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
              >
                <option value="" class="bg-black">Selecciona…</option>
                @foreach(['Sambo Kids', 'MMA', 'Sambo Adultos', 'Combat Sambo'] as $m)
                  <option value="{{ $m }}" @selected(old('modalidad')===$m) class="bg-black">{{ $m }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Nivel</label>
              <select
                name="nivel"
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
              >
                <option value="" class="bg-black">No lo sé</option>
                @foreach(['Principiante','Intermedio','Avanzado'] as $n)
                  <option value="{{ $n }}" @selected(old('nivel')===$n) class="bg-black">{{ $n }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Objetivo</label>
              <select
                name="objetivo"
                class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white outline-none transition
                       focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
              >
                <option value="" class="bg-black">Selecciona…</option>
                @foreach(['Aprender','Ponerme en forma','Competir'] as $o)
                  <option value="{{ $o }}" @selected(old('objetivo')===$o) class="bg-black">{{ $o }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div>
            <label class="block text-white/70 uppercase text-xs font-brand not-italic tracking-wide">Mensaje (opcional)</label>
            <textarea
              name="mensaje"
              rows="5"
              class="mt-2 w-full border border-white/15 bg-black/25 px-4 py-3 text-white placeholder-white/30 outline-none transition
                     focus:border-accent focus:shadow-[0_0_0_3px_rgba(255,255,0,0.15)]"
              placeholder="Dinos si tienes alguna lesión, horarios que te van bien, etc."
            >{{ old('mensaje') }}</textarea>
          </div>

          <label class="flex items-start gap-3 text-white/70 italic text-sm">
            <input type="checkbox" name="privacidad" value="1" class="mt-1">
            <span>
              He leído y acepto la
              <a href="{{ route('public.politica-privacidad') }}" class="text-accent underline underline-offset-4">política de privacidad</a>.
            </span>
          </label>

          <div class="flex flex-wrap gap-3 pt-2">
            <button
              type="submit"
              class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-6 py-3 text-sm sm:text-base transition duration-200 ease-out
                     hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0"
            >
              Enviar
            </button>

            <a
              href="{{ route('public.contacto') }}"
              class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-transparent border-2 border-accent text-accent px-6 py-3 text-sm sm:text-base transition duration-200 ease-out
                     hover:bg-accent hover:text-black hover:-translate-y-0.5 hover:shadow-[0_14px_40px_-20px_rgba(255,255,0,0.45)] active:translate-y-0"
            >
              Contacto
            </a>
          </div>
        </form>
      </div>

      <!-- Lado derecho -->
      <aside class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6 sm:p-8">
        <h2 class="uppercase font-black italic text-white text-2xl">
          ¿Qué pasa <span class="text-accent">después</span>?
        </h2>

        <ul class="mt-5 space-y-3 text-white/80 italic">
          <li class="flex gap-3"><span class="text-accent">●</span> Revisamos tu solicitud.</li>
          <li class="flex gap-3"><span class="text-accent">●</span> Te contactamos para confirmar grupo/horario.</li>
          <li class="flex gap-3"><span class="text-accent">●</span> Te decimos qué traer a la primera clase.</li>
        </ul>

        <div class="mt-7 border-t border-dotted border-white/15 pt-6 text-white/55 italic text-sm">
          Si tienes prisa, también puedes escribirnos desde la página de contacto.
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