<?php

namespace Rosa\Router;

class Route
{
    const PREFIX = 'api';

    public static function get($route, $method)
    {
        global $routes;

        $routes['GET'][] = [
            'route' => Route::PREFIX.$route,
            'method' => $method
        ];
    }

    public static function post($route, $method)
    {
        global $routes;

        $routes['POST'][] = [
            'route' => Route::PREFIX.$route,
            'method' => $method
        ];
    }

    public static function put($route, $method)
    {
        global $routes;

        $routes['PUT'][] = [
            'route' => Route::PREFIX.$route,
            'method' => $method
        ];
    }

    public static function delete($route, $method)
    {
        global $routes;

        $routes['DELETE'][] = [
            'route' => Route::PREFIX.$route,
            'method' => $method
        ];
    }
}