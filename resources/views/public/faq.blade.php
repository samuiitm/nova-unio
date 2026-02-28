@extends('layouts.public')
@section('title','Preguntas frecuentes · Nova Unió')

@section('content')
@php
$faq = [
'preguntas' => [
[
'q' => 'Hace falta tener experiencia previa para apuntarse?',
'a' => 'No. Puedes empezar desde cero. Adaptamos el entrenamiento al nivel de cada alumno y te ayudamos a aprender
progresivamente desde las bases.'
],
[
'q' => 'Qué disciplinas se practican en el club?',
'a' => 'Trabajamos Sambo, Combat Sambo y MMA. También tenemos grupo Sambo Kids, y sesiones específicas como sparring
según el día.'
],
[
'q' => 'Cómo funcionan las cuotas?',
'a' => 'La cuota que eligas te cubrirá el acceso a todas las clases de tu grupo. Si quieres entrenar en varios grupos o
disciplinas, te asesoramos para elegir la mejor opción.'
],
[
'q' => 'Las clases son siempre en los mismos horarios?',
'a' => 'Los horarios suelen ser fijos, pero pueden variar puntualmente por festivos, eventos o competiciones. Si tienes
dudas, consulta la página de horarios o contáctanos.'
],
[
'q' => 'Cómo puedo apuntarme o pedir información?',
'a' => 'Puedes hacer la preinscripción desde la web o escribirnos por redes o email. Te responderemos para orientarte y
confirmar grupo y horario.'
],
],
'inscripcion' => [
[
'q' => 'Qué necesito para la primera clase?',
'a' => 'Ropa cómoda y ganas de entrenar. Si aún no tienes material, te asesoramos. En algunas sesiones podrás empezar
sin equipación específica.'
],
[
'q' => 'Puedo probar una clase antes de apuntarme?',
'a' => 'Consúltanos. Dependiendo del grupo y del aforo, podemos organizar una primera toma de contacto para que veas el
ambiente.'
],
[
'q' => 'Hay límite de plazas?',
'a' => 'Algunos grupos pueden llenarse. Por eso recomendamos hacer preinscripción para reservar plaza y coordinar el
inicio.'
],
],
'disciplinas' => [
[
'q' => 'Cuál es la diferencia entre Sambo y Combat Sambo?',
'a' => 'El Sambo se centra en proyecciones y control con luxaciones, mientras que Combat Sambo incluye también golpeo.
La progresión depende del grupo y del nivel.'
],
[
'q' => 'Qué grupo me conviene si soy principiante?',
'a' => 'Te orientamos según edad y objetivo. En general, puedes empezar en MMA Adultos o Sambo Adultos y adaptaremos
intensidad y técnica.'
],
[
'q' => 'Hay entrenos para niños?',
'a' => 'Sí. Sambo Kids está diseñado para enseñar bases de lucha y coordinación de forma segura y progresiva.'
],
],
'pagos' => [
[
'q' => 'Qué opciones de pago existen?',
'a' => 'Mensual, trimestral, semestral y temporada (septiembre a junio). Además, hay clases privadas por sesión o bono.'
],
[
'q' => 'Si falto a clases, se descuenta?',
'a' => 'No solemos descontar por ausencias, pero si tienes una situación puntual, coméntalo con los entrenadores y lo
valoramos.'
],
[
'q' => 'La temporada sale más barata?',
'a' => 'Sí. Si vas a entrenar durante la mayor parte del curso, la temporada es la opción más rentable.'
],
],
];

$temas = [
['k' => 'preguntas', 'label' => 'Preguntas frecuentes'],
['k' => 'inscripcion','label' => 'Inscripción'],
['k' => 'disciplinas','label' => 'Disciplinas y niveles'],
['k' => 'pagos', 'label' => 'Cuotas y pagos'],
];
@endphp

