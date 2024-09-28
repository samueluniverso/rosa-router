<?php

namespace Rosa\Router;

class Route
{
    public static function get($route, $method)
    {
        global $routes;

        $routes['GET'][] = [
            'route' => $route,
            'method' => $method
        ];
    }

    public static function post($route, $method)
    {
        global $routes;

        $routes['POST'][] = [
            'route' => $route,
            'method' => $method
        ];
    }

    public static function put($route, $method)
    {
        global $routes;

        $routes['PUT'][] = [
            'route' => $route,
            'method' => $method
        ];
    }

    public static function delete($route, $method)
    {
        global $routes;

        $routes['DELETE'][] = [
            'route' => $route,
            'method' => $method
        ];
    }
}