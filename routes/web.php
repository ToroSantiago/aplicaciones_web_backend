<?php

use App\Http\Controllers\PerfumeController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Inertia\Inertia;
use App\Http\Controllers\EstadisticasController;

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

    // Rutas protegidas por autenticación e Inertia
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('dashboard');
        })->name('dashboard');

        require __DIR__.'/settings.php';
    }); 
});