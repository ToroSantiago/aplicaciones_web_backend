<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea el acceso a usuarios que no tengan rol 'Administrador'.
 *
 * Asume que ya pasó por el middleware 'auth' (la ruta debe combinarlos:
 * `->middleware(['auth', 'admin'])`).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isAdmin') || ! $user->isAdmin()) {
            abort(403, 'Solo los administradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
