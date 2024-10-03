<?php

namespace Rosa\Router\Routes;

use Rosa\Router\Route;
use Rosa\Controllers\PostController;
use Rosa\Controllers\UserController;

Route::group('v1', function() {
    Route::group('v2', function() {
        Route::get('/post/{post}/comment/{comment}', [
            PostController::class, 'get'
        ])->private();
    });
});

Route::get('/user/{id}', [
    UserController::class, 'get'
])->public();
