@extends('layouts.public')
@section('title','El club · Nova Unió')

@section('content')
@php
  $steps = [
    [
      'kicker' => 'RECUERDOS',
      'title'  => 'El tiempo pasa, el tatami queda',
      'text'   => [
        'Los años han pasado rápidamente y es difícil no mirar atrás y sonreír por tan buenos momentos vividos en nuestro tatami y en todos los amigos que hemos ido haciendo por el camino.'
      ],
      'img'    => Vite::asset('resources/img/el-club/recuerdos.webp'),
      'pos'    => '50% 20%',
    ],
    [
      'kicker' => '2003',
      'title'  => 'Un club diferente desde el inicio',
      'text'   => [
        'Fundado en 2003 nuestro club inició su andadura como un club de judo atípico para esos años donde se entrenaba tanto con quimono como sin quimono.',
        'Era habitual entrenar técnicas de otras artes marciales de forma cruzada, con una mentalidad abierta y práctica.'
      ],
      'img'    => Vite::asset('resources/img/el-club/inicio-club.webp'),
      'pos'    => '65% 35%',
    ],
    [
      'kicker' => 'DISCIPLINAS',
      'title'  => 'Competición y aprendizaje real',
      'text'   => [
        'Competíamos en judo, sambo, jiu jitsu tradicional, jiu jitsu brasileño, grappling y MMA.',
        'Cada etapa nos fue aportando experiencia y una forma de entrenar centrada en lo que funciona.'
      ],
      'img'    => Vite::asset('resources/img/el-club/disciplinas.webp'),
      'pos'    => '45% 70%',
    ],
    [
      'kicker' => 'PANDEMIA',
      'title'  => 'Reinventarse para seguir',
      'text'   => [
        'Con la pandemia y el confinamiento el club se ha tenido que reinventar y actualmente estamos afiliados a la Federación Catalana de Lucha.',
        'Seguimos avanzando con la misma idea de siempre: constancia, respeto y buen ambiente.'
      ],
      'img'    => Vite::asset('resources/img/el-club/pandemia.webp'),
      'pos'    => '50% 55%',
    ],
    [
      'kicker' => 'KIDS',
      'title'  => 'Base segura para los pequeños',
      'text'   => [
        'Hemos diseñado un sistema para nuestros alumnos más pequeños que incluye las técnicas más seguras de todas las artes marciales y estilos de lucha que practicamos en el club.',
        'Así pueden aprender bien, disfrutar, y decidir en qué prefieren especializarse cuando sean mayores.'
      ],
      'img'    => Vite::asset('resources/img/el-club/kids.webp'),
      'pos'    => '55% 25%',
    ],
    [
      'kicker' => 'ADULTOS',
      'title'  => 'Sambo, Combat Sambo y MMA',
      'text'   => [
        'Para adultos ofrecemos clases de lucha sambo, combat sambo y MMA.',
        'Nos vemos a los tatamis compañer@s.'
      ],
      'img'    => Vite::asset('resources/img/el-club/adultos.webp'),
      'pos'    => '50% 35%',
    ],
  ];
@endphp

<section
  class="w-full relative"
  x-data="{
    active: 0,
    total: {{ count($steps) }},
    _lastWheel: 0,
    _wheelBound: false,

    init() {
        const items = this.$el.querySelectorAll('[data-step]');
        if (!items.length) return;

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                if (e.isIntersecting) this.active = Number(e.target.dataset.step || 0);
            });
        }, {
            rootMargin: '-45% 0px -45% 0px',
            threshold: 0
        });

        items.forEach(el => io.observe(el));
      }

      // 2) Control de scroll con rueda (solo en desktop)
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
        if (!isDesktop) return;

        const wheelHandler = (e) => {
        // Solo si el scroll ocurre dentro de esta sección
        if (!this.$el.contains(e.target)) return;

        // si está haciendo zoom con ctrl, no toques
        if (e.ctrlKey) return;

        const dy = e.deltaY || 0;

        // trackpads a veces dan valores muy pequeños
        if (Math.abs(dy) < 2) return;

        const dir = dy > 0 ? 1 : -1;

        // Si ya estamos al principio y sube, o al final y baja, deja scroll normal
        if ((dir < 0 && this.active <= 0) || (dir > 0 && this.active >= this.total - 1)) {
            return;
        }

        const now = Date.now();
        if (now - this._lastWheel < 550) {
            e.preventDefault();
            return;
        }
        this._lastWheel = now;

        e.preventDefault();
        this.goClamp(this.active + dir);
        };

        // IMPORTANTE: en window, para que siempre se capture
        window.addEventListener('wheel', wheelHandler, { passive: false });

        // limpieza al salir de la página
        if (this.$cleanup) {
        this.$cleanup(() => window.removeEventListener('wheel', wheelHandler));
        }

        this._wheelBound = true;
    },

    go(i) {
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const isDesktop = window.matchMedia('(min-width: 1024px)').matches;

        // si es el primer capítulo, sube al título
        const el = (i === 0)
            ? document.getElementById('club-top')
            : document.getElementById('cap-' + i);

        if (!el) return;

        el.scrollIntoView({
            behavior: reduced ? 'auto' : 'smooth',
            // el primer capítulo arriba, los demás centrados en desktop
            block: (i === 0) ? 'start' : (isDesktop ? 'center' : 'start')
        });
    },

    goClamp(i) {
      const n = Math.max(0, Math.min(i, this.total - 1));
      this.active = n;
      this.go(n);
    }
  }"
  x-init="init()"
