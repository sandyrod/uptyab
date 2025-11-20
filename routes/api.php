<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\ComunidadController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('estados', EstadoController::class);
Route::apiResource('municipios', MunicipioController::class);
Route::apiResource('parroquias', ParroquiaController::class);
Route::apiResource('comunidades', ComunidadController::class);
