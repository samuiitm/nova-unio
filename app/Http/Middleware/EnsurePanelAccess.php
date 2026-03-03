<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\RolUsuario;

class EnsurePanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            abort(403);
        }

        $role = $user->role instanceof RolUsuario ? $user->role->value : $user->role;

        abort_unless(in_array($role, ['admin', 'entrenador'], true), 403);

        return $next($request);
    }
}