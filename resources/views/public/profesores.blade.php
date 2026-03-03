@extends('layouts.public')
@section('title','Profesores · Nova Unió')

@section('meta_description','Equipo técnico de Nova Unió: entrenadores de MMA y Sambo con experiencia y metodología clara. Conócelos y entrena con confianza.')
@section('og_image', Vite::asset('resources/img/hero/profesores.webp'))

@push('head')
<link rel="preload" as="image" href="{{ Vite::asset('resources/img/hero/profesores.webp') }}" type="image/webp">
@endpush

@section('content')
@php
$profesores = [
[
'slug' => 'alejo-luna',
'nombre' => 'Alejo Luna',
'roles' => ['Clases MMA', 'Sambo Kids'],
'foto' => Vite::asset('resources/img/profesores/alejo-luna.webp'),
'fallback' => Vite::asset('resources/img/hero/hero-desktop.webp'),
'subtitulo' => 'Entrenador de MMA · Competidor activo',
'bio_corta' => 'Entrenador de MMA y Sambo kids en el club y luchador activo, donde compite a nivel amateur.',
'bio' => [
'Entrenador de MMA en el club y luchador activo, donde compite a nivel amateur con un recorrido de 3 victorias y 2
derrotas.',
'Además de su faceta como competidor, destaca por su disciplina, constancia y compromiso con el club, valores que
traslada directamente a cada entrenamiento.',
'Imparte las clases de MMA trabajando tanto la parte técnica como la mental, y adaptando los entrenamientos al nivel de
cada alumno.',
'Su experiencia en competición aporta una visión real del deporte, ayudando a los alumnos a mejorar, progresar y
entrenar con seriedad en un ambiente cercano y respetuoso.'
],
'stats' => [
['k' => 'AFL', 'v' => 'Liga'],
['k' => '3-2', 'v' => 'Récord'],
['k' => 'MMA', 'v' => 'Disciplina'],
],
'tags' => ['MMA','KIDS'],
],
[
  'slug' => 'marc-dailos',
  'nombre' => 'Marc Dailos',
  'roles' => ['Headcoach', 'Entrenador de Sambo'],
  'foto' => Vite::asset('resources/img/profesores/marc-dailos.webp'),
  'fallback' => Vite::asset('resources/img/hero/hero-desktop.webp'),
  'subtitulo' => 'Headcoach · Entrenador de Sambo',
  'bio_corta' => 'Deportista y entrenador con más de 30 años de experiencia en el mundo de la lucha, vinculado a judo y disciplinas de combate.',
  'bio' => [
    'Ha estado vinculado al judo durante años como competidor y profesor.',
    'Con el tiempo se ha especializado también en Sambo, artes marciales mixtas (MMA) y otras disciplinas de combate.',
    'Es presidente y figura clave del Club Esportiu Nova Unió, entidad centrada actualmente en el Sambo y deportes de lucha.',
    'Destaca por su vocación docente y de integración social a través del deporte, formando a jóvenes luchadores.'
  ],
  'stats' => [
    ['k' => 'ESP 2018', 'v' => 'Campeón España BJJ 2018'],
    ['k' => 'ESP 2019', 'v' => 'Campeón Copa España BJJ 2019'],
    ['k' => '30+', 'v' => 'Años de experiencia'],
  ],
  'tags' => ['SAMBO'],
],
[
  'slug' => 'zaki-banane',
  'nombre' => 'Zaki Banane',
  'roles' => ['Preparación física (competición)'],
  'foto' => Vite::asset('resources/img/profesores/zaki-banane.webp'),
  'fallback' => Vite::asset('resources/img/hero/hero-desktop.webp'),
  'subtitulo' => 'Preparador físico · Especialista en deportes de combate',
  'bio_corta' => 'Prepara físicamente a luchadores y competidores del club para mejorar el rendimiento real en combate: fuerza, potencia, resistencia específica y control corporal aplicado al gesto técnico.',
  'bio' => [
    'Trabaja como preparador físico enfocado al rendimiento deportivo competitivo, orientando el trabajo a capacidades que marcan la diferencia en competición: fuerza, potencia, resistencia específica y control corporal aplicado al gesto técnico.',
    'Cuenta con más de cinco años de experiencia preparando deportistas y equipos que compiten, desarrollando programas tanto individuales como colectivos según las demandas del deporte.',
    'Durante dos años dirigió su propio centro de entrenamiento (AZ Performance), trabajando con atletas de distintas disciplinas.',
    'Ha sido preparador físico en clubes de patinaje artístico, pádel, atletismo, fútbol sala y voleibol, además de su implicación directa en deportes de combate.',
    'Su enfoque parte de que el rendimiento no es genérico: cada deporte exige una estructura concreta y cada deportista necesita una planificación adaptada a su contexto competitivo, buscando transferencia directa a la competición.',
    'Formación: EXOS Performance Specialist Course · Especialización en preparación física para deportes de combate (Phil Daru).',
    'Palmarés: 3º de España en Combat Sambo 2025 · Jugador de voleibol en Primera División Autonómica.',
  ],
  'stats' => [
    ['k' => 'EXOS Certification', 'v' => 'Performance Specialist'],
    ['k' => 'ESP 2025', 'v' => '3º Combat Sambo'],
    ['k' => '5+', 'v' => 'Años exp.'],
  ],
  'tags' => ['PERFORMANCE', 'S&C'],
],
// POR SI HAY QUE AÑADIR MÁS PROFES
// [
// 'slug' => 'nombre-apellido',
// 'nombre' => 'Nombre Apellido',
// 'roles' => ['Sambo', 'Combat Sambo'],
// 'foto' => Vite::asset('resources/img/profesores/otro.webp'),
// 'fallback' => Vite::asset('resources/img/hero/hero.webp'),
// 'subtitulo' => 'Entrenador · ...',
// 'bio_corta' => '...',
// 'bio' => ['...'],
// 'stats' => [['k'=>'', 'v'=>'']],
// 'tags' => ['SAMBO'],
// ],
];
@endphp

