<?php

namespace Rosa\Router\Helpers;

use Exception;

class AbstractRequest
{
    private array $routes_map;

    /**
     * Handle the Request
     * 
     * @method handle
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @return RequestAction
     */
    public function handle($routes, $method, $uri)
    {
        $action = new RequestAction();
        $action->setUri($uri);

        $this->routes_map = $this->map($routes, $method);
        $action->setRoute($this->match($this->routes_map, $action->getUri()));
        if (empty($action->getRoute()))
            throw new Exception('No matching route');

        if (array_key_exists(array_key_first($action->getRoute()), $routes[$method])) {
            $call = $routes[$method][array_key_first($action->getRoute())];
            $action->setClass($call['method'][0]);
            $action->setMethod($call['method'][1]);
        }
        else {
            throw new Exception('No  method defined for route');
        }

        return $action;
    }

    /**
     * Map the routes
     * 
     * @method map
     * @param array $routes
     * @param string $method
     * @return array
     */
    public function map($routes, $method)
    {
        return array_map(
            fn($r) => $r['route'],
            $routes[$method]
        );
    }

    /**
     * Find the matching route
     * 
     * @method match
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @return array
     */
    public function match($mapped_routes, $uri)
    {
        return array_filter(
            $mapped_routes,
            function($route) use ($uri) {
                $route_args = preg_split('/({[\w]+})/', $route, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                if (str_contains($uri, $route_args[0]))
                {
                    return true;
                }
            }
        );
    }

    /**
     * Get the route arguments
     * 
     * @method routeArgs
     * @param array $route_match
     * @return array
     */
    public function routeArgs($route_match) : array
    {
        return preg_split('/(\/[\w]+\/)({[\w]+})/', $route_match[array_key_first($route_match)], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Get the route params
     * 
     * @method routeParams
     * @param string $uri
     * @return array
     */
    public function routeParams($uri)
    {
        $route_params = explode('/', $uri);
        array_shift($route_params);

        return $route_params;
    }
}