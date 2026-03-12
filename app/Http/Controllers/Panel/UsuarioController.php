<?php

namespace App\Http\Controllers\Panel;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            ->orderByRaw("
                CASE rol
                    WHEN 'admin' THEN 0
                    WHEN 'entrenador_admin' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('rol', RolUsuario::Admin->value)->count(),
            'entrenadores_admin' => User::where('rol', RolUsuario::EntrenadorAdmin->value)->count(),
            'entrenadores' => User::where('rol', RolUsuario::Entrenador->value)->count(),
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

    public function edit(User $usuario)
    {
        return view('panel.usuarios.edit', compact('usuario'));
    }

    public function update(UpdateUsuarioRequest $request, User $usuario)
    {
        $data = $request->validated();

        $payload = [
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? null,
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'rol' => $data['rol'],
            'activo' => $request->boolean('activo', false),
        ];

        if (!empty($data['password'])) {
            $payload['password_hash'] = $data['password'];
        }

        $this->protegerAdminUnico($usuario, $payload);

        $usuario->update($payload);

        return redirect()
            ->route('panel.usuarios.index')
            ->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ((int) $request->user()->id === (int) $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $this->protegerEliminacion($usuario);

        $usuario->delete();

        return redirect()
            ->route('panel.usuarios.index')
            ->with('ok', 'Usuario eliminado correctamente.');
    }

    private function protegerAdminUnico(User $usuario, array $payload): void
    {
        $rolDestino = $payload['rol'] ?? ($usuario->rolEnum()?->value ?? RolUsuario::Entrenador->value);
        $activoDestino = array_key_exists('activo', $payload) ? (bool) $payload['activo'] : (bool) $usuario->activo;

        $adminsActivos = User::query()
            ->where('rol', RolUsuario::Admin->value)
            ->where('activo', 1)
            ->count();

        if (
            $usuario->esAdmin()
            && $usuario->activo
            && $adminsActivos <= 1
            && ($rolDestino !== RolUsuario::Admin->value || !$activoDestino)
        ) {
            throw ValidationException::withMessages([
                'rol' => 'No puedes dejar el sistema sin un admin activo.',
            ]);
        }
    }

    private function protegerEliminacion(User $usuario): void
    {
        $adminsActivos = User::query()
            ->where('rol', RolUsuario::Admin->value)
            ->where('activo', 1)
            ->count();

        if ($usuario->esAdmin() && $usuario->activo && $adminsActivos <= 1) {
            throw ValidationException::withMessages([
                'usuario' => 'No puedes eliminar al último admin activo.',
            ]);
        }
    }
}