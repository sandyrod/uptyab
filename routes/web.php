<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "Conexión exitosa a la base de datos!";
    } catch (\Exception $e) {
        return "Error en la conexión: " . $e->getMessage();
    }
});