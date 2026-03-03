<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        $role = $request->user()?->role;
        abort_unless(in_array($role, ['admin', 'entrenador'], true), 403);

        return $next($request);
    }
}