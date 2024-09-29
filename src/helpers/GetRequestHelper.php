<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Request;
use Rosa\Router\Response;

class GetRequestHelper
{
    private RequestAction $action;

    private array $routes_map;
    private array $route_match;

    public function handle($routes, $method, $uri)
    {
        $this->action = new RequestAction();
        $this->action->uri = $uri;

        $this->routes_map = $this->map($routes, $method);
        $this->route_match = $this->match($this->routes_map, $method, $this->action->uri);
        if (empty($this->route_match)) {
            Response::json([
                'message' => 'No matching route'
            ], 403);
        }

        $call = $routes[$method][array_key_first($this->route_match)];

        $this->action->class = $call['method'][0];
        $this->action->method = $call['method'][1];

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

        $route_args = $this->routeArgs($this->route_match);
        $route_params = $this->routeParams($this->action->uri);

        foreach($route_args as $key => $value) {
            if ($key == 0) {continue;}

            if ($key %2 == 0) {
                $param = substr($value, 1, -1);
                if ($param !== 'id') {
                    if ($param !== $route_params[$key-1]) {
                        Response::json([
                            'message' => 'Invalid route params'
                        ], 403);
                    }
                }

                $attribute = substr($route_args[$key], 1, -1);
                if (isset($route_params[$key]))
                    $request->$attribute = $route_params[$key];
            }
        }

        return $request;
    }
}