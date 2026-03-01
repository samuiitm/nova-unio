@extends('layouts.public')

@section('title', 'Error · Nova Unió')

@section('content')
<section class="relative w-full pt-20 sm:pt-24 lg:pt-28 pb-16">
  <div class="mx-auto max-w-6xl w-full px-4 sm:px-6 lg:px-8">
    <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-black/40 backdrop-blur-sm">
      <div class="absolute inset-0 pointer-events-none"
           style="background:
             linear-gradient(135deg, rgba(255,255,0,.10) 0%, rgba(255,255,0,0) 35%),
             linear-gradient(0deg, rgba(0,0,0,.65) 0%, rgba(0,0,0,0) 55%);">
      </div>

      <div class="relative p-6 sm:p-10 lg:p-14">
        <p class="font-brand uppercase tracking-wide text-accent">
          Algo se ha roto
        </p>

        <h1 class="mt-3 uppercase font-black italic leading-[0.92] text-white text-3xl sm:text-4xl md:text-5xl">
          Error interno del servidor
        </h1>

        <p class="mt-4 max-w-xl text-main text-sm sm:text-base md:text-lg">
          Ahora mismo no podemos procesar la solicitud. Prueba a recargar en unos segundos.
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
          <a href="{{ route('public.home') }}"
             class="inline-flex font-brand font-semibold uppercase tracking-wide bg-accent text-black px-5 py-3 text-sm sm:text-base transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 active:translate-y-0">
            Ir al inicio
          </a>

          <a href="{{ route('public.contacto') }}"
             class="inline-flex font-brand font-semibold uppercase tracking-wide border border-white/20 text-white px-5 py-3 text-sm sm:text-base transition duration-200 ease-out hover:border-white/40 hover:bg-white/5 active:translate-y-0">
            Avisarnos
          </a>
        </div>

        <p class="mt-6 text-xs text-white/45">
          Código: 500
        </p>
      </div>
    </div>
  </div>
</section>
@endsection