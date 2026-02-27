@extends('layouts.public')
@section('title','Política de cookies · Nova Unió')

@section('content')
<section class="relative w-full py-16 sm:pt-24">

  <!-- Fondo -->
  <div class="fixed inset-0 -z-10 pointer-events-none">
    <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image:
    linear-gradient(to bottom,
    rgba(0,0,0,0) 0%,
    rgba(0,0,0,0.25) 55%,
    rgba(0,0,0,0.75) 80%,
    rgba(0,0,0,1) 100%
    ),
    url('{{ Vite::asset('resources/img/hero/hero.webp') }}');" aria-hidden="true"></div>

    <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>

    <div class="absolute inset-0 mix-blend-overlay"
      style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27140%27 height=%27140%27%3E%3Cfilter id=%27n%27%3E%3CfeTurbulence type=%27fractalNoise%27 baseFrequency=%27.8%27 numOctaves=%273%27 stitchTiles=%27stitch%27/%3E%3C/filter%3E%3Crect width=%27140%27 height=%27140%27 filter=%27url(%23n)%27 opacity=%27.6%27/%3E%3C/svg%3E'); background-repeat:repeat;"
      aria-hidden="true"></div>
  </div>

  <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl text-white">
      POLÍTICA <span class="text-accent">DE COOKIES</span>
    </h1>

    <p class="mt-4 text-main italic">
      Esta web no usa cookies de analítica ni de publicidad.
    </p>

    <div class="mt-10 space-y-6">

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">1. Cookies técnicas</h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          El sitio puede usar cookies técnicas necesarias para que la web funcione correctamente
          (por ejemplo, seguridad, navegación o funcionamiento de formularios).
          Estas cookies no se usan para hacer perfiles ni para publicidad.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">2. Cómo desactivar cookies</h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          Puedes bloquear o eliminar cookies desde la configuración de tu navegador. Si las desactivas,
          algunas partes de la web podrían no funcionar del todo bien.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">3. Contacto</h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          Si tienes dudas sobre esta política, escríbenos a: <span class="text-accent">contacto@novaunio.cat</span>.
        </p>
      </div>

    </div>
  </div>
</section>
@endsection