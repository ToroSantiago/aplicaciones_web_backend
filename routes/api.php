<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PerfumeApiController;

// Rutas específicas PRIMERO
Route::get('/all', [PerfumeApiController::class, 'all']); // Lista todos
Route::get('/paginated', [PerfumeApiController::class, 'paginated']); // Con paginación
Route::get('/genero/{genero}', [PerfumeApiController::class, 'byGenero']); // Filtrar por género

// Rutas para variantes específicas
Route::get('/{perfume}/variantes/{variante}', [PerfumeApiController::class, 'showVariante']);
Route::patch('/{perfume}/variantes/{variante}/stock', [PerfumeApiController::class, 'updateStock']);

// Ruta de compra (importante que esté antes y sin conflictos)
Route::post('/compra', [PerfumeApiController::class, 'compra']); // Comprar perfumes

// Rutas base CRUD
Route::get('/', [PerfumeApiController::class, 'index']); // Lista 5 perfumes
Route::get('/{id}', [PerfumeApiController::class, 'show'])->where('id', '[0-9]+'); // Ver un perfume
Route::post('/', [PerfumeApiController::class, 'store']); // Crear perfume
Route::put('/{id}', [PerfumeApiController::class, 'update'])->where('id', '[0-9]+'); // Actualizar perfume
Route::delete('/{id}', [PerfumeApiController::class, 'destroy'])->where('id', '[0-9]+'); // Eliminar perfume