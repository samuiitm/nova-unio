@extends('layouts.public')

@section('title','Nova Unió · Club de MMA y Sambo')
@section('meta_description','Club de artes marciales Nova Unió: MMA y Sambo. Entrenamientos para niños, youth y adultos. Consulta planes, horarios, profesores y haz la preinscripción online.')
@section('og_image', Vite::asset('resources/img/banner.webp'))

@section('content')

<!-- HERO -->
<section id="hero" class="relative reveal home-hero w-full pt-16 hero-section">
    <div
        class="relative z-10 mx-auto max-w-6xl w-full px-4 sm:px-6 lg:px-8 flex items-center min-h-[calc(100svh-6rem)] lg:min-h-[calc(100vh-10rem)] hero-container">
        <div class="hero-box flex flex-col gap-8">
            <div class="flex flex-col gap-2">
                <h1 class="uppercase font-black leading-[0.9] text-5xl sm:text-6xl md:text-7xl text-white">
                    Bienvenidos a<br>
                    <span class="text-accent">Nova Unió</span>
                </h1>

                <p class="mt-3 max-w-lg text-sm sm:text-base md:text-lg text-main">
                    Dos décadas de historia donde la pasión por la lucha,
                    el aprendizaje y el compañerismo siguen marcando cada entrenamiento.
                    ¡Nos vemos en los tatamis!
                </p>
            </div>

            <img src="{{ Vite::asset('resources/img/hero/flechas.svg') }}" alt="" class="w-4 sm:w-5 opacity-80 mt-2">

            <a href="{{ route('public.preinscripcion') }}"
                class="w-fit inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-5 py-3 text-sm sm:text-base md:text-lg transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                Preinscríbete
            </a>
        </div>
    </div>
</section>

<!-- EL CLUB -->
<section id="el-club" class="reveal relative w-full py-14 sm:pt-12 overflow-hidden md:mt-0">
    <!-- Fondo SOLO en móvil -->
    <div class="absolute inset-0 lg:hidden pointer-events-none">
        <img src="{{ Vite::asset('resources/img/home/home-elclub.webp') }}" alt=""
            class="h-full w-full object-cover opacity-35 mix-blend-multiply">
        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-transparent"></div>
    </div>

    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-14 items-center">

            <!-- Imagen izquierda SOLO en desktop -->
            <div class="hidden lg:block relative">
                <div class="aspect-3 overflow-hidden">
                    <img src="{{ Vite::asset('resources/img/home/home-elclub.webp') }}" alt="El club"
                        class="h-full w-full object-cover opacity-80">
                </div>
                <div
                    class="pointer-events-none absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent">
                </div>
            </div>

            <!-- Texto (móvil y desktop) alineado a la derecha -->
            <div class="relative z-10 flex flex-col items-end text-right">
                <h2 class="uppercase font-black text-5xl sm:text-6xl leading-[0.9] text-white">
                    <span class="text-white">EL</span> <span class="text-accent">CLUB</span>
                </h2>

                <p class="mt-6 max-w-xl text-base sm:text-lg text-main leading-relaxed">
                    Nuestro club nació en <strong class="text-white font-bold">2003</strong> como un espacio abierto a
                    varias
                    <strong class="text-white font-bold">artes marciales</strong>, con entrenos cruzados y competición
                    en
                    <strong class="text-white font-bold">judo, sambo, jiu jitsu, grappling y MMA</strong>.
                    Tras la <strong class="text-white font-bold">pandemia</strong> nos reorganizamos y ahora formamos
                    parte de la
                    <strong class="text-white font-bold">Federación Catalana de Lucha</strong>.
                    Los más pequeños siguen un <strong class="text-white font-bold">sistema seguro</strong> que combina
                    técnicas de
                    todos nuestros estilos; los adultos entrenan lucha <strong
                        class="text-white font-bold">sambo</strong>,
                    <strong class="text-white font-bold">combat sambo</strong> y <strong
                        class="text-white font-bold">MMA</strong>.
                </p>

                <a href="{{ route('public.elclub') }}"
                    class="mt-8 font-brand uppercase not-italic font-bold tracking-wide bg-accent text-black px-10 py-3 text-lg transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                    Leer más
                </a>
            </div>

        </div>
    </div>
</section>

