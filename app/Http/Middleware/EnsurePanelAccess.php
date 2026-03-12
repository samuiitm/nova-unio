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

        abort_unless(
            $user->esAdmin() || $user->esEntrenadorAdmin() || $user->esEntrenador(),
            403
        );

        return $next($request);
    }
}