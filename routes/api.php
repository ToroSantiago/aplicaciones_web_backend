<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PerfumeApiController;

    // Rutas específicas PRIMERO (antes que las rutas con parámetros)
    Route::get('/all', [PerfumeApiController::class, 'all']); // Lista todos
    Route::get('/paginated', [PerfumeApiController::class, 'paginated']); // Con paginación
    Route::get('/genero/{genero}', [PerfumeApiController::class, 'byGenero']); // Filtrar por género
    
    // Rutas base y con parámetros AL FINAL
    Route::get('/', [PerfumeApiController::class, 'index']); // Lista 5 perfumes
    Route::get('/{id}', [PerfumeApiController::class, 'show']); // Ver un perfume
    //Route::post('/', [PerfumeApiController::class, 'store']); // Crear perfume
    //Route::put('/{id}', [PerfumeApiController::class, 'update']); // Actualizar perfume
    //Route::delete('/{id}', [PerfumeApiController::class, 'destroy']); // Eliminar perfume
