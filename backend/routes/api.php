<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;

Route::middleware('api')->group(function () {
    Route::apiResource('books', BookController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('loans', LoanController::class);
    Route::get('stats', [LoanController::class, 'statistics']);
});
