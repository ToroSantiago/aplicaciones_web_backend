<?php

use App\Http\Controllers\SaludoController;
use App\Http\Controllers\PerfumeController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Inertia\Inertia;

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

// Rutas que usan Inertia específicamente
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    // Incluir settings.php que seguramente contiene rutas Inertia
    require __DIR__.'/settings.php';
});

// Rutas de autenticación de Breeze/Jetstream (si las usas)
// require __DIR__.'/auth.php';