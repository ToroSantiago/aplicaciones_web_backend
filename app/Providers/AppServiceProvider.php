<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // En producción (Vercel) terminamos SSL en el proxy y la request
        // llega al runtime PHP como HTTP. Si no forzamos el esquema acá,
        // route()/url() generan links http:// y el navegador los marca
        // como "formulario no seguro" cuando la página está en https://.
        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}
