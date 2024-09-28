<?php

use Rosa\Router\Route;

Route::get('api/user/{id}', [
    Rosa\Controllers\UserController::class, 'get'
]);