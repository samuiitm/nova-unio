@extends('layouts.public')
@section('title','Nova Unió')
@section('content')
<section class="relative home-hero w-full pt-24 hero-section">
    <div class="relative z-10 mx-auto max-w-6xl w-full px-4 sm:px-6 lg:px-8 flex items-center min-h-[calc(100svh-6rem)] lg:min-h-[calc(100vh-10rem)] hero-container">
        <div class="hero-box flex flex-col gap-8">
            <div class="flex flex-col gap-2">
                <h1 class="uppercase font-black leading-[0.9] text-5xl sm:text-6xl md:text-7xl">
                Bienvenidos a<br>
                <span class="text-yellow-500">Nova Unió</span>
                </h1>

                <p class="mt-3 max-w-lg text-sm sm:text-base md:text-lg text-neutral-300">
                Dos décadas de historia donde la pasión por la lucha,
                el aprendizaje y el compañerismo siguen marcando cada entrenamiento.
                ¡Nos vemos en los tatamis!
                </p>
            </div>

            <img src="{{ Vite::asset('resources/img/hero/flechas.svg') }}"
                alt=""
                class="w-4 sm:w-5 opacity-80 mt-2">

            <div class="flex flex-col gap-4">
                <a href="{{ route('public.preinscripcion') }}"
                class="w-fit inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-yellow-500 text-black px-5 py-3 text-sm sm:text-base md:text-lg">
                Preinscríbete
                </a>
            </div>
        </div>
    </div>
</section>


@endsection