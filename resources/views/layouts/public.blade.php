<!doctype html>
<html lang="es">
<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="8k-PPG0EF-rqgSVuGiRkWwZaN2AWPCzJz-u5ELU8yiU" />

    @php
        $siteName    = config('app.name', 'Nova Unió | MMA & Sambo | Lloret de Mar');
        $canonical   = trim($__env->yieldContent('canonical')) ?: url()->current();
        $title       = trim($__env->yieldContent('title')) ?: $siteName;
        $description = trim($__env->yieldContent('meta_description')) ?: 'Club de artes marciales Nova Unió: MMA y Sambo. Entrenamientos para niños, adolescentes y adultos. Planes, horarios, profesores y preinscripción online.';
        $ogType      = trim($__env->yieldContent('og_type')) ?: 'website';
        $ogImage     = trim($__env->yieldContent('og_image')) ?: Vite::asset('resources/img/banner.webp');
        $ogImageAbs  = str_starts_with($ogImage, 'http') ? $ogImage : url($ogImage);
        $robots      = trim($__env->yieldContent('meta_robots')) ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    @endphp

    <title>{{!! $title !!}}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="es" href="{{ $canonical }}">

    <!-- Open Graph -->
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:locale" content="es_ES">
    <meta property="og:image" content="{{ $ogImageAbs }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImageAbs }}">

    <meta name="theme-color" content="#000000">

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="preconnect" href="https://p.typekit.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="https://use.typekit.net/fxa0uin.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=League+Spartan:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @hasSection('jsonld')
        @yield('jsonld')
    @else
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => url(Vite::asset('resources/img/logo.png')),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    @stack('head')
</head>

<body class="min-h-screen bg-black text-main">
<header
  class="header-dynamic z-[90]"
  x-data="{ open: false, a: 0 }"
  x-init="
    const clamp = (v,min,max)=>Math.min(max,Math.max(min,v));
    let ticking = false;

    const update = () => {
      const y = window.scrollY || 0;
      a = clamp(y / 180, 0, 1);
      $el.style.setProperty('--a', a);
      ticking = false;
    };

    const onScroll = () => {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(update);
      }
    };

    $watch('open', v => {
      document.documentElement.classList.toggle('overflow-hidden', v);
      if (v) {
        a = 1;
        $el.style.setProperty('--a', 1);
      } else {
        update();
      }
    });

    update();
    window.addEventListener('scroll', onScroll, { passive: true });
  "
