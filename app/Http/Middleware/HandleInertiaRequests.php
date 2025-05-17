<?php
namespace App\Http\Middleware;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    // Sobrescribe el método handle para permitir vistas Blade
    public function handle($request, \Closure $next)
    {
        // Comprueba si la ruta actual debería usar Blade en lugar de Inertia
        $bladeRoutes = [
            '/perfumes', 
            '/perfumes/create',
            '/perfumes/{id}',
            '/perfumes/{id}/edit',
            '/usuarios',
            '/usuarios/create',
            '/usuarios/{id}',
            '/usuarios/{id}/edit'
        ];
        
        $currentPath = $request->getPathInfo();
        
        // Para rutas de recursos como /perfumes/1, /perfumes/1/edit
        foreach ($bladeRoutes as $route) {
            // Reemplazar cualquier parámetro {id} con un patrón regex
            $pattern = str_replace('{id}', '\d+', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if ($route === $currentPath || preg_match($pattern, $currentPath)) {
                // Si es una ruta Blade, pasa directamente al siguiente middleware
                return $next($request);
            }
        }
        
        // Si no es una ruta Blade, procesa normalmente con Inertia
        return parent::handle($request, $next);
    }

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => $request->cookie('sidebar_state') === 'true',
        ];
    }
}