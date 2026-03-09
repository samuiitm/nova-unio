<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Preinscripcion;
use Illuminate\Http\Request;

class PreinscripcionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todas');

        $query = Preinscripcion::query()->with('alumno');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('modalidad', 'like', "%{$q}%")
                    ->orWhere('nivel', 'like', "%{$q}%")
                    ->orWhere('objetivo', 'like', "%{$q}%")
                    ->orWhere('mensaje', 'like', "%{$q}%");
            });
        }

        if ($estado !== 'todas') {
            $query->where('estado', $estado);
        }

        $preinscripciones = $query
            ->orderByRaw("CASE WHEN estado = 'resuelta' THEN 1 ELSE 0 END")
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'nuevas' => Preinscripcion::where('estado', 'nueva')->count(),
            'en_proceso' => Preinscripcion::where('estado', 'en_proceso')->count(),
            'resueltas' => Preinscripcion::where('estado', 'resuelta')->count(),
            'total' => Preinscripcion::count(),
        ];

        return view('panel.preinscripciones.index', compact('preinscripciones', 'q', 'estado', 'stats'));
    }

    public function show(Preinscripcion $preinscripcion)
    {
        $preinscripcion->load('alumno');

        return view('panel.preinscripciones.show', compact('preinscripcion'));
    }

    public function convertir(Preinscripcion $preinscripcion)
    {
        if ($preinscripcion->estado === 'resuelta' && $preinscripcion->alumno_id) {
            return redirect()
                ->route('panel.alumnos.show', $preinscripcion->alumno_id)
                ->with('ok', 'Esta preinscripción ya se convirtió en alumno.');
        }

        return redirect()->route('panel.alumnos.create', [
            'preinscripcion' => $preinscripcion->id,
        ]);
    }
}