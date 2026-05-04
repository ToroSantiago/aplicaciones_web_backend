<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite el acceso solo a usuarios con rol 'Empleado' o 'Administrador'.
 * Bloquea explícitamente a los 'Cliente' (que se crean desde el SPA y
 * no tienen nada que hacer en el backoffice).
 *
 * Asume que ya pasó por el middleware 'auth'.
 */
class EnsureUserCanAccessBackoffice
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'canAccessBackoffice') || ! $user->canAccessBackoffice()) {
            abort(403, 'Esta cuenta no tiene permisos para acceder al backoffice.');
        }

        return $next($request);
    }
}
