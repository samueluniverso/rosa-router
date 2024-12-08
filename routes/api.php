<?php

use Rockberpro\RestRouter\Route;
use Rockberpro\RestRouter\Controllers\AuthController;

Route::prefix('auth')->group(function() {
    Route::get('/refresh', [
        AuthController::class, 'refresh'
    ])->public();

    Route::get('/access', [
        AuthController::class, 'access'
    ])->public();
});