<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlumnoRequest;
use App\Http\Requests\UpdateAlumnoRequest;
use App\Models\Alumno;
use App\Models\Cuota;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\Preinscripcion;
use App\Models\TipoCuota;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos'); // todos | activos | inactivos
        $orden = $request->query('orden', 'reciente'); // reciente | nombre

        $query = Alumno::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('catsalut', 'like', "%{$q}%")
                    ->orWhere('poblacion', 'like', "%{$q}%");
            });
        }

        if ($estado === 'activos') {
            $query->where('activo', 1);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', 0);
        }

        if ($orden === 'nombre') {
            $query->orderByDesc('activo')
                ->orderBy('apellidos')
                ->orderBy('nombre');
        } else {
            $query->orderByDesc('activo')
                ->orderByDesc('created_at');
        }

        $alumnos = $query->paginate(10)->withQueryString();

        $nuevosMes = Alumno::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return view('panel.alumnos.index', compact('alumnos', 'q', 'estado', 'orden', 'nuevosMes'));
    }

    public function create(Request $request)
    {
        $grupos = Grupo::where('activo', 1)->orderBy('nombre')->get();
        $tiposCuota = TipoCuota::where('activo', 1)->orderBy('nombre')->get();

        $preinscripcion = null;
        $alumno = null;

        if ($request->filled('preinscripcion')) {
            $preinscripcion = Preinscripcion::findOrFail($request->integer('preinscripcion'));

            if ($preinscripcion->estado === 'resuelta' && $preinscripcion->alumno_id) {
                return redirect()
                    ->route('panel.alumnos.show', $preinscripcion->alumno_id)
                    ->with('ok', 'Esta preinscripción ya está resuelta y vinculada a un alumno.');
            }

            $alumno = new Alumno([
                'nombre' => $preinscripcion->nombre,
                'apellidos' => $preinscripcion->apellidos,
                'email' => $preinscripcion->email,
                'telefono' => $preinscripcion->telefono,
            ]);
        }

        return view('panel.alumnos.create', compact('grupos', 'tiposCuota', 'preinscripcion', 'alumno'));
    }

    public function store(StoreAlumnoRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto_path'] = $this->guardarFotoOptimizada($request->file('foto'));
        }

        unset($data['foto'], $data['quitar_foto']);

        $grupoIds = collect($data['grupos'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $preinscripcionId = $data['preinscripcion_id'] ?? null;
        $tipoCuotaId = $data['tipo_cuota_id'] ?? null;
        $cuotaEstado = $data['cuota_estado'] ?? null;
        $fechaPago = $data['fecha_pago'] ?? null;
        $metodoPago = $data['metodo_pago'] ?? null;
        $notasPago = $data['notas_pago'] ?? null;

        unset(
            $data['grupos'],
            $data['preinscripcion_id'],
            $data['tipo_cuota_id'],
            $data['cuota_estado'],
            $data['fecha_pago'],
            $data['metodo_pago'],
            $data['notas_pago']
        );

        $data['activo'] = true;
        $data['fecha_baja'] = null;
        $data['fecha_inicio_actividad'] = null;

        $alumno = DB::transaction(function () use (
            $data,
            $grupoIds,
            $preinscripcionId,
            $tipoCuotaId,
            $cuotaEstado,
            $fechaPago,
            $metodoPago,
            $notasPago
        ) {
            $preinscripcion = null;

            if ($preinscripcionId) {
                $preinscripcion = Preinscripcion::lockForUpdate()->findOrFail($preinscripcionId);

                if ($preinscripcion->estado === 'resuelta' && $preinscripcion->alumno_id) {
                    throw ValidationException::withMessages([
                        'preinscripcion_id' => 'Esta preinscripción ya fue convertida en alumno.',
                    ]);
                }
            }

            $alumno = Alumno::create($data);

            $this->sincronizarGrupos($alumno, $grupoIds);

            if ($tipoCuotaId && $cuotaEstado) {
                $this->crearCuotaInicial(
                    alumno: $alumno,
                    tipoCuotaId: (int) $tipoCuotaId,
                    estado: $cuotaEstado,
                    fechaPago: $fechaPago,
                    metodoPago: $metodoPago,
                    notasPago: $notasPago,
                );
            }

            if ($preinscripcion) {
                $preinscripcion->update([
                    'estado' => 'resuelta',
                    'alumno_id' => $alumno->id,
                    'resuelta_at' => now(),
                ]);
            }

            return $alumno;
        });

        return redirect()
            ->route('panel.alumnos.show', $alumno)
            ->with('ok', 'Alumno creado correctamente.');
    }

    public function show(Alumno $alumno)
    {
        $hoy = now()->toDateString();

        $gruposActivos = $alumno->gruposActivos()->orderBy('nombre')->get();

        $cuotaVigente = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', $hoy);
            })
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id')
            ->first();

        $cuotaPendiente = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pendiente')
            ->with(['tipoCuota'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        $ultimaPagada = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->where('estado', 'pagada')
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('fecha_fin')
            ->orderByDesc('id')
            ->first();

        $estadoCuota = 'sin_cuota';

        if ($cuotaVigente) {
            $estadoCuota = 'vigente';
        } elseif ($cuotaPendiente) {
            $estadoCuota = 'pendiente';
        } elseif ($ultimaPagada && $ultimaPagada->fecha_fin && $ultimaPagada->fecha_fin->toDateString() < $hoy) {
            $estadoCuota = 'vencida';
        }

        $cuotas = Cuota::query()
            ->where('alumno_id', $alumno->id)
            ->with(['tipoCuota', 'pago'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $pagos = Pago::query()
            ->where('alumno_id', $alumno->id)
            ->with(['cuota.tipoCuota'])
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->get();

        return view('panel.alumnos.show', compact(
            'alumno',
            'gruposActivos',
            'estadoCuota',
            'cuotaVigente',
            'cuotaPendiente',
            'ultimaPagada',
            'cuotas',
            'pagos'
        ));
    }

    public function edit(Alumno $alumno)
    {
        $grupos = Grupo::where('activo', 1)->orderBy('nombre')->get();

        $gruposSeleccionados = $alumno->grupos()
            ->wherePivotNull('fecha_baja')
            ->pluck('grupos.id')
            ->map(fn ($v) => (int) $v)
            ->all();

        return view('panel.alumnos.edit', compact('alumno', 'grupos', 'gruposSeleccionados'));
    }

    public function update(UpdateAlumnoRequest $request, Alumno $alumno)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto_path'] = $this->guardarFotoOptimizada(
                $request->file('foto'),
                $alumno->foto_path
            );
        } elseif ($request->boolean('quitar_foto') && $alumno->foto_path) {
            Storage::disk('public')->delete($alumno->foto_path);
            $data['foto_path'] = null;
        }

        unset($data['foto'], $data['quitar_foto']);

        $alumno->update($data);

        return redirect()
            ->route('panel.alumnos.show', $alumno)
            ->with('ok', 'Alumno actualizado.');
    }

    public function baja(Alumno $alumno)
    {
        $alumno->update([
            'activo' => false,
            'fecha_baja' => now()->toDateString(),
        ]);

        return back()->with('ok', 'Alumno dado de baja.');
    }

    public function activar(Alumno $alumno)
    {
        $alumno->update([
            'activo' => true,
            'fecha_baja' => null,
        ]);

        return back()->with('ok', 'Alumno activado.');
    }

    private function sincronizarGrupos(Alumno $alumno, array $grupoIds): void
    {
        $ahora = now();
        $hoy = $ahora->toDateString();

        $grupoIds = collect($grupoIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $gruposActivosActuales = DB::table('alumno_grupo')
            ->where('alumno_id', $alumno->id)
            ->whereNull('fecha_baja')
            ->pluck('grupo_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $gruposParaAlta = array_values(array_diff($grupoIds, $gruposActivosActuales));
        $gruposParaBaja = array_values(array_diff($gruposActivosActuales, $grupoIds));

        foreach ($gruposParaAlta as $grupoId) {
            $alumno->grupos()->attach($grupoId, [
                'fecha_alta' => $hoy,
                'fecha_baja' => null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        if (!empty($gruposParaBaja)) {
            DB::table('alumno_grupo')
                ->where('alumno_id', $alumno->id)
                ->whereIn('grupo_id', $gruposParaBaja)
                ->whereNull('fecha_baja')
                ->update([
                    'fecha_baja' => $hoy,
                    'updated_at' => $ahora,
                ]);
        }
    }

    private function crearCuotaInicial(
        Alumno $alumno,
        int $tipoCuotaId,
        string $estado,
        ?string $fechaPago,
        ?string $metodoPago,
        ?string $notasPago
    ): void {
        $tipo = TipoCuota::findOrFail($tipoCuotaId);

        if ($estado === 'pendiente') {
            Cuota::create([
                'alumno_id' => $alumno->id,
                'tipo_cuota_id' => $tipo->id,
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->toDateString(),
                'importe' => $tipo->importe,
                'estado' => 'pendiente',
            ]);

            return;
        }

        $fecha = Carbon::parse($fechaPago ?: now()->toDateString());
        $inicio = $fecha->copy();
        $fin = $fecha->copy()->addMonthsNoOverflow(max(1, (int) ($tipo->duracion_meses ?? 1)));

        $cuota = Cuota::create([
            'alumno_id' => $alumno->id,
            'tipo_cuota_id' => $tipo->id,
            'fecha_inicio' => $inicio->toDateString(),
            'fecha_fin' => $fin->toDateString(),
            'importe' => $tipo->importe,
            'estado' => 'pagada',
        ]);

        Pago::create([
            'cuota_id' => $cuota->id,
            'alumno_id' => $alumno->id,
            'fecha_pago' => $fecha->toDateString(),
            'importe' => $tipo->importe,
            'metodo' => $metodoPago ?: 'efectivo',
            'notas' => $notasPago,

            'tipo_cuota_id' => $tipo->id,
            'tipo_cuota_nombre' => $tipo->nombre,
            'vigencia_inicio' => $inicio->toDateString(),
            'vigencia_fin' => $fin->toDateString(),
        ]);
    }

    private function guardarFotoOptimizada(UploadedFile $file, ?string $fotoAnterior = null): ?string
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('La extensión GD no está activa en PHP.');
        }

        $binario = file_get_contents($file->getRealPath());

        if ($binario === false) {
            throw new \RuntimeException('No se ha podido leer la imagen subida.');
        }

        $imagenOrigen = imagecreatefromstring($binario);

        if (!$imagenOrigen) {
            throw new \RuntimeException('La imagen subida no tiene un formato válido.');
        }

        $anchoOrigen = imagesx($imagenOrigen);
        $altoOrigen = imagesy($imagenOrigen);

        $maxLado = 480;
        $escala = min($maxLado / max($anchoOrigen, $altoOrigen), 1);

        $anchoDestino = max(1, (int) round($anchoOrigen * $escala));
        $altoDestino = max(1, (int) round($altoOrigen * $escala));

        $imagenDestino = imagecreatetruecolor($anchoDestino, $altoDestino);

        imagealphablending($imagenDestino, true);
        imagesavealpha($imagenDestino, true);

        $transparente = imagecolorallocatealpha($imagenDestino, 0, 0, 0, 127);
        imagefill($imagenDestino, 0, 0, $transparente);

        imagecopyresampled(
            $imagenDestino,
            $imagenOrigen,
            0,
            0,
            0,
            0,
            $anchoDestino,
            $altoDestino,
            $anchoOrigen,
            $altoOrigen
        );

        $carpeta = 'alumnos/' . now()->format('Y/m');
        $nombreBase = Str::uuid()->toString();

        if (function_exists('imagewebp')) {
            $ruta = $carpeta . '/' . $nombreBase . '.webp';

            ob_start();
            imagewebp($imagenDestino, null, 78);
            $contenido = ob_get_clean();
        } else {
            $ruta = $carpeta . '/' . $nombreBase . '.jpg';

            ob_start();
            imagejpeg($imagenDestino, null, 78);
            $contenido = ob_get_clean();
        }

        imagedestroy($imagenOrigen);
        imagedestroy($imagenDestino);

        if ($contenido === false || $contenido === null) {
            throw new \RuntimeException('No se ha podido generar la imagen optimizada.');
        }

        Storage::disk('public')->put($ruta, $contenido);

        if ($fotoAnterior && Storage::disk('public')->exists($fotoAnterior)) {
            Storage::disk('public')->delete($fotoAnterior);
        }

        return $ruta;
    }
}