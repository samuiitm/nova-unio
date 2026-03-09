<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $rol = $request->query('rol', 'todos');
        $estado = $request->query('estado', 'todos');

        $query = User::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            });
        }

        if ($rol !== 'todos') {
            $query->where('rol', $rol);
        }

        if ($estado === 'activos') {
            $query->where('activo', 1);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', 0);
        }

        $usuarios = $query
            ->orderByRaw("CASE WHEN rol = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('rol', 'admin')->count(),
            'entrenadores' => User::where('rol', 'entrenador')->count(),
            'activos' => User::where('activo', 1)->count(),
        ];

        return view('panel.usuarios.index', compact('usuarios', 'q', 'rol', 'estado', 'stats'));
    }

    public function create()
    {
        return view('panel.usuarios.create');
    }

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();

        User::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? null,
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'rol' => $data['rol'],
            'activo' => $request->boolean('activo', true),
            'password_hash' => $data['password'],
        ]);

        return redirect()
            ->route('panel.usuarios.index')
            ->with('ok', 'Usuario creado correctamente.');
    }
}