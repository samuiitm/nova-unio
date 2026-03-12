<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePanelManagement
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_active || !$user->puedeGestionarClub()) {
            abort(403);
        }

        return $next($request);
    }
}