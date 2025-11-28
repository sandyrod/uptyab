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
use App\Http\Controllers\EventoController;
use App\Http\Controllers\AfluenciaController;
use App\Http\Controllers\ComunaController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\ConsejoComunalController;
use App\Http\Controllers\ProyectoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/estados', [EstadoController::class, 'index']);
Route::post('/estados', [EstadoController::class, 'store']);
Route::get('/estados/{id}', [EstadoController::class, 'show']);
Route::put('/estados/{id}', [EstadoController::class, 'update']);
Route::delete('/estados/{id}', [EstadoController::class, 'destroy']);

Route::get('/municipios', [MunicipioController::class, 'index']);
Route::post('/municipios', [MunicipioController::class, 'store']);
Route::get('/municipios/{id}', [MunicipioController::class, 'show']);
Route::put('/municipios/{id}', [MunicipioController::class, 'update']);
Route::delete('/municipios/{id}', [MunicipioController::class, 'destroy']);

Route::get('/parroquias', [ParroquiaController::class, 'index']);
Route::post('/parroquias', [ParroquiaController::class, 'store']);
Route::get('/parroquias/{id}', [ParroquiaController::class, 'show']);
Route::put('/parroquias/{id}', [ParroquiaController::class, 'update']);
Route::delete('/parroquias/{id}', [ParroquiaController::class, 'destroy']);

Route::get('/comunidades', [ComunidadController::class, 'index']);
Route::post('/comunidades', [ComunidadController::class, 'store']);
Route::get('/comunidades/{id}', [ComunidadController::class, 'show']);
Route::put('/comunidades/{id}', [ComunidadController::class, 'update']);
Route::delete('/comunidades/{id}', [ComunidadController::class, 'destroy']);

Route::get('/ubicaciones', [UbicacionController::class, 'index']);
Route::post('/ubicaciones', [UbicacionController::class, 'store']);
Route::get('/ubicaciones/{id}', [UbicacionController::class, 'show']);
Route::put('/ubicaciones/{id}', [UbicacionController::class, 'update']);
Route::delete('/ubicaciones/{id}', [UbicacionController::class, 'destroy']);

Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);
Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{id}', [RoleController::class, 'update']);
Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

Route::post('/usuarios/login', [UsuarioController::class, 'login']);

Route::get('/personas', [PersonaController::class, 'index']);
Route::post('/personas', [PersonaController::class, 'store']);
Route::get('/personas/{id}', [PersonaController::class, 'show']);
Route::put('/personas/{id}', [PersonaController::class, 'update']);
Route::delete('/personas/{id}', [PersonaController::class, 'destroy']);

Route::get('/votacion-centros', [VotacionCentroController::class, 'index']);
Route::post('/votacion-centros', [VotacionCentroController::class, 'store']);
Route::get('/votacion-centros/{id}', [VotacionCentroController::class, 'show']);
Route::put('/votacion-centros/{id}', [VotacionCentroController::class, 'update']);
Route::delete('/votacion-centros/{id}', [VotacionCentroController::class, 'destroy']);

Route::get('/eventos', [EventoController::class, 'index']);
Route::post('/eventos', [EventoController::class, 'store']);
Route::get('/eventos/{id}', [EventoController::class, 'show']);
Route::put('/eventos/{id}', [EventoController::class, 'update']);
Route::delete('/eventos/{id}', [EventoController::class, 'destroy']);
Route::get('/votacion-centros/{id}/eventos', [EventoController::class, 'porVotacionCentro']);

Route::get('/afluencias', [AfluenciaController::class, 'index']);
Route::post('/afluencias', [AfluenciaController::class, 'store']);
Route::get('/afluencias/{id}', [AfluenciaController::class, 'show']);
Route::put('/afluencias/{id}', [AfluenciaController::class, 'update']);
Route::delete('/afluencias/{id}', [AfluenciaController::class, 'destroy']);
// Route::get('/afluencias/{id}/eventos', [AfluenciaController::class, 'porEvento']);
// Route::get('/eventos/{id}/afluencias', [AfluenciaController::class, 'porEvento']);
// Route::get('/votacion-centros/{id}/afluencias', [AfluenciaController::class, 'porVotacionCentro']);
// Route::get('/votacion-centros/{id}/afluencias/{fecha}', [AfluenciaController::class, 'porVotacionCentroPorFecha']);

Route::get('/comunas', [ComunaController::class, 'index']);
Route::post('/comunas', [ComunaController::class, 'store']);
Route::get('/comunas/{id}', [ComunaController::class, 'show']);
Route::put('/comunas/{id}', [ComunaController::class, 'update']);
Route::delete('/comunas/{id}', [ComunaController::class, 'destroy']);
Route::get('/comunas/search/{search}', [ComunaController::class, 'search']);

Route::get('/partidos', [PartidoController::class, 'index']);
Route::post('/partidos', [PartidoController::class, 'store']);
Route::get('/partidos/{id}', [PartidoController::class, 'show']);
Route::put('/partidos/{id}', [PartidoController::class, 'update']);
Route::delete('/partidos/{id}', [PartidoController::class, 'destroy']);
Route::get('/partidos/search/{search}', [PartidoController::class, 'search']);

Route::get('/consejos', [ConsejoComunalController::class, 'index']);
Route::post('/consejos', [ConsejoComunalController::class, 'store']);
Route::get('/consejos/{id}', [ConsejoComunalController::class, 'show']);
Route::put('/consejos/{id}', [ConsejoComunalController::class, 'update']);
Route::delete('/consejos/{id}', [ConsejoComunalController::class, 'destroy']);
Route::get('/consejos/search/{search}', [ConsejoComunalController::class, 'search']);

Route::get('/proyectos', [ProyectoController::class, 'index']);
Route::post('/proyectos', [ProyectoController::class, 'store']);
Route::get('/proyectos/{id}', [ProyectoController::class, 'show']);
Route::put('/proyectos/{id}', [ProyectoController::class, 'update']);
Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy']);
Route::get('/proyectos/search/{search}', [ProyectoController::class, 'search']);