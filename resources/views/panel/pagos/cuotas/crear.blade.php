@extends('layouts.panel')

@section('title', 'Asignar cuota | Nova Unió')

@section('content')
@php
    $tiposJson = $tipos->map(fn($t) => [
        'id' => $t->id,
        'nombre' => $t->nombre,
        'importe' => (float) $t->importe,
        'tipo_vigencia' => $t->tipo_vigencia ?? 'meses',
        'duracion_meses' => (int) $t->duracion_meses,
        'venta_inicio_mes' => $t->venta_inicio_mes,
        'venta_fin_mes' => $t->venta_fin_mes,
    ])->values();
@endphp

<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Asignar cuota</h1>
        <p class="mt-1 panel-muted">{{ $alumno->nombre }} {{ $alumno->apellidos }}</p>
    </div>

    <a href="{{ route('panel.alumnos.show', $alumno) }}" class="panel-icon-btn px-5 py-3">Volver</a>
</div>

@if($errors->any())
    <div class="mt-5 panel-card p-4">
        <div class="text-sm font-medium">Hay errores:</div>
        <ul class="mt-2 text-sm panel-muted list-disc pl-5">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-5 grid gap-5 lg:grid-cols-2"
     x-data="cuotaTicket(@js($tiposJson), '{{ old('tipo_cuota_id', '') }}', '{{ old('estado','pagada') }}', '{{ old('fecha_pago', $fechaPagoSugerida) }}')">

    <div class="panel-card p-6">
        <form method="POST" action="{{ route('panel.pagos.cuotas.store', $alumno) }}" class="grid gap-3">
            @csrf
            <div>
                <label class="text-sm panel-muted">Tipo de cuota</label>
                <select name="tipo_cuota_id" class="panel-input w-full mt-1 px-4 py-3" x-model="tipoId" @change="recalcular()">
                    <option value="">Selecciona un tipo</option>
                    <template x-for="t in tipos" :key="t.id">
                        <option :value="t.id" x-text="textoTipo(t)"></option>
                    </template>
                </select>
                <div class="mt-2 text-sm text-amber-300" x-show="mensajeDisponibilidad" x-text="mensajeDisponibilidad"></div>
            </div>

            <div>
                <label class="text-sm panel-muted">Estado</label>
                <select name="estado" class="panel-input w-full mt-1 px-4 py-3" x-model="estado" @change="recalcular()">
                    <option value="pagada">Pagada</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>

            <div x-show="estado === 'pagada'" class="grid gap-3 lg:grid-cols-3">
                <div>
                    <label class="text-sm panel-muted">Fecha de pago</label>
                    <input type="date" name="fecha_pago" class="panel-input w-full mt-1 px-4 py-3"
                           x-model="fechaPago" @change="recalcular()">
                </div>

                <div>
                    <label class="text-sm panel-muted">Método</label>
                    <select name="metodo" class="panel-input w-full mt-1 px-4 py-3">
                        @foreach(['efectivo','bizum','tarjeta','transferencia','otro'] as $m)
                            <option value="{{ $m }}" @selected(old('metodo','efectivo')===$m)>{{ ucfirst($m) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm panel-muted">Mes pagado</label>
                    <input
                        name="notas"
                        value="{{ old('notas') }}"
                        class="panel-input w-full mt-1 px-4 py-3"
                        placeholder="Ej. NOV 2026"
                        :required="estado === 'pagada'"
                    >
                    <p class="mt-1 text-xs panel-muted">Indica el mes o periodo pagado, por ejemplo: NOV 2026.</p>
                </div>
            </div>

            <div class="mt-2">
                <button class="panel-btn px-6 py-3" :disabled="!tipoSeleccionado || !puedeGuardarse">
                    Guardar
                </button>
            </div>
        </form>
    </div>

    <div>
        <div class="panel-card p-6" style="background: radial-gradient(1200px 600px at 0% 0%, rgba(0,255,160,.06), transparent 60%);">
            <div class="text-xl font-semibold" style="color: rgb(60 220 150);" x-text="tituloTicket"></div>

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

        <div class="mt-3 panel-muted text-sm">
            El alumno tendrá acceso a las clases una vez registrado el pago.
        </div>
    </div>
</div>

<script>
function cuotaTicket(tipos, tipoIdInicial, estadoInicial, fechaPagoInicial) {
    return {
        tipos: tipos,
        tipoId: tipoIdInicial,
        estado: estadoInicial,
        fechaPago: fechaPagoInicial,

        get tipoSeleccionado() {
            return this.tipos.find(t => String(t.id) === String(this.tipoId)) || null;
        },

        get tituloTicket() {
            return this.tipoSeleccionado ? this.tipoSeleccionado.nombre : 'Selecciona un tipo de cuota';
        },

        get mensajeDisponibilidad() {
            if (!this.tipoSeleccionado || this.tipoSeleccionado.tipo_vigencia !== 'temporada') {
                return '';
            }

            if (this.puedeGuardarse) {
                return '';
            }

            return 'La cuota de temporada solo se puede vender ' + this.textoVentana(this.tipoSeleccionado) + '.';
        },

        get puedeGuardarse() {
            if (!this.tipoSeleccionado) return false;
            if (this.tipoSeleccionado.tipo_vigencia !== 'temporada') return true;

            const fecha = this.estado === 'pagada' ? this.fechaPago : this.hoyIso();
            return this.estaEnVentanaVenta(this.tipoSeleccionado, fecha);
        },

        get inicioIso() {
            if (!this.tipoSeleccionado) return '';
            if (this.estado !== 'pagada') return '';
            if (!this.fechaPago || !this.puedeGuardarse) return '';

            if (this.tipoSeleccionado.tipo_vigencia === 'indefinida') {
                return this.fechaPago;
            }

            if (this.tipoSeleccionado.tipo_vigencia !== 'temporada') {
                return this.fechaPago;
            }

            const rango = this.rangoTemporada(this.fechaPago);
            return this.fechaPago < rango.inicio ? rango.inicio : this.fechaPago;
        },

        get finIso() {
            if (!this.tipoSeleccionado) return '';
            if (this.estado !== 'pagada') return '';
            if (!this.fechaPago || !this.puedeGuardarse) return '';

            if (this.tipoSeleccionado.tipo_vigencia === 'indefinida') {
                return '';
            }

            if (this.tipoSeleccionado.tipo_vigencia !== 'temporada') {
                return this.addMonthsNoOverflow(this.fechaPago, this.tipoSeleccionado.duracion_meses);
            }

            return this.rangoTemporada(this.fechaPago).fin;
        },

        get inicioFmt() {
            if (!this.tipoSeleccionado) return '—';
            if (this.estado !== 'pagada') return 'Al cobrar';
            return this.formatoFecha(this.inicioIso);
        },

        get finFmt() {
            if (!this.tipoSeleccionado) return '—';
            if (this.estado !== 'pagada') return 'Al cobrar';

            if (this.tipoSeleccionado.tipo_vigencia === 'indefinida') {
                return 'Sin vencimiento';
            }

            return this.formatoFecha(this.finIso);
        },

        get totalFmt() {
            if (!this.tipoSeleccionado) return '—';
            return this.formatoEuros(this.tipoSeleccionado.importe);
        },

        recalcular() {},

        textoTipo(t) {
            if (t.tipo_vigencia === 'temporada') {
                return `${t.nombre} (${this.formatoEuros(t.importe)} · temporada · venta ${this.textoVentana(t)})`;
            }

            if (t.tipo_vigencia === 'indefinida') {
                return `${t.nombre} (${this.formatoEuros(t.importe)} · indefinida)`;
            }

            return `${t.nombre} (${this.formatoEuros(t.importe)} · ${t.duracion_meses} mes/es)`;
        },

        textoVentana(t) {
            return this.nombreMes(t.venta_inicio_mes || 8) + ' - ' + this.nombreMes(t.venta_fin_mes || 12);
        },

        estaEnVentanaVenta(t, iso) {
            const mes = Number((iso || this.hoyIso()).split('-')[1]);
            const inicio = Number(t.venta_inicio_mes || 8);
            const fin = Number(t.venta_fin_mes || 12);

            if (inicio <= fin) {
                return mes >= inicio && mes <= fin;
            }

            return mes >= inicio || mes <= fin;
        },

        rangoTemporada(iso) {
            const [y, m] = iso.split('-').map(Number);

            if (m >= 9) {
                return { inicio: `${y}-09-01`, fin: `${y + 1}-06-30` };
            }

            if (m <= 6) {
                return { inicio: `${y - 1}-09-01`, fin: `${y}-06-30` };
            }

            return { inicio: `${y}-09-01`, fin: `${y + 1}-06-30` };
        },

        hoyIso() {
            return new Date().toISOString().slice(0, 10);
        },

        nombreMes(numero) {
            return ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][numero - 1] || 'mes';
        },

        formatoEuros(n) {
            if (n === null || n === undefined) return '—';
            return (Number(n).toFixed(2)).replace('.', ',') + ' €';
        },

        formatoFecha(iso) {
            if (!iso) return '—';
            const [y, m, d] = iso.split('-');
            return `${d}/${m}/${y}`;
        },

        addMonthsNoOverflow(iso, months) {
            const [y, m, d] = iso.split('-').map(Number);
            const base = new Date(y, m - 1, d);
            const originalDay = base.getDate();

            const res = new Date(base);
            res.setMonth(res.getMonth() + Number(months));

            if (res.getDate() !== originalDay) {
                res.setDate(0);
            }

            const yy = res.getFullYear();
            const mm = String(res.getMonth() + 1).padStart(2, '0');
            const dd = String(res.getDate()).padStart(2, '0');
            return `${yy}-${mm}-${dd}`;
        },
    };
}
</script>
@endsection