<!-- PLANES -->
<section id="planes" class="reveal relative plans-bg w-full py-10 sm:pt-10">
    <!-- Fondo (solo para esta sección) -->
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image:
        linear-gradient(to bottom,
        rgba(0,0,0,0.5) 0%,
        rgba(0,0,0,0.25) 55%,
        rgba(0,0,0,0.6) 80%,
        rgba(0,0,0,8) 100%
        ),
        url('{{ Vite::asset('resources/img/hero/planes.webp') }}');" aria-hidden="true"></div>

        <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
    </div>
    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" x-data="{ plan: 'mensual' }">

        <!-- Título -->
        <div class="text-center">
            <h2 class="uppercase font-black leading-[0.9] text-4xl sm:text-5xl md:text-6xl text-white">
                <span class="text-white">Nuestros</span>
                <span class="text-accent">Planes</span>
            </h2>

            <p class="mt-4 text-main text-base sm:text-lg max-w-2xl mx-auto">
                Ofrecemos distintos planes de entrenamiento según edad, modalidad y nivel de compromiso.
            </p>
        </div>

        <!-- Tabs mensual/temporada -->
        <div class="mt-8 flex justify-center">
            <div class="inline-flex border border-accent">
                <button
                    class="font-brand uppercase not-italic px-5 py-2 text-sm sm:text-base transition duration-200 ease-out"
                    :class="plan === 'mensual'
            ? 'bg-accent text-black shadow-[0_10px_30px_-18px_rgba(255,255,0,0.35)]'
            : 'text-accent hover:bg-accent/10 hover:text-accent'" @click="plan = 'mensual'" type="button">
                    Mensual
                </button>
                <button
                    class="font-brand uppercase not-italic px-5 py-2 text-sm sm:text-base transition duration-200 ease-out"
                    :class="plan === 'temporada'
            ? 'bg-accent text-black shadow-[0_10px_30px_-18px_rgba(255,255,0,0.35)]'
            : 'text-accent hover:bg-accent/10 hover:text-accent'" @click="plan = 'temporada'" type="button">
                    Temporada
                </button>
            </div>
        </div>

        <!-- Cards -->
        <div class="mt-12 grid gap-6 lg:grid-cols-3">

            <!-- Card 1 -->
            <div
                class="relative overflow-hidden group border border-white/15 backdrop-blur-[1px] p-6 sm:p-8 bg-gradient-to-tr from-transparent via-white/2 to-white/5 transition duration-300 ease-out hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_20px_70px_-30px_rgba(0,0,0,0.85)]">
                <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                    style="background: radial-gradient(650px 260px at 20% 0%, rgba(255,255,0,0.12), transparent 60%);">
                </div>

                <div class="relative">
                    <h3 class="uppercase font-black text-2xl sm:text-3xl text-white">Sambo Kids</h3>
                    <p
                        class="mt-3 text-main group-hover:text-[rgb(var(--c-gray)/0.90)] transition duration-200 text-sm sm:text-base leading-relaxed">
                        Programa formativo que combina judo, jiu jitsu, sambo, grappling y deportes de contacto para
                        aprender a luchar
                        con y sin kimono, defenderse de golpes y entrenar de forma segura en un entorno educativo.
                    </p>

                    <div class="mt-6">
                        <div class="text-4xl font-black text-white">
                            <span x-text="plan === 'mensual' ? '30€' : '250€'">30€</span>
                            <span class="text-accent text-sm font-bold"
                                x-text="plan === 'mensual' ? '/mes' : '/temporada'">/mes</span>
                        </div>
                        <div class="text-[rgb(var(--c-gray)/0.70)] text-sm mt-1 group-hover:text-[rgb(var(--c-gray)/0.85)] transition duration-200"
                            x-text="plan === 'mensual' ? 'Abonar mensualmente o 250€ por temporada' : 'Abonar 250€ por temporada (equivalente a 25€/mes)'">
                            Abonar mensualmente o 250€ por temporada
                        </div>
                    </div>

                    <ul class="mt-6 space-y-3 text-[rgb(var(--c-white)/0.80)] text-sm">
                        <li class="flex gap-3"><span class="text-accent">●</span> 3 días a la semana</li>
                        <li class="flex gap-3"><span class="text-accent">●</span> Seguro deportivo obligatorio</li>
                    </ul>

                    <a href="{{ route('public.preinscripcion') }}"
                        class="mt-8 inline-flex w-full justify-center font-brand uppercase not-italic bg-transparent border border-accent text-accent px-6 py-3 transition duration-200 ease-out hover:bg-accent hover:text-black hover:-translate-y-0.5 hover:shadow-[0_14px_40px_-20px_rgba(255,255,0,0.45)] active:translate-y-0">
                        Preinscríbete ya
                    </a>
                </div>
            </div>

            <!-- Card 2 (destacada) -->
            <div
                class="relative overflow-hidden group border-4 border-accent bg-gradient-to-br from-transparent via-[rgb(var(--c-accent)/0.10)] to-transparent shadow-[0_0_20px_0_rgba(255,255,0,0.16)] p-6 sm:p-8 transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_30px_90px_-35px_rgba(255,255,0,0.28)]">
                <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                    style="background: radial-gradient(700px 300px at 50% 10%, rgba(255,255,0,0.16), transparent 60%);">
                </div>

                <div class="relative">
                    <h3 class="uppercase font-black text-2xl sm:text-3xl text-white">MMA-Sambo</h3>
                    <p
                        class="mt-3 text-main group-hover:text-[rgb(var(--c-gray)/0.90)] transition duration-200 text-sm sm:text-base leading-relaxed">
                        Programa con un enfoque completo de combate: Sambo, Combat Sambo y MMA, combinando golpeo,
                        proyecciones y
                        trabajo en el suelo, con luxaciones y sumisiones. Un entrenamiento exigente orientado tanto a la
                        mejora personal
                        como a la competición.
                    </p>

                    <div class="mt-6">
                        <div class="text-4xl font-black text-white">
                            <span x-text="plan === 'mensual' ? '40€' : '300€'">40€</span>
                            <span class="text-accent text-sm font-bold"
                                x-text="plan === 'mensual' ? '/mes' : '/temporada'">/mes</span>
                        </div>
                        <div class="text-[rgb(var(--c-gray)/0.70)] text-sm mt-1 group-hover:text-[rgb(var(--c-gray)/0.85)] transition duration-200"
                            x-text="plan === 'mensual' ? 'Abonar mensualmente o 300€ por temporada' : 'Abonar 300€ por temporada (equivalente a 30€/mes)'">
                            Abonar mensualmente o 300€ por temporada
                        </div>
                    </div>

                    <ul class="mt-6 space-y-3 text-[rgb(var(--c-white)/0.80)] text-sm">
                        <li class="flex gap-3"><span class="text-accent">●</span> 3 días a la semana</li>
                        <li class="flex gap-3"><span class="text-accent">●</span> Seguro deportivo obligatorio</li>
                    </ul>

                    <a href="{{ route('public.preinscripcion') }}"
                        class="mt-8 inline-flex w-full justify-center font-brand uppercase not-italic bg-accent text-black px-6 py-3 transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                        Preinscríbete ya
                    </a>
                </div>
            </div>

            <!-- Card 3 -->
            <div
                class="relative overflow-hidden group border border-white/15 bg-gradient-to-tl from-transparent via-white/2 to-white/5 backdrop-blur-[1px] p-6 sm:p-8 flex flex-col justify-between transition duration-300 ease-out hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_20px_70px_-30px_rgba(0,0,0,0.85)]">
                <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                    style="background: radial-gradient(650px 260px at 20% 0%, rgba(255,255,0,0.10), transparent 60%);">
                </div>

                <div class="relative flex flex-col justify-between h-full">
                    <div class="flex flex-col">
                        <h3 class="uppercase font-black text-2xl sm:text-3xl text-white">Clases Privadas</h3>
                        <p
                            class="mt-3 text-main group-hover:text-[rgb(var(--c-gray)/0.90)] transition duration-200 text-sm sm:text-base leading-relaxed">
                            Entrenamiento personalizado adaptado a tus objetivos y nivel, con la opción de contratar
                            sesiones sueltas o un
                            bono de 4 entrenamientos individuales para un seguimiento más continuado.
                        </p>
                    </div>

                    <div class="mt-6">
                        <div class="flex sm:flex-row gap-6 sm:gap-8 items-start sm:items-end">
                            <div class="text-4xl font-black text-white">40€ <span
                                    class="text-accent text-sm font-bold">/sesión</span></div>
                            <div class="text-4xl font-black text-white">120€ <span
                                    class="text-accent text-sm font-bold">/4 sesiones</span></div>
                        </div>

                        <div
                            class="text-[rgb(var(--c-gray)/0.70)] text-sm mt-2 group-hover:text-[rgb(var(--c-gray)/0.85)] transition duration-200">
                            Abonar 40€ por sesión suelta o 120€ por bono de 4 sesiones
                        </div>

                        <div class="flex flex-col">
                            <ul class="mt-6 space-y-3 text-[rgb(var(--c-white)/0.80)] text-sm">
                                <li class="flex gap-3"><span class="text-accent">●</span> Entrenamiento adaptado a tu
                                    nivel y objetivos</li>
                                <li class="flex gap-3"><span class="text-accent">●</span> Seguimiento técnico individual
                                </li>
                            </ul>

                            <a href="{{ route('public.contacto') }}"
                                class="mt-8 inline-flex w-full justify-center font-brand uppercase not-italic bg-transparent border border-accent text-accent px-6 py-3 transition duration-200 ease-out hover:bg-accent hover:text-black hover:-translate-y-0.5 hover:shadow-[0_14px_40px_-20px_rgba(255,255,0,0.45)] active:translate-y-0">
                                Contacta ya
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex flex-col gap-4 m-auto w-fit mt-12">
            <a href="{{ route('public.planes') }}"
                class="w-fit inline-flex font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-5 py-3 text-sm sm:text-base md:text-lg transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 hover:shadow-[0_16px_50px_-22px_rgba(255,255,0,0.55)] active:translate-y-0">
                Ver más planes
            </a>
        </div>

    </div>
