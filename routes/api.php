<?php

use Rockberpro\RestRouter\Route;
use Rockberpro\RestRouter\Controllers\AuthController;
use Rockberpro\RestRouter\Controllers\HelloWorld;
use Rockberpro\RestRouter\Response;

Route::get('/test/{id}', function() {
    Response::json([
        'message' => 'Apenas um teste'
    ], 200);
})->public();

Route::prefix('auth')->group(function() {
    Route::get('/refresh', [
        AuthController::class, 'refresh'
    ])->public();

    Route::get('/access', [
        AuthController::class, 'access'
    ])->public();
});

Route::get('/hello', [
    HelloWorld::class, 'hello'
])->private();
