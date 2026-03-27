@php
    $tiposTicket = collect($tiposSeguro)->map(fn ($tipoSeguro, $clave) => [
        'clave' => $clave,
        'nombre' => $tipoSeguro['nombre'],
        'importe' => (float) $tipoSeguro['importe'],
    ])->values();

    $alumnoSeleccionadoId = old('alumno_id', optional($seguro)->alumno_id ?? $alumnoPreseleccionado?->id ?? '');
    $tipoSeleccionado = old('tipo', optional($seguro)->tipo ?? '');
    $estadoSeleccionado = old('estado', optional($seguro)->estado ?? 'pagado');
    $fechaPagoSeleccionada = old('fecha_pago', optional(optional($seguro)->fecha_pago)->toDateString() ?? now()->toDateString());

    $esEdicion = isset($seguro) && $seguro;
@endphp

<div class="mt-5 grid gap-5 lg:grid-cols-2"
     x-data="seguroTicket(
        @js($tiposTicket),
        '{{ $tipoSeleccionado }}',
        '{{ $esEdicion ? 'pendiente' : $estadoSeleccionado }}',
        '{{ $fechaPagoSeleccionada }}'
     )">

    <div class="panel-card p-6">
        <form method="POST" action="{{ $action }}" class="grid gap-3">
            @csrf
            @if(($method ?? 'POST') !== 'POST')
                @method($method)
            @endif

            @if($esEdicion)
                <input type="hidden" name="estado" value="pendiente">
            @endif

            <div>
                <label class="text-sm panel-muted">Alumno</label>
                <select name="alumno_id" class="panel-input w-full mt-1 px-4 py-3">
                    <option value="">Selecciona un alumno</option>
                    @foreach($alumnos as $alumnoItem)
                        <option value="{{ $alumnoItem->id }}" @selected((string) $alumnoSeleccionadoId === (string) $alumnoItem->id)>
                            {{ $alumnoItem->apellidos }}, {{ $alumnoItem->nombre }}
                            @if($alumnoItem->dni)
                                · {{ $alumnoItem->dni }}
                            @endif
                            @if(!$alumnoItem->activo)
                                · (de baja)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm panel-muted">Tipo de seguro</label>
                <select name="tipo" class="panel-input w-full mt-1 px-4 py-3" x-model="tipo">
                    <option value="">Selecciona un seguro</option>
                    @foreach($tiposSeguro as $clave => $tipoSeguro)
                        <option value="{{ $clave }}" @selected($tipoSeleccionado === $clave)>
                            {{ $tipoSeguro['nombre'] }} ({{ number_format((float) $tipoSeguro['importe'], 2, ',', '.') }} €)
                        </option>
                    @endforeach
                </select>
            </div>

            @if(!$esEdicion)
                <div>
                    <label class="text-sm panel-muted">Estado inicial</label>
                    <select name="estado" class="panel-input w-full mt-1 px-4 py-3" x-model="estado">
                        <option value="pagado" @selected($estadoSeleccionado === 'pagado')>Pagado</option>
                        <option value="pendiente" @selected($estadoSeleccionado === 'pendiente')>Pendiente</option>
                    </select>
                </div>
            @endif

            <template x-if="estado === 'pagado'">
                <div class="grid gap-3 lg:grid-cols-2">
                    <div>
                        <label class="text-sm panel-muted">Fecha de pago</label>
                        <input
                            type="date"
                            name="fecha_pago"
                            value="{{ $fechaPagoSeleccionada }}"
                            class="panel-input w-full mt-1 px-4 py-3"
                            x-model="fechaPago"
                        >
                    </div>

                    <div>
                        <label class="text-sm panel-muted">Método</label>
                        <select name="metodo" class="panel-input w-full mt-1 px-4 py-3">
                            @foreach(['efectivo','bizum','tarjeta','transferencia','otro'] as $metodo)
                                <option value="{{ $metodo }}" @selected(old('metodo', optional($seguro)->metodo ?? 'efectivo') === $metodo)>
                                    {{ ucfirst($metodo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </template>

            <template x-if="estado === 'pagado'">
                <div>
                    <label class="text-sm panel-muted">Notas</label>
                    <textarea
                        name="notas"
                        rows="4"
                        class="panel-input w-full mt-1 px-4 py-3"
                        placeholder="Opcional"
                    >{{ old('notas', optional($seguro)->notas ?? '') }}</textarea>
                </div>
            </template>

            <div class="mt-2">
                <button class="panel-btn px-6 py-3" :disabled="!tipo">
                    {{ $submitLabel }}
                </button>
            </div>
        </form>
    </div>

    <div>
        <div class="panel-card p-6"
             style="background: radial-gradient(1200px 600px at 0% 0%, rgba(90,155,255,.10), transparent 60%);">
            <div class="text-xl font-semibold" style="color: rgb(145 190 255);" x-text="tituloTicket"></div>

            <div class="mt-2 text-sm panel-muted" x-text="descripcionEstado"></div>

            <div class="mt-5 flex items-center justify-between">
                <div class="panel-muted text-sm">
                    Inicio:
                    <span class="text-white" x-text="inicioFmt"></span>
                </div>
                <div class="panel-muted text-sm">
                    Fin:
                    <span class="text-white" x-text="finFmt"></span>
                </div>
            </div>

            <div class="mt-8 border-t panel-border pt-5 flex items-end justify-between">
                <div class="panel-muted uppercase tracking-wider">TOTAL</div>
                <div class="text-3xl font-bold" x-text="totalFmt"></div>
            </div>
        </div>
    </div>
</div>

<script>
function seguroTicket(tipos, tipoInicial, estadoInicial, fechaPagoInicial) {
    return {
        tipos: tipos,
        tipo: tipoInicial,
        estado: estadoInicial,
        fechaPago: fechaPagoInicial,

        get tipoSeleccionado() {
            return this.tipos.find(t => String(t.clave) === String(this.tipo)) || null;
        },

        get tituloTicket() {
            return this.tipoSeleccionado ? this.tipoSeleccionado.nombre : 'Selecciona un seguro deportivo';
        },

        get totalFmt() {
            if (!this.tipoSeleccionado) return '—';
            return this.formatoEuros(this.tipoSeleccionado.importe);
        },

        get descripcionEstado() {
            return this.estado === 'pendiente'
                ? 'Se asigna como pendiente. Después podrás cobrarlo, editarlo o eliminarlo.'
                : 'Pago único anual. Se registra por separado de las cuotas del alumno.';
        },

        get inicioFmt() {
            if (this.estado === 'pendiente') return 'Al cobrar';
            return this.formatoFecha(this.fechaPago);
        },

        get finFmt() {
            if (this.estado === 'pendiente') return 'Al cobrar';
            if (!this.fechaPago) return '—';
            return this.formatoFecha(this.finIso());
        },

        finIso() {
            if (!this.fechaPago) return '';

            const fecha = new Date(this.fechaPago + 'T12:00:00');
            fecha.setFullYear(fecha.getFullYear() + 1);
            fecha.setDate(fecha.getDate() - 1);

            return this.iso(fecha);
        },

        formatoFecha(iso) {
            if (!iso) return '—';
            const [y, m, d] = iso.split('-');
            return `${d}/${m}/${y}`;
        },

        formatoEuros(valor) {
            return new Intl.NumberFormat('es-ES', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(valor) + ' €';
        },

        iso(fecha) {
            const y = fecha.getFullYear();
            const m = String(fecha.getMonth() + 1).padStart(2, '0');
            const d = String(fecha.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },
    }
}
</script>