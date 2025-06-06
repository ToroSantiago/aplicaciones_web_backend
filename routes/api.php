<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PerfumeApiController;


// Rutas para la API de perfumes
Route::prefix('perfumes')->group(function () {
    Route::get('/', [PerfumeApiController::class, 'index']); // Listar perfumes (max 5)
    Route::get('/paginated', [PerfumeApiController::class, 'paginated']); // Con paginación
    Route::get('/genero/{genero}', [PerfumeApiController::class, 'byGenero']); // Filtrar por género
    Route::get('/{id}', [PerfumeApiController::class, 'show']); // Ver un perfume específico
});