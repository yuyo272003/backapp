<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgresoController;

Route::post('/progreso', [ProgresoController::class, 'actualizarProgreso']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();

});

Route::middleware('auth:sanctum')->get('/progreso', [ProgresoController::class, 'verProgreso']);

Route::middleware('auth:sanctum')->post('/progreso/siguiente', [ProgresoController::class, 'siguiente']);

Route::middleware('auth:sanctum')->post('/progreso/avanzar', [ProgresoController::class, 'avanzar']);

// routes/api.php
Route::middleware('auth:sanctum')->post('/progreso/siguiente-leccion', [ProgresoController::class, 'siguienteLeccion']);



Route::middleware('auth:sanctum')->post('/progreso/avanzar-vowel-match', [ProgresoController::class, 'avanzarVowelMatchGame']);


Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-2', [ProgresoController::class, 'avanzarLeccion2']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-3', [ProgresoController::class, 'avanzarLeccion3']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-4', [ProgresoController::class, 'avanzarLeccion4']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-5', [ProgresoController::class, 'avanzarLeccion5']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-6', [ProgresoController::class, 'avanzarLeccion6']);


require __DIR__.'/auth.php';
