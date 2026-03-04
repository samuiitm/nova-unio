@php
    $grupo = $grupo ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="text-sm font-medium">Nombre *</label>
        <input name="nombre"
               value="{{ old('nombre', $grupo->nombre ?? '') }}"
               class="mt-1 w-full panel-input px-4 py-3"
               required>
    </div>

    <div class="sm:col-span-2 flex items-center gap-3">
        @php
            $checked = old('activo', $grupo->activo ?? true) ? 'checked' : '';
        @endphp
        <input type="checkbox" name="activo" value="1" class="h-5 w-5" {{ $checked }}>
        <span class="text-sm">Grupo activo</span>
    </div>
</div>