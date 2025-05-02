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

Route::middleware('auth:sanctum')->post('/progreso/get-leccion', [ProgresoController::class, 'obtenerLeccionId']);



Route::middleware('auth:sanctum')->post('/progreso/avanzar-vowel-match', [ProgresoController::class, 'avanzarVowelMatchGame']);

Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-1', [ProgresoController::class, 'avanzarLeccion1']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-2', [ProgresoController::class, 'avanzarLeccion2']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-3', [ProgresoController::class, 'avanzarLeccion3']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-4', [ProgresoController::class, 'avanzarLeccion4']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-5', [ProgresoController::class, 'avanzarLeccion5']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-7', [ProgresoController::class, 'avanzarLeccion6']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-7', [ProgresoController::class, 'avanzarLeccion7']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-8', [ProgresoController::class, 'avanzarLeccion8']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-9', [ProgresoController::class, 'avanzarLeccion9']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-10', [ProgresoController::class, 'avanzarLeccion10']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-11', [ProgresoController::class, 'avanzarLeccion11']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-12', [ProgresoController::class, 'avanzarLeccion12']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-13', [ProgresoController::class, 'avanzarLeccion13']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-14', [ProgresoController::class, 'avanzarLeccion14']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-15', [ProgresoController::class, 'avanzarLeccion15']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-16', [ProgresoController::class, 'avanzarLeccion16']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-17', [ProgresoController::class, 'avanzarLeccion17']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-18', [ProgresoController::class, 'avanzarLeccion18']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-19', [ProgresoController::class, 'avanzarLeccion19']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-20', [ProgresoController::class, 'avanzarLeccion20']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-21', [ProgresoController::class, 'avanzarLeccion21']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-22', [ProgresoController::class, 'avanzarLeccion22']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-23', [ProgresoController::class, 'avanzarLeccion23']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-24', [ProgresoController::class, 'avanzarLeccion24']);
Route::middleware('auth:sanctum')->post('/progreso/avanzar-leccion-25', [ProgresoController::class, 'avanzarLeccion25']);
require __DIR__.'/auth.php';
