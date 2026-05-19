<?php

use App\Http\Controllers\DescuentoController;
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

    // Callbacks de MercadoPago: públicos (los invoca MP, no un usuario logueado).
    Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
    Route::get('/mercadopago/failed', [MercadoPagoController::class, 'failed'])->name('mercadopago.failed');

    /*
    |--------------------------------------------------------------------------
    | Backoffice
    |--------------------------------------------------------------------------
    | Acceso para Empleados y Administradores (los Cliente quedan bloqueados
    | en el login y por el middleware 'backoffice').
    |
    | - Lectura (index/show, estadísticas) → 'backoffice'
    | - Escritura (create/store/edit/update/destroy, cambios de estado, ABM
    |   de usuarios) → 'admin'
    */
    Route::middleware(['auth', 'backoffice'])->group(function () {
        // Perfumes: lectura
        Route::get('perfumes', [PerfumeController::class, 'index'])->name('perfumes.index');
        Route::get('perfumes/{perfume}', [PerfumeController::class, 'show'])
            ->whereNumber('perfume')
            ->name('perfumes.show');

        // Ventas: lectura + estadísticas
        Route::get('ventas/estadisticas', [VentaController::class, 'estadisticas'])
            ->name('ventas.estadisticas');
        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/{venta}', [VentaController::class, 'show'])
            ->whereNumber('venta')
            ->name('ventas.show');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        // Perfumes: escritura
        Route::get('perfumes/create', [PerfumeController::class, 'create'])->name('perfumes.create');
        Route::post('perfumes', [PerfumeController::class, 'store'])->name('perfumes.store');
        Route::get('perfumes/{perfume}/edit', [PerfumeController::class, 'edit'])->name('perfumes.edit');
        Route::put('perfumes/{perfume}', [PerfumeController::class, 'update'])->name('perfumes.update');
        Route::patch('perfumes/{perfume}', [PerfumeController::class, 'update']);
        Route::delete('perfumes/{perfume}', [PerfumeController::class, 'destroy'])->name('perfumes.destroy');

        // Ventas: cambios de estado
        Route::patch('ventas/{venta}/status', [VentaController::class, 'updateStatus'])
            ->name('ventas.updateStatus');

        // ABM de usuarios (incluye asignar/quitar el rol Administrador)
        Route::resource('usuarios', UsuarioController::class);

        // ABM de descuentos por variante (campañas con % y vigencia)
        Route::resource('descuentos', DescuentoController::class);

        // Estadísticas (placeholder — el módulo real está en VentaController::estadisticas)
        Route::resource('estadisticas', EstadisticasController::class);
    });

    // Rutas protegidas por autenticación e Inertia
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('dashboard');
        })->name('dashboard');

        require __DIR__.'/settings.php';
    }); 
});