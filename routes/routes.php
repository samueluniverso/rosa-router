<?php

namespace Rosa\Router\Routes;

use Rosa\Router\Route;
use Rosa\Controllers\PostController;
use Rosa\Controllers\UserController;

Route::get('api/user/{id}', [
    UserController::class, 'get'
]);

Route::get('api/post/{post}/comment/{comment}', [
    PostController::class, 'get'
]);