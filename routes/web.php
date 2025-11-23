<?php

use Illuminate\Support\Facades\Route;

// Rutas para Vue - SPA (excluyendo las rutas API)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');