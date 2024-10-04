<?php

use Rosa\Router\Route;
use Rosa\Controllers\PostController;
use Rosa\Controllers\UserController;
use Rosa\Controllers\V1\ExampleController as V1ExampleController;
use Rosa\Controllers\V2\ExampleController as V2ExampleController;

/**
 * Simple routes
 */
Route::post('/user/', [
    UserController::class, 'post'
])->private();

Route::get('/post/{post}/comment/{comment}', [
    PostController::class, 'get'
])->public();

/**
 * Grouped routes
 */
Route::group('v1', function() {
    Route::get('/example/{id}', [
        V1ExampleController::class, 'get'
    ])->private();

    Route::post('/example/', [
        V1ExampleController::class, 'post'
    ])->private();
});

Route::group('v2', function() {
    Route::get('/example/{id}', [
        V2ExampleController::class, 'get'
    ])->private();

    Route::post('/example/', [
        V2ExampleController::class, 'post'
    ])->private();
});

/**
 * Nested groups
 */
Route::group('multilevel', function() {
    Route::group('1', function() {
        Route::get('/example/{id}', [
            V2ExampleController::class, 'get'
        ])->private();

        Route::post('/example/', [
            V2ExampleController::class, 'post'
        ])->private();

        Route::group('2', function() {
            Route::get('/example/{id}', [
                V2ExampleController::class, 'get'
            ])->private();

            Route::post('/example/', [
                V2ExampleController::class, 'post'
            ])->private();
        });
    });
});