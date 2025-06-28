<?php

use App\Http\Controllers\PerfumeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Inertia\Inertia;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\MercadoPagoController;


Route::middleware('web')->group(function () {

    // Ruta raíz
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');

    // Rutas de autenticación con Blade
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rutas de recursos con Blade
    Route::resource('perfumes', PerfumeController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('estadisticas', EstadisticasController::class);

    Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
    Route::get('/mercadopago/failed', [MercadoPagoController::class, 'failed'])->name('mercadopago.failed');

    // NUEVAS RUTAS DE VENTAS
    // IMPORTANTE: La ruta de estadísticas debe ir ANTES del resource
    Route::get('ventas/estadisticas', [VentaController::class, 'estadisticas'])->name('ventas.estadisticas');
    Route::resource('ventas', VentaController::class)->only(['index', 'show']);
    Route::patch('ventas/{venta}/status', [VentaController::class, 'updateStatus'])->name('ventas.updateStatus');

    // Rutas protegidas por autenticación e Inertia
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('dashboard');
        })->name('dashboard');

        require __DIR__.'/settings.php';
    }); 
});