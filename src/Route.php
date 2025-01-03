<?php

namespace Rockberpro\RestRouter;

use Rockberpro\RestRouter\Interfaces\RouteInterface;
use Closure;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
class Route implements RouteInterface
{
    const PREFIX = '/api';

    private static $groupPrefix = [];

    private static self $instance;

    private ?string $middleware;
    private ?string $namespace;
    private ?string $controller;
    private string $prefix;
    private string $route;
    private string $method;
    private $target;

    /**
     * @method get
     * @param string $route
     * @param string $target
     * @return void
     */
    public static function get($route, $target)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.implode(self::$groupPrefix).$route;
        }
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->prefix = explode('{', $_route)[0];
        self::$instance->route = $_route;
        self::$instance->method = 'GET';
        self::$instance->target = self::buildTarget($target);

        self::$instance->build();
    }

    /**
     * @method post
     * @param string $route
     * @param string $target
     * @return void
     */
    public static function post($route, $target)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.implode(self::$groupPrefix).$route;
        }
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->prefix = explode('{', $_route)[0];
        self::$instance->route = $_route;
        self::$instance->method = 'POST';
        self::$instance->target = self::buildTarget($target);

        self::$instance->build();
    }

    /**
     * @method put
     * @param string $route
     * @param string $target
     * @return void
     */
    public static function put($route, $target)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.implode(self::$groupPrefix).$route;
        }
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->prefix = explode('{', $_route)[0];
        self::$instance->route = $_route;
        self::$instance->method = 'PUT';
        self::$instance->target = self::buildTarget($target);

        self::$instance->build();
    }

    /**
     * @method patch
     * @param string $route
     * @param string $target
     * @return void
     */
    public static function patch($route, $target)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.implode(self::$groupPrefix).$route;
        }
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->prefix = explode('{', $_route)[0];
        self::$instance->route = $_route;
        self::$instance->method = 'PATCH';
        self::$instance->target = self::buildTarget($target);

        self::$instance->build();
    }


    /**
     * @method delete
     * @param string $route
     * @param string $target
     * @return void
     */
    public static function delete($route, $target)
    {
        $_route = Route::PREFIX.$route;
        if (self::$groupPrefix) {
            $_route = Route::PREFIX.implode(self::$groupPrefix).$route;
        }
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance = new self();
        self::$instance->prefix = explode('{', $_route)[0];
        self::$instance->route = $_route;
        self::$instance->method = 'DELETE';
        self::$instance->target = self::buildTarget($target);

        self::$instance->build();
    }

    /**
     * Adds prefix to the route group
     * 
     * @method prefix
     * @param $prefix
     * @return self
     */
    public static function prefix($prefix)
    {
        self::$groupPrefix[] = "/{$prefix}";

        return new self();
    }

    /**
     * Adds prefix to the route group
     * 
     * @method namespace
     * @param string $namespace
     * @return self
     */
    public static function namespace($namespace)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->namespace = $namespace;

        return self::$instance;
    }

    /**
     * Adds controller to the route
     * 
     * @method controller
     * @param string $controller classname
     * @return self
     */
    public static function controller($controller)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->controller = $controller;

        return self::$instance;
    }

    /**
     * Adds middleware to the route
     * 
     * @method middleware
     * @param string $middleware classname
     * @return self
     */
    public static function middleware($middleware)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->middleware = $middleware;

        return self::$instance;
    }

    /**
     * Group routes under the same prefix
     * 
     * @method group
     * @param string $prefix
     * @param function $closure()
     */
    public function group($closure)
    {
        $closure();

        array_pop(self::$groupPrefix);
        self::namespace(null);
    }

    /**
     * Building the route
     * 
     * @method private
     * @param void
     * @return void
     */
    private function build()
    {
        $route = [
            'prefix' => self::$instance->prefix,
            'route' => self::$instance->route,
            'target' => self::$instance->target
        ];

        if (isset(self::$instance->middleware)) {
            $route['middleware'] = self::$instance->middleware;
        }

        global $routes;
        $routes[self::$instance->method][] = $route;
    }

    /**
     * Build the target for the route
     * 
     * @method buildTarget
     * @param string|array $target
     * @return array
     */
    private static function buildTarget($target)
    {
        if ($target instanceof Closure) {
            return $target;
        }
        if (gettype($target) === 'array') {
            return $target;
        }
        if (gettype($target) === 'string') {
            if (isset(self::$instance->controller)) {
                $controller = self::$instance->controller;
                $method = $target;
            }
            else if (isset(self::$instance->namespace)) {
                $parts = explode('@', $target);
                $controller = self::$instance->namespace.'\\'.$parts[0];
                $method = $parts[1];
            }
            else {
                throw new Exception('Error trying to determine the route target');
            }

            return [$controller, $method];
        }

        throw new Exception('Error trying to determine the route target');
    }

    /**
     * Get all routes
     * 
     * @method getRoutes
     * @param void
     * @return array
     */
    public static function getRoutes()
    {
        global $routes;
        return $routes;
    }
}