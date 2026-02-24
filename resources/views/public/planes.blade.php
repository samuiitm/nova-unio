@extends('layouts.public')
@section('title','Planes · Nova Unió')

@section('content')
<!-- PLANES -->
<section id="planes" class="relative home-hero plans-bg w-full py-16 sm:pt-24">
    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" x-data="{
         plan: 'mensual',
         label() {
           if (this.plan === 'mensual') return 'mes'
           if (this.plan === 'trimestral') return 'trimestre'
           if (this.plan === 'semestral') return 'semestre'
           return 'temporada'
         },
         kidsPrice() {
           if (this.plan === 'mensual') return 30
           if (this.plan === 'trimestral') return 85
           if (this.plan === 'semestral') return 165
           return 250
         },
         mmaPrice() {
           if (this.plan === 'mensual') return 40
           if (this.plan === 'trimestral') return 110
           if (this.plan === 'semestral') return 210
           return 300
         },
         kidsNote() {
           if (this.plan === 'mensual') return 'Abonar mensualmente o 250€ por temporada'
           if (this.plan === 'trimestral') return 'Abonar 85€ por trimestre'
           if (this.plan === 'semestral') return 'Abonar 165€ por semestre'
           return 'Abonar 250€ por temporada (equivalente a 25€/mes)'
         },
         mmaNote() {
           if (this.plan === 'mensual') return 'Abonar mensualmente o 300€ por temporada'
           if (this.plan === 'trimestral') return 'Abonar 110€ por trimestre'
           if (this.plan === 'semestral') return 'Abonar 210€ por semestre'
           return 'Abonar 300€ por temporada (equivalente a 30€/mes)'
         }
       }">

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

        <!-- Tabs -->
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
                    :class="plan === 'trimestral'
            ? 'bg-accent text-black shadow-[0_10px_30px_-18px_rgba(255,255,0,0.35)]'
            : 'text-accent hover:bg-accent/10 hover:text-accent'" @click="plan = 'trimestral'" type="button">
                    Trimestral
                </button>

                <button
                    class="font-brand uppercase not-italic px-5 py-2 text-sm sm:text-base transition duration-200 ease-out"
                    :class="plan === 'semestral'
            ? 'bg-accent text-black shadow-[0_10px_30px_-18px_rgba(255,255,0,0.35)]'
            : 'text-accent hover:bg-accent/10 hover:text-accent'" @click="plan = 'semestral'" type="button">
                    Semestral
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
                            <span x-text="kidsPrice() + '€'">30€</span>
                            <span class="text-accent text-sm font-bold" x-text="'/' + label()">/mes</span>
                        </div>

                        <div class="text-[rgb(var(--c-gray)/0.70)] text-sm mt-1 group-hover:text-[rgb(var(--c-gray)/0.85)] transition duration-200"
                            x-text="kidsNote()">
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
                            <span x-text="mmaPrice() + '€'">40€</span>
                            <span class="text-accent text-sm font-bold" x-text="'/' + label()">/mes</span>
                        </div>

                        <div class="text-[rgb(var(--c-gray)/0.70)] text-sm mt-1 group-hover:text-[rgb(var(--c-gray)/0.85)] transition duration-200"
                            x-text="mmaNote()">
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
                class="relative overflow-hidden group border border-white/15 bg-gradient-to-tl from-transparent via-white/2 to-white/5 backdrop-blur-[1px] p-6 sm:p-8 flex flex-col h-full transition duration-300 ease-out hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_20px_70px_-30px_rgba(0,0,0,0.85)]">
                <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                    style="background: radial-gradient(650px 260px at 20% 0%, rgba(255,255,0,0.10), transparent 60%);">
                </div>

                <div class="relative flex flex-col h-full">
                    <!-- ARRIBA -->
                    <div>
                        <h3 class="uppercase font-black text-2xl sm:text-3xl text-white">Clases Privadas</h3>
                        <p
                            class="mt-3 text-main group-hover:text-[rgb(var(--c-gray)/0.90)] transition duration-200 text-sm sm:text-base leading-relaxed">
                            Entrenamiento personalizado adaptado a tus objetivos y nivel, con la opción de contratar
                            sesiones sueltas o un bono de 4 entrenamientos individuales para un seguimiento más
                            continuado.
                        </p>
                    </div>

                    <!-- ABAJO -->
                    <div class="mt-auto pt-6">
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

                        <ul class="mt-6 space-y-3 text-[rgb(var(--c-white)/0.80)] text-sm">
                            <li class="flex gap-3"><span class="text-accent">●</span> Entrenamiento adaptado a tu nivel
                                y objetivos</li>
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
</section>
@endsection