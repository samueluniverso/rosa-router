<?php

use Rockberpro\RestRouter\Route;
use Rockberpro\RestRouter\Controllers\AuthController;
use Rockberpro\RestRouter\Controllers\HelloWorldController;

Route::prefix('auth')->group(function() {
    Route::post('/refresh', [
        AuthController::class, 'refresh'
    ])->public();

    Route::post('/access', [
        AuthController::class, 'access'
    ])->public();
});

Route::get('/hello', [
    HelloWorldController::class, 'hello'
])->private();