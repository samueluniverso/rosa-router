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

    private static $namespace;
    private static $controller;
    private static $middleware;

    private static $groupPrefix = [];
    private static $groupNamespace = [];
    private static $groupController = [];
    private static $groupMiddleware = [];

    private static self $instance;

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
        self::$namespace = $namespace;

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

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
        self::$controller = $controller;

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

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
        self::$middleware = $middleware;

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

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
        self::$groupNamespace[] = self::$namespace;
        self::$groupController[] = self::$controller;
        self::$groupMiddleware[] = self::$middleware;

        $closure();

        self::collapseGroups();
    }

    /**
     * Collapse the groups
     * 
     * @method private
     * @return void
     */
    private static function collapseGroups()
    {
        array_pop(self::$groupPrefix);

        array_pop(self::$groupNamespace);
        if (empty(self::$groupNamespace)) {
            self::$namespace = null;
        }

        array_pop(self::$groupController);
        if (empty(self::$groupController)) {
            self::$controller = null;
        }

        array_pop(self::$groupMiddleware);
        if (empty(self::$groupMiddleware)) {
            self::$middleware = null;
        }
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

        self::buildGroups();

        global $routes;
        $routes[self::$instance->method][] = $route;
    }

    /**
     * Build the grouped attributes
     * 
     * @method buildGroups
     * @return void
     */
    private function buildGroups()
    {
        /** controller for grouped namespace */
        $namespace = end(self::$groupNamespace);
        if ($namespace) {
            $route['namespace'] = $namespace;
        }
        /** controller for individual namespace */
        if (isset(self::$namespace) && empty(self::$groupNamespace)) {
            $route['namespace'] = self::$namespace;
            self::$namespace = null;
        }

        /** controller for grouped routes */
        $controller = end(self::$groupController);
        if ($controller) {
            $route['controller'] = $controller;
        }
        /** controller for individual routes */
        if (isset(self::$controller) && empty(self::$groupController)) {
            $route['controller'] = self::$controller;
            self::$controller = null;
        }

        /** middleware for grouped routes */
        $middleware = end(self::$groupMiddleware);
        if ($middleware) {
            $route['middleware'] = $middleware;
        }
        /** middleware for individual routes */
        if (isset(self::$middleware) && empty(self::$groupMiddleware)) {
            $route['middleware'] = self::$middleware;
            self::$middleware = null;
        }
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
            $controller = self::$controller;
            if (isset($controller)) {
                $controller = $controller;
                $method = $target;
            }
            else if (isset(self::$namespace)) {
                $namespace = end(self::$groupNamespace);
                $parts = explode('@', $target);
                $controller = $namespace.'\\'.$parts[0];
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