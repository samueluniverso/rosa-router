<?php

namespace Rosa\Router;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
 */
class Route
{
    const PREFIX = 'api';

    private static $groupPrefix;

    private static self $instance;

    private $route;
    private $method;
    private $controllerMethod;

    /**
     * @method get
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function get($route, $method)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.self::$groupPrefix.$route;
        }
        self::$instance = new self();
        self::$instance->route = $_route;
        self::$instance->method = 'GET';
        self::$instance->controllerMethod = $method;

        return self::$instance;
    }

    /**
     * @method post
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function post($route, $method)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.self::$groupPrefix.$route;
        }
        self::$instance = new self();
        self::$instance->route = $_route;
        self::$instance->method = 'POST';
        self::$instance->controllerMethod = $method;

        return self::$instance;
    }

    /**
     * @method put
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function put($route, $method)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.self::$groupPrefix.$route;
        }
        self::$instance = new self();
        self::$instance->route = $_route;
        self::$instance->method = 'PUT';
        self::$instance->controllerMethod = $method;

        return self::$instance;
    }

    /**
     * @method patch
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function patch($route, $method)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.self::$groupPrefix.$route;
        }
        self::$instance = new self();
        self::$instance->route = $_route;
        self::$instance->method = 'PATCH';
        self::$instance->controllerMethod = $method;

        return self::$instance;
    }


    /**
     * @method delete
     * @param string $route
     * @param string $method
     * @return Route
     */
    public static function delete($route, $method)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.self::$groupPrefix.$route;
        }
        self::$instance = new self();
        self::$instance->route = $_route;
        self::$instance->method = 'DELETE';
        self::$instance->controllerMethod = $method;

        return self::$instance;
    }

    /**
     * Group routes under the same prefix
     * 
     * @method group
     * @param string $prefix
     * @param function $function
     */
    public static function group($prefix, $function)
    {
        self::$groupPrefix = "/{$prefix}";
        $function();
    }

    /**
     * Setting a public route
     * 
     * @method private
     * @param void
     * @return void
     */
    public function private()
    {
        global $routes;
        $routes[self::$instance->method][] = [
            'route' => self::$instance->route,
            'method' => self::$instance->controllerMethod,
            'public' => false,
        ];
    }

    /**
     * Setting a public route
     * 
     * @method public
     * @param void
     * @return void
     */
    public function public()
    {
        global $routes;
        $routes[self::$instance->method][] = [
            'route' => self::$instance->route,
            'method' => self::$instance->controllerMethod,
            'public' => true,
        ];
    }
}