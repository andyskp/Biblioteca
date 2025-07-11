<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Carga explícita de rutas API
require base_path('routes/api.php');
