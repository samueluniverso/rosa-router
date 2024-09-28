<?php

use Rosa\Router\Route;

Route::get('api/user/{id}', [
    Rosa\Controllers\UserController::class, 'get'
]);

Route::get('api/post/{post}/comment/{comment}', [
    Rosa\Controllers\PostController::class, 'get'
]);