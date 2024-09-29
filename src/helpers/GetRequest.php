<?php

namespace Rosa\Router\Helpers;

use Exception;
use Rosa\Router\Request;

class GetRequest
{
    private RequestAction $action;

    private array $routes_map;

    public function handle($routes, $method, $uri)
    {
        $this->action = $action = new RequestAction();
        $action->setUri($uri);

        $this->routes_map = $this->map($routes, $method);
        $action->setRoute($this->match($this->routes_map, $method, $action->getUri()));
        if (empty($action->getRoute()))
            throw new Exception('No matching route');

        $call = $routes[$method][array_key_first($action->getRoute())];

        $action->setClass($call['method'][0]);
        $action->setMethod($call['method'][1]);

        return $this->action;
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
    public function match($mapped_routes, $method, $uri)
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
    private function routeArgs($route_match)
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
    private function routeParams($uri)
    {
        $route_params = explode('/', $uri);
        array_shift($route_params);

        return $route_params;
    }

    /**
     * Build the request for Get method
     * 
     * @method buildRequest
     * @return Request
     */
    public function buildRequest() : Request
    {
        $request = new Request();

        $route_args = $this->routeArgs($this->action->getRoute());
        $route_params = $this->routeParams($this->action->getUri());

        foreach($route_args as $key => $value) {
            if ($key == 0) {continue;}

            if ($key %2 == 0) {
                $param = substr($value, 1, -1);
                if ($param !== 'id') {
                    if ($param !== $route_params[$key-1])
                        throw new Exception('Invalid route params');
                }

                $attribute = substr($route_args[$key], 1, -1);
                if (isset($route_params[$key]))
                    $request->$attribute = $route_params[$key];
            }
        }

        return $request;
    }
}