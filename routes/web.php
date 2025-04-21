<?php

use App\Http\Controllers\SaludoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('saludo');
})->name('home');

Route::get('/saludo', [SaludoController::class,'saludo']) -> name('saludo');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
