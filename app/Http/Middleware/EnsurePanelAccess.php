<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->activo) {
            abort(403);
        }

        abort_unless(in_array($user->rol, ['admin', 'entrenador'], true), 403);

        return $next($request);
    }
}