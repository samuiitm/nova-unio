@php
    $grupo = $grupo ?? null;
    $colorActual = old('color', $grupo->color ?? '#7C5CFF');
@endphp

<div class="grid gap-4 sm:grid-cols-2" x-data="{ color: '{{ $colorActual }}' }">
    <div class="sm:col-span-2">
        <label class="text-sm font-medium">Nombre *</label>
        <input name="nombre"
               value="{{ old('nombre', $grupo->nombre ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               required>
    </div>

    <div>
        <label class="text-sm font-medium">Color del grupo *</label>

        <div class="mt-1 flex items-center gap-3">
            <input type="color"
                   x-model="color"
                   class="h-12 w-16 rounded-xl border panel-border bg-transparent p-1 cursor-pointer">

            <div class="flex-1">
                <input name="color"
                       x-model="color"
                       class="w-full panel-input px-4 py-3 uppercase"
                       pattern="^#[A-Fa-f0-9]{6}$"
                       required>
            </div>

            <span class="inline-block h-10 w-10 rounded-xl border panel-border"
                  :style="`background: ${color}`"></span>
        </div>

        <p class="mt-2 text-xs panel-muted">
            Este color se usará para identificar el grupo en el calendario.
        </p>
    </div>

    <div class="sm:col-span-2 flex items-center gap-3">
        @php
            $checked = old('activo', $grupo->activo ?? true) ? 'checked' : '';
        @endphp
        <input type="checkbox" name="activo" value="1" class="h-5 w-5" {{ $checked }}>
        <span class="text-sm">Grupo activo</span>
    </div>
</div>