@php
    $modo = $modo ?? 'edit';
    $alumno = $alumno ?? null;

    $grupos = $grupos ?? collect();
    $tiposCuota = $tiposCuota ?? collect();

    $gruposSeleccionados = $gruposSeleccionados ?? [];
    $tipoSeleccionado = old('tipo_cuota_id');
    $cuotaEstado = old('cuota_estado', 'pagada');

    $fechaPago = old('fecha_pago', now()->format('Y-m-d'));
    $metodoPago = old('metodo_pago', 'efectivo');
@endphp

<div class="mb-6 rounded-2xl border panel-border p-4" style="background: rgb(255 255 255 / .03);">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <img
            src="{{ $alumno->foto_url ?? asset('img/alumno-default.svg') }}"
            alt="Foto del alumno"
            class="h-24 w-24 rounded-2xl object-cover border panel-border"
        >

        <div class="flex-1">
            <label class="text-sm font-medium">Foto</label>

            <input
                type="file"
                name="foto"
                accept=".jpg,.jpeg,.png,.webp,image/*"
                class="mt-2 w-full panel-input px-4 py-3"
            >

            <p class="mt-2 text-xs panel-muted">
                La imagen se reduce automáticamente y se guarda optimizada para no ocupar mucho.
            </p>

            @if(($alumno->foto_path ?? null))
                <label class="mt-3 inline-flex items-center gap-2 text-sm panel-muted">
                    <input type="checkbox" name="quitar_foto" value="1">
                    Quitar foto actual
                </label>
            @endif
        </div>
    </div>
