@extends('layouts.public')
@section('title','Horarios · Nova Unió')

@section('meta_description','Horarios de entrenamientos en Nova Unió. Consulta las clases semanales de MMA y Sambo y encuentra el grupo que mejor encaja contigo.')
@section('og_image', Vite::asset('resources/img/hero/horarios.webp'))

@push('head')
<link rel="preload" as="image" href="{{ Vite::asset('resources/img/hero/horarios.webp') }}" type="image/webp">
@endpush

@section('content')
@php
$dias = [
['k' => 'LUN', 'full' => 'Lunes'],
['k' => 'MAR', 'full' => 'Martes'],
['k' => 'MIÉ', 'full' => 'Miércoles'],
['k' => 'JUE', 'full' => 'Jueves'],
['k' => 'VIE', 'full' => 'Viernes'],
];

$filas = [
[
'nombre' => 'Sambo Kids',
'edad' => '3–12 años',
'dias' => ['LUN', 'MIÉ', 'VIE'],
'hora' => '17:30 · 18:30',
],
[
'nombre' => 'MMA Youth',
'edad' => '12–17 años',
'dias' => ['MAR', 'JUE'],
'hora' => '17:30 · 18:30',
],
[
'nombre' => 'Sambo Adultos',
'edad' => '+12 años',
'dias' => ['LUN', 'MIÉ'],
'hora' => '18:30 · 19:30',
],
[
'nombre' => 'MMA Adultos',
'edad' => '+18 años',
'dias' => ['MAR', 'JUE'],
'hora' => '18:30 · 19:30',
],
[
'nombre' => 'Sparring',
'edad' => '+12 años',
'dias' => ['VIE'],
'hora' => '18:30 · 19:30',
],
];
@endphp

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
        url('{{ Vite::asset('resources/img/hero/horarios.webp') }}');" aria-hidden="true"></div>

        <!-- Oscurecer general -->
        <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
    </div>
    </div>

    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Cabecera -->
        <div class="text-center">
            <h1 class="uppercase font-black italic leading-[0.9] text-4xl sm:text-5xl md:text-6xl text-white">
                NUESTROS <span class="text-accent">HORARIOS</span>
            </h1>
            <p class="mt-4 text-main text-base sm:text-lg max-w-2xl mx-auto italic">
                Entrenamientos semanales organizados por grupos. Si es tu primera vez, te ayudamos a elegir el mejor
                para ti.
            </p>
        </div>

        <!-- Lista -->
        <div class="mt-10 border border-white/15 bg-black/20 backdrop-blur-[1px] overflow-hidden">
            @foreach($filas as $i => $f)
            <div class="group relative">
                @if($i !== 0)
                <div class="h-px bg-white/10"></div>
                @endif

                <div class="relative px-4 sm:px-6 py-5 transition duration-300 ease-out hover:bg-white/[0.03]">
                    <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                        style="background: radial-gradient(900px 240px at 20% 50%, rgba(255,255,0,0.10), transparent 60%);">
                    </div>

                    <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:gap-6">
                        <!-- Izquierda: grupo -->
                        <div class="md:w-[42%]">
                            <div class="flex items-baseline gap-3 flex-wrap">
                                <div class="uppercase font-black italic text-white text-2xl sm:text-3xl">
                                    {{ $f['nombre'] }}
                                </div>

                                @if(!empty($f['edad']))
                                <span class="text-white/55 italic text-sm">
                                    {{ $f['edad'] }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Centro: días -->
                        <div class="md:flex-1">
                            <div class="flex flex-wrap gap-2">
                                @foreach($f['dias'] as $dk)
                                <span
                                    class="font-brand uppercase not-italic text-xs sm:text-sm px-3 py-1 border border-accent bg-accent text-black shadow-[0_10px_28px_-18px_rgba(255,255,0,0.45)]">
                                    {{ $dk }}
                                </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Derecha: hora -->
                        <div class="md:w-[22%] md:text-right">
                            <div class="uppercase font-black italic text-white text-xl sm:text-2xl">
                                {{ $f['hora'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Nota + CTA -->
        <div class="mt-10 flex flex-col items-center gap-4 text-center">
            <p class="text-white/55 italic text-sm max-w-2xl">
                Horario sujeto a cambios puntuales por festivos o eventos. Si quieres confirmar tu grupo, escríbenos.
            </p>

            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('public.preinscripcion') }}"
                    class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-5 py-3 text-sm sm:text-base transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                    Preinscripción
                </a>
                <a href="{{ route('public.contacto') }}"
                    class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-transparent border-2 border-accent text-accent px-5 py-3 text-sm sm:text-base transition duration-200 ease-out hover:bg-accent hover:text-black hover:-translate-y-0.5 hover:shadow-[0_14px_40px_-20px_rgba(255,255,0,0.45)] active:translate-y-0">
                    Contacto
                </a>
            </div>
        </div>
    </div>
</section>
@endsection