>
  <!-- FONDO FIJO QUE CAMBIA -->
  <div class="fixed inset-0 z-0 pointer-events-none">
    @foreach($steps as $i => $s)
      <div
        class="absolute inset-0 bg-cover bg-center transition-opacity duration-700 ease-out"
        style="background-image:url('{{ $s['img'] }}'); background-position: {{ $s['pos'] }};"
        :class="active === {{ $i }} ? 'opacity-100' : 'opacity-0'"
        aria-hidden="true"
      ></div>
    @endforeach

    <div class="absolute inset-0 bg-black/55"></div>

    <div class="absolute inset-0 opacity-[0.10] mix-blend-overlay"
         style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27140%27 height=%27140%27%3E%3Cfilter id=%27n%27%3E%3CfeTurbulence type=%27fractalNoise%27 baseFrequency=%27.8%27 numOctaves=%273%27 stitchTiles=%27stitch%27/%3E%3C/filter%3E%3Crect width=%27140%27 height=%27140%27 filter=%27url(%23n)%27 opacity=%27.6%27/%3E%3C/svg%3E'); background-repeat:repeat;">
    </div>

    <div class="absolute inset-x-0 bottom-0 h-56 bg-gradient-to-t from-black/85 to-transparent"></div>
  </div>

  <!-- CONTENIDO -->
  <div class="relative z-10 px-4 lg:px-16 2xl:px-24 pt-24">
    <div id="club-top" class="max-w-3xl scroll-mt-28">
      <h1 class="uppercase font-black italic text-white text-5xl sm:text-6xl md:text-7xl leading-none">
        EL <span class="text-accent">CLUB</span>
      </h1>
      <p class="mt-6 text-sm sm:text-base md:text-lg text-white/80 italic max-w-2xl">
        Un recorrido por nuestra historia y por la forma en la que entendemos el entrenamiento.
      </p>
    </div>

    <div class="mt-6 relative">
      <!-- Bloques -->
      <div class="space-y-6 sm:space-y-8 lg:space-y-10 lg:pr-72">
        @foreach($steps as $i => $s)
          <article
            id="cap-{{ $i }}"
            data-step="{{ $i }}"
            class="{{ $i === 0
                    ? 'min-h-[70svh] sm:min-h-[80svh] lg:min-h-[100svh] flex justify-start items-start pt-6 sm:pt-10 lg:pt-10 scroll-mt-28'
                    : 'py-10 sm:py-12 lg:py-0 lg:min-h-[100svh] flex justify-start items-start lg:items-center scroll-mt-28'
                }}"
          >
            <div class="max-w-2xl w-full border border-white/15 bg-black/25 backdrop-blur-sm shadow-[0_0_0_1px_rgba(255,255,255,0.03)] p-6 sm:p-8 mb-10">
              <div class="flex items-center gap-3">
                <span class="font-brand uppercase tracking-wide text-sm bg-accent text-black px-2 py-1">
                  {{ $s['kicker'] }}
                </span>
                <div class="h-px flex-1 bg-white/15"></div>
              </div>

              <h2 class="mt-5 text-3xl sm:text-4xl uppercase font-black italic text-white">
                {{ $s['title'] }}
              </h2>

              <div class="mt-5 space-y-4 text-white/80 italic leading-relaxed">
                @foreach($s['text'] as $p)
                  <p>{{ $p }}</p>
                @endforeach
              </div>

              <div class="mt-6 text-white/50 text-sm italic">
                {{ $i + 1 }} / {{ count($steps) }}
              </div>
            </div>
          </article>
        @endforeach

        <!-- CTA final -->
        <div class="pb-12">
          <div class="max-w-2xl border border-accent bg-black/35 backdrop-blur-sm p-6 sm:p-8">
            <h3 class="text-2xl sm:text-3xl uppercase font-black italic text-white">
              ¿Te apuntas a entrenar?
            </h3>
            <p class="mt-3 text-white/75 italic">
              Mira horarios, conoce a los entrenadores y si quieres, deja tu preinscripción.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
              <a href="{{ route('public.horarios') }}"
                 class="font-brand font-semibold uppercase tracking-wide bg-accent text-black px-4 py-3">
                Ver horarios
              </a>
              <a href="{{ route('public.profesores') }}"
                 class="font-brand font-semibold uppercase tracking-wide border-2 border-white/25 text-white px-4 py-3 hover:border-white/60 transition">
                Profesores
              </a>
              <a href="{{ route('public.preinscripcion') }}"
                 class="font-brand font-semibold uppercase tracking-wide border-2 border-accent text-accent px-4 py-3 hover:bg-accent hover:text-black transition">
                Preinscripción
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Índice lateral -->
      <aside class="hidden lg:block fixed top-24 right-16 2xl:right-24 z-20">
        <div class="border border-white/10 bg-black/25 backdrop-blur-sm p-4 w-64">
          <div class="text-white/70 uppercase font-brand tracking-wide text-sm">
            Capítulos
          </div>
          <ul class="mt-4 space-y-2">
            @foreach($steps as $i => $s)
              <li>
                <button
                  type="button"
                  class="w-full text-left flex items-center gap-3 py-2 transition"
                  :class="active === {{ $i }} ? 'text-white' : 'text-white/55 hover:text-white/80'"
                  @click.prevent="goClamp({{ $i }})"
                >
                  <span
                    class="inline-block w-2 h-2 rounded-full"
                    :class="active === {{ $i }} ? 'bg-accent' : 'bg-white/25'"
                  ></span>
                  <span class="uppercase italic">
                    {{ $s['kicker'] }}
                  </span>
                  <span class="ml-auto text-xs text-white/35">
                    {{ $i + 1 }}
                  </span>
                </button>
              </li>
            @endforeach
          </ul>
        </div>
      </aside>
    </div>
  </div>
</section>
@endsection