>
  <nav class="relative w-full px-4 lg:px-16 2xl:px-24 py-4 flex items-center justify-between">
    <a href="{{ route('public.home') }}" class="flex items-center gap-3 transition duration-200 ease-out hover:-translate-y-0.5 active:translate-y-0">
      <img src="{{ Vite::asset('resources/img/logo-novaunio.svg') }}" alt="Nova Unió" class="h-10 sm:h-12 w-auto opacity-90">
    </a>

    <div class="flex items-center gap-3">
      <a href="{{ route('public.preinscripcion') }}"
         class="font-brand font-semibold uppercase tracking-wide not-italic bg-accent text-black px-3 py-2 text-sm sm:text-base transition duration-200 ease-out hover:-translate-y-0.5 hover:brightness-110 active:translate-y-0">
        Preinscripción
      </a>

      <button
        class="inline-flex items-center justify-center text-main hover:text-white transition"
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-label="Abrir menú"
      >
        <!-- Hamburguesa -->
        <svg x-show="!open" x-transition.opacity class="w-8 h-8 sm:w-9 sm:h-9" viewBox="0 0 24 24" fill="none">
          <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>

        <!-- X -->
        <svg x-show="open" x-transition.opacity class="w-8 h-8 sm:w-9 sm:h-9" viewBox="0 0 24 24" fill="none">
          <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
    </div>
  </nav>

  <!-- MENÚ FULLSCREEN: TELEPORT AL BODY -->
  <template x-teleport="body">
    <div
      x-show="open"
      x-cloak
      x-transition:enter="transition-opacity duration-200 ease-out"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition-opacity duration-150 ease-in"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      class="fixed inset-0 z-[80] bg-black/95"
      @click.self="open = false"
      @keydown.escape.window="open = false"
    >

      <div class="h-full flex flex-col justify-center mt-8 px-4 sm:px-6 lg:px-16 2xl:px-24">
        <div class="mx-auto w-full max-w-3xl">

          @php
            $menu = [
              ['label' => 'EL CLUB', 'href' => route('public.elclub')],
              ['label' => 'PLANES', 'href' => route('public.planes')],
              ['label' => 'HORARIOS', 'href' => route('public.horarios')],
              ['label' => 'CONTACTO', 'href' => route('public.contacto')],
            ];
          @endphp

          <!-- Items grandes -->
          <nav class="space-y-6 sm:space-y-7 w-full">
            @foreach($menu as $i => $item)
              <a
                href="{{ $item['href'] }}"
                @click="open = false"
                class="group grid grid-cols-[1fr_auto] items-center gap-10 text-4xl sm:text-5xl md:text-6xl uppercase font-black italic text-white/90 hover:text-white transition"
                x-show="open"
                x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0 translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition duration-150 ease-in"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                :style="open ? 'transition-delay: {{ 80 + ($i * 70) }}ms' : 'transition-delay: 0ms'"
              >
                <span>{{ $item['label'] }}</span>
                <span class="min-w-[2rem] text-right text-white/60 group-hover:text-white transition text-3xl sm:text-4xl">›</span>
              </a>
            @endforeach
          </nav>

          <!-- Links pequeños + redes -->
          <div
            class="mt-10 sm:mt-12 text-white/70"
            x-show="open"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            :style="open ? 'transition-delay: 420ms' : 'transition-delay: 0ms'"
          >
            <div class="space-y-2 text-base sm:text-lg italic">
              <a href="{{ route('public.profesores') }}" @click="open = false" class="block hover:text-white">
                Conoce a nuestros entrenadores
              </a>
              <a href="{{ route('public.faq') }}" @click="open = false" class="block hover:text-white">
                Preguntas frecuentes
              </a>
            </div>

            <div class="flex items-center gap-4 mt-6">
              <!-- Instagram -->
              <a href="https://www.instagram.com/novaunioteam"
                 class="w-8 h-8 grid place-items-center border-2 rounded-full border-white/25 hover:border-white/60 text-main hover:text-white transition"
                 aria-label="Instagram">
                <svg width="16" height="16" viewBox="0 0 7 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.25001 4.58329C3.60363 4.58329 3.94277 4.44282 4.19282 4.19277C4.44287 3.94272 4.58335 3.60358 4.58335 3.24996C4.58335 2.89634 4.44287 2.5572 4.19282 2.30715C3.94277 2.0571 3.60363 1.91663 3.25001 1.91663C2.89639 1.91663 2.55725 2.0571 2.3072 2.30715C2.05716 2.5572 1.91668 2.89634 1.91668 3.24996C1.91668 3.60358 2.05716 3.94272 2.3072 4.19277C2.55725 4.44282 2.89639 4.58329 3.25001 4.58329Z" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M0.25 4.58333V1.91667C0.25 1.47464 0.425595 1.05072 0.738155 0.738155C1.05072 0.425595 1.47464 0.25 1.91667 0.25H4.58333C5.02536 0.25 5.44928 0.425595 5.76184 0.738155C6.07441 1.05072 6.25 1.47464 6.25 1.91667V4.58333C6.25 5.02536 6.07441 5.44928 5.76184 5.76184C5.44928 6.07441 5.02536 6.25 4.58333 6.25H1.91667C1.47464 6.25 1.05072 6.07441 0.738155 5.76184C0.425595 5.44928 0.25 5.02536 0.25 4.58333Z" stroke="currentColor" stroke-width="0.5"/>
                </svg>
              </a>

              <!-- TikTok -->
              <a href="https://www.tiktok.com/@novaunioteam"
                 class="w-8 h-8 grid place-items-center border-2 rounded-full border-white/25 hover:border-white/60 text-main hover:text-white transition"
                 aria-label="TikTok">
                <svg fill="currentColor" width="16" height="16" viewBox="-3.2 -3.2 38.40 38.40" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"></path>
                </svg>
              </a>

              <!-- Email -->
              <a href="mailto:contacto@novaunio.cat"
                 class="w-8 h-8 grid place-items-center border-2 rounded-full border-white/25 hover:border-white/60 text-main hover:text-white transition"
                 aria-label="Email">
                <svg width="16" height="16" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M1.02326 0C0.751872 0 0.491602 0.112154 0.299705 0.311789C0.107807 0.511425 0 0.782189 0 1.06452V4.93548C0 5.21781 0.107807 5.48857 0.299705 5.68821C0.491602 5.88785 0.751872 6 1.02326 6H6.97674C7.24813 6 7.5084 5.88785 7.7003 5.68821C7.89219 5.48857 8 5.21781 8 4.93548V1.06452C8 0.782189 7.89219 0.511425 7.7003 0.311789C7.5084 0.112154 7.24813 0 6.97674 0H1.02326ZM2.29953 1.60103C2.23888 1.55985 2.16521 1.54482 2.09409 1.55911C2.02297 1.57341 1.95999 1.61591 1.91844 1.67763C1.87689 1.73935 1.86003 1.81545 1.87143 1.88987C1.88284 1.96428 1.9216 2.03116 1.97953 2.07639L3.84 3.43123C3.8869 3.46537 3.94276 3.48368 4 3.48368C4.05724 3.48368 4.1131 3.46537 4.16 3.43123L6.02047 2.07639C6.0518 2.05511 6.07871 2.0275 6.09958 1.99519C6.12045 1.96289 6.13486 1.92654 6.14196 1.88832C6.14906 1.8501 6.14869 1.81079 6.14089 1.77272C6.13309 1.73465 6.11801 1.6986 6.09654 1.66671C6.07508 1.63483 6.04767 1.60776 6.01595 1.58711C5.98422 1.56647 5.94883 1.55268 5.91188 1.54655C5.87493 1.54042 5.83717 1.54209 5.80085 1.55145C5.76453 1.56081 5.73039 1.57767 5.70047 1.60103L4 2.83935L2.29953 1.60103Z" fill="currentColor"/>
                </svg>
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </template>
</header>

<main class="mx-auto flex flex-col gap-10">
    @yield('content')
</main>

<footer>
    <div class="max-w-7xl m-auto text-xs px-4 lg:px-16 lg:text-lg 2xl:px-24 py-[1.7rem] flex items-center justify-between">
        <p class="text-main">
          © Club Esportiu Nova Unió {{ date('Y') }}. Diseñado y desarrollado por
          <a href="https://samuiitm.github.io" class="text-accent underline">samuiitm</a>
        </p>

        <div class="flex items-center gap-4">
          <a href="{{ route('public.aviso-legal') }}" class="text-main hover:text-white transition">Aviso Legal</a>
          <a href="{{ route('public.politica-privacidad') }}" class="text-main hover:text-white transition">Política de Privacidad</a>
          <a href="{{ route('public.politica-cookies') }}" class="text-main hover:text-white transition">Política de Cookies</a>
        </div>
    </div>
</footer>

</body>
</html>