</section>

<!-- FAQ + CONTACTO -->
<section id="faq" class="reveal relative w-full">
    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        <div class="max-w-3xl">
            <h2 class="uppercase font-black leading-[0.9] text-4xl sm:text-5xl md:text-6xl text-white">
                ¿TIENES <span class="text-accent">DUDAS?</span>
            </h2>

            <p class="mt-3 text-main text-sm sm:text-base md:text-lg">
                Consulta las dudas más comunes, o visita nuestra
                <a href="{{ route('public.faq') }}" class="text-accent underline underline-offset-4 font-bold">
                    página de preguntas frecuentes
                </a>.
            </p>
        </div>

        @php
        $faqs = [
        ['q' => '¿Hace falta tener experiencia previa para apuntarse?', 'a' => 'No. Puedes empezar desde cero. Adaptamos
        el entrenamiento a tu nivel y progresas paso a paso.'],
        ['q' => '¿Qué disciplinas se practican en el club?', 'a' => 'Principalmente MMA y Sambo. En función del grupo
        también trabajamos lucha, grappling y fundamentos de combate.'],
        ['q' => '¿Cómo funcionan las cuotas?', 'a' => 'Tienes planes mensuales y por temporada. La cuota te cubre el
        periodo pagado y se tiene que volver a pagar al finalizar el periodo.'],
        ['q' => '¿Las clases son siempre en los mismos horarios?', 'a' => 'Normalmente sí, con un patrón semanal. Si hay
        cambios puntuales se avisan y se actualiza el calendario a través del grupo de WhatsApp.'],
        ['q' => '¿Cómo puedo apuntarme o pedir información?', 'a' => 'Puedes hacer la preinscripción online o contactar
        con nosotros por Instagram o WhatsApp. Te respondemos rápido.'],
        ];
        @endphp

        <div class="mt-8 border-2 border-accent bg-gradient-to-br from-transparent via-[rgb(var(--c-accent)/0.10)] to-transparent shadow-[0_0_20px_0_rgba(255,255,0,0.16)]"
            x-data="{ open: null }">
            <div class="px-5 sm:px-20 py-6 sm:py-10">

                @foreach($faqs as $i => $f)
                <div class="py-5 sm:py-6 {{ $i < count($faqs)-1 ? 'border-b border-dotted border-white/25' : '' }}">
                    <button type="button" class="w-full flex items-center justify-between gap-6 text-left"
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                        <span class="text-white/90 font-semibold text-base sm:text-lg md:text-xl">
                            {{ $f['q'] }}
                        </span>

                        <svg class="w-6 h-6 text-white/70 transition-transform"
                            :class="open === {{ $i }} ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div x-show="open === {{ $i }}" x-collapse.duration.250ms x-transition.opacity.duration.200ms
                        class="mt-4 overflow-hidden text-[rgb(var(--c-gray)/0.80)] text-sm sm:text-base">
                        {{ $f['a'] }}
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        <div class="mt-10 flex mx-auto justify-center items-center gap-10">
            <div class="font-black text-xl sm:text-3xl text-white/90">
                Mantente en contacto
            </div>

            <div class="flex items-center gap-6">
                <a href="https://www.instagram.com/novaunioteam"
                    class="w-10 h-10 grid place-items-center border-2 rounded-full border-white/25 hover:border-white/60 text-main hover:text-white transition">
                    <span class="sr-only">Instagram</span>
                    <svg width="20" height="20" viewBox="0 0 7 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3.25001 4.58329C3.60363 4.58329 3.94277 4.44282 4.19282 4.19277C4.44287 3.94272 4.58335 3.60358 4.58335 3.24996C4.58335 2.89634 4.44287 2.5572 4.19282 2.30715C3.94277 2.0571 3.60363 1.91663 3.25001 1.91663C2.89639 1.91663 2.55725 2.0571 2.3072 2.30715C2.05716 2.5572 1.91668 2.89634 1.91668 3.24996C1.91668 3.60358 2.05716 3.94272 2.3072 4.19277C2.55725 4.44282 2.89639 4.58329 3.25001 4.58329Z"
                            stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M0.25 4.58333V1.91667C0.25 1.47464 0.425595 1.05072 0.738155 0.738155C1.05072 0.425595 1.47464 0.25 1.91667 0.25H4.58333C5.02536 0.25 5.44928 0.425595 5.76184 0.738155C6.07441 1.05072 6.25 1.47464 6.25 1.91667V4.58333C6.25 5.02536 6.07441 5.44928 5.76184 5.76184C5.44928 6.07441 5.02536 6.25 4.58333 6.25H1.91667C1.47464 6.25 1.05072 6.07441 0.738155 5.76184C0.425595 5.44928 0.25 5.02536 0.25 4.58333Z"
                            stroke="currentColor" stroke-width="0.5" />
                    </svg>
                </a>

                <a href="https://www.tiktok.com/@novaunioteam"
                    class="w-10 h-10 grid place-items-center border-2 rounded-full border-white/25 hover:border-white/60 text-main hover:text-white transition">
                    <span class="sr-only">TikTok</span>
                    <svg fill="currentColor" width="20" height="20" viewBox="-3.2 -3.2 38.40 38.40"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z">
                        </path>
                    </svg>
                </a>

                <a href="mailto:contacto@novaunio.cat"
                    class="w-10 h-10 grid place-items-center border-2 rounded-full border-white/25 hover:border-white/60 text-main hover:text-white transition">
                    <span class="sr-only">Email</span>
                    <svg width="20" height="20" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.02326 0C0.751872 0 0.491602 0.112154 0.299705 0.311789C0.107807 0.511425 0 0.782189 0 1.06452V4.93548C0 5.21781 0.107807 5.48857 0.299705 5.68821C0.491602 5.88785 0.751872 6 1.02326 6H6.97674C7.24813 6 7.5084 5.88785 7.7003 5.68821C7.89219 5.48857 8 5.21781 8 4.93548V1.06452C8 0.782189 7.89219 0.511425 7.7003 0.311789C7.5084 0.112154 7.24813 0 6.97674 0H1.02326ZM2.29953 1.60103C2.23888 1.55985 2.16521 1.54482 2.09409 1.55911C2.02297 1.57341 1.95999 1.61591 1.91844 1.67763C1.87689 1.73935 1.86003 1.81545 1.87143 1.88987C1.88284 1.96428 1.9216 2.03116 1.97953 2.07639L3.84 3.43123C3.8869 3.46537 3.94276 3.48368 4 3.48368C4.05724 3.48368 4.1131 3.46537 4.16 3.43123L6.02047 2.07639C6.0518 2.05511 6.07871 2.0275 6.09958 1.99519C6.12045 1.96289 6.13486 1.92654 6.14196 1.88832C6.14906 1.8501 6.14869 1.81079 6.14089 1.77272C6.13309 1.73465 6.11801 1.6986 6.09654 1.66671C6.07508 1.63483 6.04767 1.60776 6.01595 1.58711C5.98422 1.56647 5.94883 1.55268 5.91188 1.54655C5.87493 1.54042 5.83717 1.54209 5.80085 1.55145C5.76453 1.56081 5.73039 1.57767 5.70047 1.60103L4 2.83935L2.29953 1.60103Z"
                            fill="currentColor" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Separador antes del footer -->
        <div class="mt-10 h-[4px]
      bg-[radial-gradient(circle,rgba(255,255,255,0.2)_2px,transparent_2px)]
      bg-[length:8px_4px]
      bg-repeat-x"></div>

    </div>
</section>

@endsection