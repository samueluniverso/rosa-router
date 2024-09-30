<?php

namespace Rosa\Router;

use Rosa\Router\Helpers\RouteHelper;

class Route
{
    const PREFIX = 'api';

    protected static $route;
    protected static $method;

    /**
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function get($route, $method)
    {
        global $routes;

        $instance = new self();

        $instance->route = Route::PREFIX.$route;
        $instance->method = 'GET';

        $routes[$instance->method][] = [
            'route' => $instance->route,
            'method' => $method,
            'public' => true,
        ];

        return $instance;
    }

    /**
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function post($route, $method)
    {
        global $routes;

        $instance = new self();

        $instance->route = Route::PREFIX.$route;
        $instance->method = 'POST';

        $routes[$instance->method][] = [
            'route' => $instance->route,
            'method' => $method,
            'public' => true,
        ];

        return $instance;
    }

    /**
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function put($route, $method)
    {
        global $routes;

        $instance = new self();

        $instance->route = Route::PREFIX.$route;
        $instance->method = 'PUT';

        $routes[$instance->method][] = [
            'route' => $instance->route,
            'method' => $method,
            'public' => true,
        ];

        return $instance;
    }

    /**
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function delete($route, $method)
    {
        global $routes;

        $instance = new self();

        $instance->route = Route::PREFIX.$route;
        $instance->method = 'DELETE';

        $routes[$instance->method][] = [
            'route' => $instance->route,
            'method' => $method,
            'public' => true,
        ];

        return $instance;
    }

    /**
     * Protecting a specific route
     * 
     * @method auth
     * @param void
     */
    public function auth()
    {
        global $routes;

        $exists = array_filter(
            $routes[$this->method],
            function($route) {
                $route_args = RouteHelper::routeVars($route['route']);
                $route_params = RouteHelper::routeVars($this->route);
                if (stripos($route_args[0], $route_params[0]) !== false)
                    return true;
            }
        );

        /** protect route */
        if (!empty($exists)) {
            $routes[$this->method][array_key_first($exists)]['public'] = false;
        }
    }
}