<section class="relative w-full py-16 sm:pt-24" x-data="{
    filter: 'TODOS',
    open: '',
    matches(p) {
      if (this.filter === 'TODOS') return true;
      return (p.tags || []).includes(this.filter);
    },
    setOpen(slug) {
      this.open = (this.open === slug) ? '' : slug;
    }
  }">
    <!-- Fondo fijo para que fusione con footer -->
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-top opacity-50" style="background-image:
        linear-gradient(to bottom,
          rgba(0,0,0,0) 0%,
          rgba(0,0,0,0.25) 55%,
          rgba(0,0,0,0.75) 80%,
          rgba(0,0,0,1) 100%
        ),
        url('{{ Vite::asset('resources/img/hero/profesores.webp') }}');" aria-hidden="true"></div>

        <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Cabecera -->
        <div class="max-w-4xl">
            <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl md:text-7xl text-white">
                NUESTROS <span class="text-accent">PROFESORES</span>
            </h1>

            <p class="mt-4 text-main text-base sm:text-lg italic max-w-3xl">
                Conoce a nuestros expertos: combinan experiencia y dedicación para formar a luchadores de todos los
                niveles.
            </p>

            <!-- Filtros -->
            <div class="mt-7 flex flex-wrap gap-2">
                @php
                $filters = [
                ['k'=>'TODOS', 'label'=>'Todos'],
                ['k'=>'MMA', 'label'=>'MMA'],
                ['k'=>'SAMBO', 'label'=>'Sambo'],
                ['k'=>'KIDS', 'label'=>'Kids'],
                ];
                @endphp

                @foreach($filters as $f)
                <button type="button"
                    class="font-brand uppercase not-italic text-xs sm:text-sm px-4 py-2 border transition duration-200"
                    :class="filter === '{{ $f['k'] }}'
              ? 'bg-accent text-black border-accent shadow-[0_10px_28px_-18px_rgba(255,255,0,0.45)]'
              : 'border-white/15 text-white/70 hover:border-accent/60 hover:text-white'"
                    @click="filter = '{{ $f['k'] }}'">
                    {{ $f['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Listado -->
        <div class="mt-10 space-y-6">
            @foreach($profesores as $p)
            <article x-show="matches({ tags: @js($p['tags']) })" x-transition.opacity.duration.200ms
                class="relative overflow-hidden group border border-white/15 bg-black/20 backdrop-blur-[1px] transition duration-300 ease-out hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_22px_80px_-35px_rgba(0,0,0,0.85)]">
                <!-- glow -->
                <div class="pointer-events-none absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-300"
                    style="background: radial-gradient(900px 300px at 25% 30%, rgba(255,255,0,0.10), transparent 60%);">
                </div>

                <div class="relative grid md:grid-cols-[280px_1fr] lg:grid-cols-[320px_1fr] gap-0">
                    <!-- Foto -->
                    <div class="relative">
                        <div class="aspect-[4/5] md:aspect-auto md:h-full bg-black/40 overflow-hidden">
                            <img src="{{ $p['foto'] }}" onerror="this.onerror=null;this.src='{{ $p['fallback'] }}';"
                            alt="Foto de {{ $p['nombre'] }}"
                            class="h-full w-full object-cover opacity-95 group-hover:opacity-100 transition duration-300"
                            width="750" height="1000" decoding="async"
                            loading="lazy">
                        </div>

                        <!-- overlay foto -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                    </div>

                    <!-- Contenido -->
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col gap-4">
                            <!-- Nombre + roles -->
                            <div>
                                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-2">
                                    <h2 class="uppercase font-black italic text-3xl sm:text-4xl text-accent">
                                        {{ $p['nombre'] }}
                                    </h2>
                                    <div class="text-white/70 italic text-sm sm:text-base">
                                        {{ $p['subtitulo'] }}
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($p['roles'] as $r)
                                    <span
                                        class="font-brand uppercase not-italic text-xs px-3 py-1 border border-accent/60 text-accent bg-black/15">
                                        {{ $r }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Bio corta -->
                            <p class="text-main italic leading-relaxed">
                                {{ $p['bio_corta'] }}
                            </p>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-2 sm:gap-3 max-w-xl">
                                @foreach($p['stats'] as $s)
                                <div class="border border-white/10 bg-black/25 p-3">
                                    <div class="uppercase font-black italic text-white text-lg leading-none">
                                        {{ $s['k'] }}
                                    </div>
                                    <div class="mt-1 text-white/55 italic text-xs sm:text-sm">
                                        {{ $s['v'] }}
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Expand -->
                            <div class="border-t border-white/10 pt-5">
                                <button type="button" class="w-full flex items-center justify-between text-left"
                                    @click="setOpen('{{ $p['slug'] }}')">
                                    <span class="font-brand uppercase not-italic text-accent tracking-wide">
                                        Ver descripción completa
                                    </span>
                                    <span class="text-white/70 text-2xl leading-none transition"
                                        :class="open === '{{ $p['slug'] }}' ? 'rotate-45' : ''">+</span>
                                </button>

                                <div x-show="open === '{{ $p['slug'] }}'" x-collapse class="mt-4 space-y-4">
                                    @foreach($p['bio'] as $par)
                                    <p class="text-white/75 italic leading-relaxed">
                                        {{ $par }}
                                    </p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Nota final -->
        <div class="mt-10 text-center text-white/55 italic text-sm">
            ¿No sabes qué grupo es para ti? Escríbenos y te orientamos.
        </div>
    </div>
</section>
@endsection