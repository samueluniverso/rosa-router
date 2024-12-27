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

    private ?string $namespace;
    private string $prefix;
    private string $route;
    private string $method;
    private $target;

    /**
     * @method get
     * @param string $route
     * @param string $target
     * @return Route
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

        return self::$instance;
    }

    /**
     * @method post
     * @param string $route
     * @param string $target
     * @return Route
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
        self::$instance->target = $target;

        return self::$instance;
    }

    /**
     * @method put
     * @param string $route
     * @param string $target
     * @return Route
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
        self::$instance->target = $target;

        return self::$instance;
    }

    /**
     * @method patch
     * @param string $route
     * @param string $target
     * @return Route
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
        self::$instance->target = $target;

        return self::$instance;
    }


    /**
     * @method delete
     * @param string $route
     * @param string $target
     * @return Route
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
        self::$instance->target = $target;

        return self::$instance;
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
     * Group routes under the same prefix
     * 
     * @method group
     * @param string $prefix
     * @param function $clojure()
     */
    public function group($clojure)
    {
        $clojure();

        /** removing prefix from group */
        array_pop(self::$groupPrefix);
        self::namespace(null);
    }


    /**
     * Setting a private route
     * 
     * @method private
     * @param void
     * @return void
     */
    public function private()
    {
        global $routes;
        $routes[self::$instance->method][] = [
            'prefix' => self::$instance->prefix,
            'route' => self::$instance->route,
            'target' => self::$instance->target,
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
            'prefix' => self::$instance->prefix,
            'route' => self::$instance->route,
            'target' => self::$instance->target,
            'public' => true,
        ];
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
        if (gettype($target) === 'string') {
            if (!isset(self::$instance->namespace)) {
                throw new Exception('Namespace not set');
            }
            $parts = explode('@', $target);
            $controller = self::$instance->namespace.'\\'.$parts[0];
            $method = $parts[1];
            return [$controller, $method];
        }
        if (gettype($target) === 'array') {
            return $target;
        }
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
