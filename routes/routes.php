<?php

use Rockberpro\RestRouter\Route;
use Rockberpro\RestRouter\Controllers\PostController;
use Rockberpro\RestRouter\Controllers\UserController;
use Rockberpro\RestRouter\Controllers\V1\ExampleController as V1ExampleController;
use Rockberpro\RestRouter\Controllers\V2\ExampleController as V2ExampleController;

/**
 * Simple routes
 */
Route::get('/user/{id}', [
    UserController::class, 'get'
])->private();

Route::post('/user', [
    UserController::class, 'post'
])->private();

Route::put('/user/', [
    UserController::class, 'put'
])->private();

Route::patch('/user/', [
    UserController::class, 'patch'
])->private();

Route::delete('/user/{id}', [
    UserController::class, 'delete'
])->private();

Route::get('/post/{post}/comment/{comment}', [
    PostController::class, 'get'
])->public();

/**
 * Grouped routes
 */
Route::prefix('v1')->group(function() {
    Route::get('/example/{id}', [
        V1ExampleController::class, 'get'
    ])->private();

    Route::post('/example', [
        V1ExampleController::class, 'post'
    ])->private();
});

Route::prefix('v2')->group(function() {
    Route::get('/example/{id}', [
        V2ExampleController::class, 'get'
    ])->private();

    Route::post('/example', [
        V2ExampleController::class, 'post'
    ])->private();
});

/**
 * Nested groups
 */
Route::prefix('multilevel')->group(function() {
    Route::prefix('1')->group(function() {
        Route::get('/example/{id}', [
            V2ExampleController::class, 'get'
        ])->private();

        Route::post('/example', [
            V2ExampleController::class, 'post'
        ])->private();

        Route::prefix('2')->group(function() {
            Route::get('/example/{id}', [
                V2ExampleController::class, 'get'
            ])->private();

            Route::post('/example', [
                V2ExampleController::class, 'post'
            ])->private();
        });
    });
});