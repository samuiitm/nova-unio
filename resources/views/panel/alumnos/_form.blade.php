@php
    $alumno = $alumno ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-medium">Nombre *</label>
        <input name="nombre"
               value="{{ old('nombre', $alumno->nombre ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               required>
    </div>

    <div>
        <label class="text-sm font-medium">Apellidos *</label>
        <input name="apellidos"
               value="{{ old('apellidos', $alumno->apellidos ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               required>
    </div>

    <div>
        <label class="text-sm font-medium">CatSalut</label>
        <input name="catsalut"
               value="{{ old('catsalut', $alumno->catsalut ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="Código CatSalut">
    </div>

    <div>
        <label class="text-sm font-medium">DNI</label>
        <input name="dni"
               value="{{ old('dni', $alumno->dni ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="12345678X">
    </div>

    <div>
        <label class="text-sm font-medium">Fecha de nacimiento</label>
        <input type="date"
               name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', optional($alumno->fecha_nacimiento ?? null)->format('Y-m-d')) }}"
               class="mt-1 w-full panel-input px-4 py-3">
    </div>

    <div>
        <label class="text-sm font-medium">Lugar de nacimiento</label>
        <input name="lugar_nacimiento"
               value="{{ old('lugar_nacimiento', $alumno->lugar_nacimiento ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="Ciudad / País">
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-medium">Dirección</label>
        <input name="direccion"
               value="{{ old('direccion', $alumno->direccion ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="Calle, número, piso...">
    </div>

    <div>
        <label class="text-sm font-medium">CP</label>
        <input name="cp"
               value="{{ old('cp', $alumno->cp ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="08000">
    </div>

    <div>
        <label class="text-sm font-medium">Población</label>
        <input name="poblacion"
               value="{{ old('poblacion', $alumno->poblacion ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="Barcelona">
    </div>

    <div>
        <label class="text-sm font-medium">Teléfono</label>
        <input name="telefono"
               value="{{ old('telefono', $alumno->telefono ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="600 000 000">
    </div>

    <div>
        <label class="text-sm font-medium">Email</label>
        <input type="email"
               name="email"
               value="{{ old('email', $alumno->email ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               placeholder="correo@ejemplo.com">
    </div>

    <div class="sm:col-span-2">
        <label class="text-sm font-medium">Notas</label>
        <textarea name="notas"
                  rows="4"
                  class="mt-1 w-full panel-input px-4 py-3"
                  placeholder="Notas internas...">{{ old('notas', $alumno->notas ?? '') }}</textarea>
    </div>
</div>