@extends('layouts.public')
@section('title','Política de privacidad · Nova Unió')
@section('meta_description','Política de privacidad de Nova Unió: tratamiento de datos, finalidades, base legal y derechos del usuario.')

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

    <!-- Oscurecer general -->
    <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
  </div>

  <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl text-white">
      POLÍTICA <span class="text-accent">DE PRIVACIDAD</span>
    </h1>

    <p class="mt-4 text-main italic">
      Aquí explicamos qué datos podemos recoger, para qué los usamos y qué derechos tienes. Nos basamos en el RGPD (UE 2016/679) y la LOPDGDD.
    </p>

    <div class="mt-10 space-y-6">

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">1. Responsable del tratamiento</h2>
        <div class="mt-4 text-white/80 italic space-y-2">
          <p><span class="text-white/60">Titular:</span> Nova Unió</p>
          <p><span class="text-white/60">Email:</span> contacto@novaunio.cat</p>
          <p><span class="text-white/60">Web:</span> novaunio.cat</p>
        </div>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">2. Qué datos recogemos</h2>
        <div class="mt-4 text-white/80 italic space-y-2 leading-relaxed">
          <p>Podemos recoger datos como nombre, email, teléfono (si lo pones) y el mensaje que nos envíes en formularios (contacto o preinscripción).</p>
          <p>También se pueden recoger datos técnicos básicos de navegación (por ejemplo: IP, navegador, páginas visitadas) mediante cookies, si están activas.</p>
        </div>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">3. Para qué usamos los datos</h2>
        <div class="mt-4 text-white/80 italic space-y-2 leading-relaxed">
          <p>Usamos los datos principalmente para:</p>
          <ul class="mt-3 space-y-2 text-white/80 italic">
            <li class="flex gap-3"><span class="text-accent">●</span> Responder consultas y mensajes.</li>
            <li class="flex gap-3"><span class="text-accent">●</span> Gestionar solicitudes de preinscripción.</li>
            <li class="flex gap-3"><span class="text-accent">●</span> Mejorar la web (si hay analítica/cookies).</li>
          </ul>
        </div>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">4. Base legal</h2>
        <div class="mt-4 text-white/80 italic space-y-2 leading-relaxed">
          <p>La base legal suele ser:</p>
          <ul class="mt-3 space-y-2 text-white/80 italic">
            <li class="flex gap-3"><span class="text-accent">●</span> Tu consentimiento (cuando envías un formulario).</li>
            <li class="flex gap-3"><span class="text-accent">●</span> Interés legítimo (por ejemplo, responder un mensaje).</li>
            <li class="flex gap-3"><span class="text-accent">●</span> Obligación legal (si aplica en algún caso).</li>
          </ul>
        </div>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">5. Cuánto tiempo guardamos los datos</h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          Guardamos los datos el tiempo necesario para responder y gestionar la solicitud. Si al final te apuntas, se pueden conservar según la gestión interna del club.
          Si solo fue una consulta, se eliminarán cuando ya no hagan falta.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">6. A quién se comunican los datos</h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          No se ceden datos a terceros salvo obligación legal. Si se usan proveedores (por ejemplo hosting o email),
          actúan como encargados del tratamiento y solo para prestar el servicio.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">7. Tus derechos</h2>
        <div class="mt-4 text-white/80 italic space-y-2 leading-relaxed">
          <p>Puedes ejercer los derechos de acceso, rectificación, supresión, limitación, portabilidad y oposición.</p>
          <p>Para hacerlo, escribe a: <span class="text-accent">contacto@novaunio.cat</span> indicando qué derecho quieres ejercer y acreditando tu identidad.</p>
          <p>También puedes reclamar ante la AEPD si crees que algo no se ha hecho bien.</p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection