<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            abort(403);
        }

        $role = $user->role;
        $roleValue = is_object($role) && property_exists($role, 'value') ? $role->value : $role;

        abort_unless(in_array($roleValue, ['admin', 'entrenador'], true), 403);

        return $next($request);
    }
}