<section class="relative w-full py-16 sm:pt-24" x-data="{
    tema: 'preguntas',
    openIndex: -1,

    setTema(k) {
      this.tema = k
      this.openIndex = -1
    },

    toggle(i) {
      this.openIndex = (this.openIndex === i) ? -1 : i
    }
  }">

    <!-- Fondo -->
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image:
    linear-gradient(to bottom,
    rgba(0,0,0,0) 0%,
    rgba(0,0,0,0.25) 55%,
    rgba(0,0,0,0.75) 80%,
    rgba(0,0,0,1) 100%
    ),
    url('{{ Vite::asset('resources/img/hero/faq.webp') }}');" aria-hidden="true"></div>

        <!-- Oscurecer general -->
        <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Título -->
        <div class="max-w-4xl">
            <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl md:text-7xl text-white">
                PREGUNTAS <span class="text-accent">FRECUENTES</span>
            </h1>
            <p class="mt-4 text-main text-base sm:text-lg italic max-w-3xl">
                Resolvemos las dudas más comunes sobre entrenamientos, niveles y pagos.
            </p>
        </div>

        <!-- Layout -->
        <div class="mt-10 grid gap-10 lg:grid-cols-[320px_1fr] items-start">
            <!-- Izquierda -->
            <aside class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
                <h2 class="uppercase font-black italic text-white text-xl">
                    ELIGE <span class="text-accent">TU TEMA</span>
                </h2>

                <nav class="mt-6">
                    <ul class="space-y-3">
                        @foreach($temas as $t)
                        <li>
                            <button type="button" class="w-full text-left flex items-center justify-between gap-4 py-2"
                                @click="setTema('{{ $t['k'] }}')">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center justify-center w-5 h-5 rounded-full border transition"
                                        :class="tema === '{{ $t['k'] }}' ? 'bg-accent text-black border-accent' : 'border-white/20 text-white/40'">
                                        <span x-show="tema === '{{ $t['k'] }}'">✓</span>
                                    </span>

                                    <span class="font-brand uppercase not-italic tracking-wide transition"
                                        :class="tema === '{{ $t['k'] }}' ? 'text-accent' : 'text-white/70 hover:text-white'">
                                        {{ $t['label'] }}
                                    </span>
                                </div>

                                <span class="text-accent">→</span>
                            </button>

                            <div class="mt-2 border-t border-dotted border-white/15"></div>
                        </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>

            <!-- Derecha -->
            <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6 sm:p-7">
                <h2 class="uppercase font-black italic text-white text-xl">
                    PREGUNTAS <span class="text-accent">FRECUENTES</span>
                </h2>

                <div class="mt-6">
                    @foreach($temas as $t)
                    @php $list = $faq[$t['k']] ?? []; @endphp

                    <!-- x-if: así el tema anterior se borra y no se queda abierto -->
                    <template x-if="tema === '{{ $t['k'] }}'">
                        <div>
                            <ul class="divide-y divide-dotted divide-white/15">
                                @foreach($list as $i => $item)
                                <li class="py-4">
                                    <button type="button"
                                        class="w-full flex items-center justify-between gap-5 text-left"
                                        @click="toggle({{ $i }})">
                                        <span class="text-white/85 italic font-semibold">
                                            {{ $item['q'] }}
                                        </span>

                                        <span class="text-white/60 transition"
                                            :class="openIndex === {{ $i }} ? 'rotate-180 text-accent' : ''">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                aria-hidden="true">
                                                <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </button>

                                    <div x-show="openIndex === {{ $i }}" x-collapse class="pt-3">
                                        <p class="text-main italic leading-relaxed">
                                            {{ $item['a'] }}
                                        </p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </template>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bloque final: no encuentras tu pregunta + contacto -->
        <div class="mt-12 border border-white/10 bg-black/25 backdrop-blur-[1px] p-6 sm:p-8">
            <h3 class="uppercase font-black italic text-3xl sm:text-4xl text-white">
                ¿NO ENCUENTRAS <span class="text-accent">TU PREGUNTA?</span>
            </h3>

            <p class="mt-3 text-main italic max-w-3xl">
                Escríbenos y te contestamos. Si nos dices tu edad y tu objetivo, te orientamos con el grupo y el plan.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('public.contacto') }}"
                    class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-5 py-3 text-sm sm:text-base transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                    Mantente en contacto
                </a>

                <a href="{{ route('public.preinscripcion') }}"
                    class="inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-transparent border-2 border-accent text-accent px-5 py-3 text-sm sm:text-base transition duration-200 ease-out hover:bg-accent hover:text-black hover:-translate-y-0.5 hover:shadow-[0_14px_40px_-20px_rgba(255,255,0,0.45)] active:translate-y-0">
                    Preinscripción
                </a>
            </div>

            <div class="mt-5 text-white/55 italic text-sm">
                También puedes escribirnos por Instagram, TikTok o email (los links los tienes en la sección de
                contacto).
            </div>
        </div>

    </div>
</section>
@endsection