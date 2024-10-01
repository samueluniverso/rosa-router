<?php

namespace Rosa\Router\Routes;

use Rosa\Router\Route;
use Rosa\Controllers\PostController;
use Rosa\Controllers\UserController;

Route::get('/user/{id}', [
    UserController::class, 'get'
])->public();

Route::get('/post/{post}/comment/{comment}', [
    PostController::class, 'get'
])->private();