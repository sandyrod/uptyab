<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\VotacionCentroController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('estados', EstadoController::class);
Route::apiResource('municipios', MunicipioController::class);
Route::apiResource('parroquias', ParroquiaController::class);
Route::apiResource('comunidades', ComunidadController::class);
Route::apiResource('ubicaciones', UbicacionController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('personas', PersonaController::class);
Route::apiResource('votacion-centros', VotacionCentroController::class);