</div>
<div class="space-y-4">
    {{-- DATOS (100% ancho) --}}
    <div class="panel-card p-6">
        <h2 class="text-lg font-semibold">Datos</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
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
    </div>

    {{-- DEBAJO: si es CREATE -> grid (Cuota izquierda, Grupos+Ticket derecha) --}}
    @if($modo === 'create')
        <div class="grid gap-4 lg:grid-cols-2">
            {{-- IZQUIERDA: CUOTA --}}
            <div class="space-y-4">
                <div class="panel-card p-6">
                    <h2 class="text-lg font-semibold">Cuota</h2>
                    <p class="mt-1 text-sm panel-muted">Opcional. Si la marcas como pagada, se registra el pago.</p>

                    <div class="mt-4">
                        <label class="text-sm font-medium">Tipo de cuota</label>
                        <select name="tipo_cuota_id" id="tipo_cuota_id" class="mt-1 w-full panel-input px-4 py-3">
                            <option value="">Selecciona un tipo</option>
                            @foreach($tiposCuota as $t)
                                <option value="{{ $t->id }}"
                                        data-nombre="{{ $t->nombre }}"
                                        data-importe="{{ (float)$t->importe }}"
                                        data-meses="{{ (int)($t->duracion_meses ?? $t->meses ?? 0) }}"
                                        data-dias="{{ (int)($t->duracion_dias ?? $t->dias ?? 0) }}"
                                        @selected((string)$tipoSeleccionado === (string)$t->id)>
                                    {{ $t->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="text-sm font-medium">Estado</label>
                        <select name="cuota_estado" id="cuota_estado" class="mt-1 w-full panel-input px-4 py-3">
                            <option value="pagada" @selected($cuotaEstado==='pagada')>Pagada</option>
                            <option value="pendiente" @selected($cuotaEstado==='pendiente')>Pendiente</option>
                        </select>
                    </div>

                    <div id="bloque_pago" class="mt-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="text-sm font-medium">Fecha de pago</label>
                                <input type="date" name="fecha_pago" id="fecha_pago"
                                       value="{{ $fechaPago }}"
                                       class="mt-1 w-full panel-input px-4 py-3">
                            </div>

                            <div>
                                <label class="text-sm font-medium">Método</label>
                                <select name="metodo_pago" id="metodo_pago" class="mt-1 w-full panel-input px-4 py-3">
                                    <option value="efectivo" @selected($metodoPago==='efectivo')>Efectivo</option>
                                    <option value="bizum" @selected($metodoPago==='bizum')>Bizum</option>
                                    <option value="tarjeta" @selected($metodoPago==='tarjeta')>Tarjeta</option>
                                    <option value="transferencia" @selected($metodoPago==='transferencia')>Transferencia</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-medium">Notas</label>
                                <input name="notas_pago"
                                       value="{{ old('notas_pago') }}"
                                       class="mt-1 w-full panel-input px-4 py-3"
                                       placeholder="Opcional">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DERECHA: GRUPOS (arriba) + TICKET (abajo) --}}
            <div class="space-y-4">
                <div class="panel-card p-6">
                    <h2 class="text-lg font-semibold">Grupos</h2>
                    <p class="mt-1 text-sm panel-muted">Marca los grupos en los que está actualmente.</p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse($grupos as $g)
                            @php
                                $checked = in_array((int)$g->id, array_map('intval', (array)$gruposSeleccionados), true);
                            @endphp
                            <label class="cursor-pointer">
                                <input type="checkbox" name="grupos[]" value="{{ $g->id }}" class="sr-only peer" @checked($checked)>
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm
                                             border panel-border bg-white/[0.02] panel-muted
                                             peer-checked:bg-white/10 peer-checked:text-white">
                                    {{ $g->nombre }}
                                </span>
                            </label>
                        @empty
                            <div class="text-sm panel-muted">No hay grupos.</div>
                        @endforelse
                    </div>
                </div>

                <div class="panel-card p-6"
                     style="background: radial-gradient(1200px 600px at 0% 0%, rgba(0,255,160,.06), transparent 60%);">
                    <div class="text-xl font-semibold" style="color: rgb(60 220 150);" id="ticket_nombre">
                        Selecciona un tipo de cuota
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="panel-muted text-sm">
                            Inicio: <span class="text-white" id="ticket_inicio">—</span>
                        </div>
                        <div class="panel-muted text-sm">
                            Fin: <span class="text-white" id="ticket_fin">—</span>
                        </div>
                    </div>

                    <div class="mt-6 border-t panel-border opacity-60"></div>

                    <div class="mt-5 flex items-end justify-between">
                        <div class="text-xs panel-muted uppercase tracking-wider">TOTAL</div>
                        <div class="text-3xl font-bold" id="ticket_total">—</div>
                    </div>
                </div>

                <div class="text-sm panel-muted">
                    El alumno tendrá acceso a las clases una vez registrado el pago.
                </div>

                @once
                    <script>
                        (function () {
                            const sel = document.getElementById('tipo_cuota_id');
                            const estado = document.getElementById('cuota_estado');
                            const bloquePago = document.getElementById('bloque_pago');
                            const fechaPago = document.getElementById('fecha_pago');

                            const ticketNombre = document.getElementById('ticket_nombre');
                            const ticketInicio = document.getElementById('ticket_inicio');
                            const ticketFin = document.getElementById('ticket_fin');
                            const ticketTotal = document.getElementById('ticket_total');

                            function fmt(d) {
                                const dd = String(d.getDate()).padStart(2, '0');
                                const mm = String(d.getMonth() + 1).padStart(2, '0');
                                const yy = d.getFullYear();
                                return dd + '/' + mm + '/' + yy;
                            }

                            function addMonthsNoOverflow(date, months) {
                                const d = new Date(date.getTime());
                                const day = d.getDate();
                                d.setDate(1);
                                d.setMonth(d.getMonth() + months);
                                const lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
                                d.setDate(Math.min(day, lastDay));
                                return d;
                            }

                            function refrescar() {
                                const tipoId = sel.value;
                                const est = estado.value;

                                bloquePago.style.display = (est === 'pagada') ? '' : 'none';

                                if (!tipoId) {
                                    ticketNombre.textContent = 'Selecciona un tipo de cuota';
                                    ticketInicio.textContent = '—';
                                    ticketFin.textContent = '—';
                                    ticketTotal.textContent = '—';
                                    return;
                                }

                                const opt = sel.options[sel.selectedIndex];
                                const nombre = opt.dataset.nombre || 'Cuota';
                                const importe = parseFloat(opt.dataset.importe || '0');
                                const meses = parseInt(opt.dataset.meses || '0');
                                const dias = parseInt(opt.dataset.dias || '0');

                                ticketNombre.textContent = nombre;
                                ticketTotal.textContent = importe.toFixed(2).replace('.', ',') + ' €';

                                if (est === 'pagada') {
                                    const base = new Date((fechaPago.value || new Date().toISOString().slice(0,10)) + 'T00:00:00');
                                    let fin = new Date(base.getTime());

                                    if (dias > 0) {
                                        fin.setDate(fin.getDate() + dias);
                                    } else {
                                        fin = addMonthsNoOverflow(fin, Math.max(1, meses));
                                    }

                                    ticketInicio.textContent = fmt(base);
                                    ticketFin.textContent = fmt(fin);
                                } else {
                                    ticketInicio.textContent = '—';
                                    ticketFin.textContent = '—';
                                }
                            }

                            sel.addEventListener('change', refrescar);
                            estado.addEventListener('change', refrescar);
                            if (fechaPago) fechaPago.addEventListener('change', refrescar);

                            refrescar();
                        })();
                    </script>
                @endonce
            </div>
        </div>
    @else
        {{-- EDIT: solo grupos debajo de datos (100% ancho) --}}
        <div class="panel-card p-6">
            <h2 class="text-lg font-semibold">Grupos</h2>
            <p class="mt-1 text-sm panel-muted">Marca los grupos en los que está actualmente.</p>

            <div class="mt-4 flex flex-wrap gap-2">
                @forelse($grupos as $g)
                    @php
                        $checked = in_array((int)$g->id, array_map('intval', (array)$gruposSeleccionados), true);
                    @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox" name="grupos[]" value="{{ $g->id }}" class="sr-only peer" @checked($checked)>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm
                                     border panel-border bg-white/[0.02] panel-muted
                                     peer-checked:bg-white/10 peer-checked:text-white">
                            {{ $g->nombre }}
                        </span>
                    </label>
                @empty
                    <div class="text-sm panel-muted">No hay grupos.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>