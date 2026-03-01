@extends('layouts.public')
@section('title','Aviso legal · Nova Unió')

@section('meta_description','Aviso legal de Nova Unió: información del titular, condiciones de uso y responsabilidades.')

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

    <div class="absolute inset-0 bg-black/80" aria-hidden="true"></div>
  </div>

  <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <h1 class="uppercase font-black italic leading-[0.9] text-5xl sm:text-6xl text-white">
      AVISO <span class="text-accent">LEGAL</span>
    </h1>

    <p class="mt-4 text-main italic">
      Este documento se publica para cumplir con la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico (LSSI-CE).
    </p>

    <div class="mt-10 space-y-6">

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">
          1. Datos del responsable
        </h2>
        <div class="mt-4 text-white/80 italic space-y-2">
          <p><span class="text-white/60">Titular:</span> Nova Unió</p>
          <p><span class="text-white/60">Correo:</span> contacto@novaunio.cat</p>
          <p><span class="text-white/60">Dominio:</span> novaunio.cat</p>
        </div>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">
          2. Objeto y uso del sitio web
        </h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          Este sitio web tiene finalidad informativa sobre el club Nova Unió (MMA y Sambo) y, si se habilita, permite enviar solicitudes de contacto o preinscripción.
          El acceso a la web atribuye la condición de usuario, y supone aceptar estas condiciones.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">
          3. Propiedad intelectual e industrial
        </h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          Los contenidos de esta web (textos, imágenes, logotipos, diseño y código) están protegidos por la normativa de propiedad intelectual e industrial.
          No se permite su reproducción o distribución sin autorización del titular, salvo en los casos permitidos por la ley.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">
          4. Responsabilidad
        </h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          El responsable no se hace responsable del mal uso que se realice del contenido de la web ni de errores puntuales.
          Esta web puede incluir enlaces a terceros; el responsable no controla esos contenidos externos y no asume responsabilidad por ellos.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">
          5. Modificaciones
        </h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          El responsable se reserva el derecho de modificar, actualizar o eliminar contenidos del sitio web en cualquier momento,
          sin necesidad de aviso previo, publicándose la versión actualizada en esta misma página.
        </p>
      </div>

      <div class="border border-white/10 bg-black/20 backdrop-blur-[1px] p-6">
        <h2 class="uppercase font-black italic text-white text-2xl">
          6. Legislación aplicable
        </h2>
        <p class="mt-4 text-white/80 italic leading-relaxed">
          Estas condiciones se rigen por la legislación española. En caso de conflicto, se aplicará la normativa vigente.
        </p>
      </div>
    </div>
  </div>
</section>
@endsection