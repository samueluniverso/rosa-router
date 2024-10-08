<?php

namespace Rockberpro\Router\Helpers;

use Rockberpro\Router\Auth;
use Rockberpro\Router\Helpers\Interfaces\AbstractRequestInterface;
use Rockberpro\Router\Request;
use Rockberpro\Router\Server;
use Rockberpro\Router\Utils\Cors;
use Rockberpro\Router\Utils\DotEnv;
use Rockberpro\Router\Utils\Sop;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\Router\Helpers
 */
abstract class AbstractRequest implements AbstractRequestInterface
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

        /** handling authentication */
        $match = $routes[$method][array_key_first($action->getRoute())];
        if($match['public'] == false) {
            Sop::check();
            Cors::allowOrigin();
            Auth::check(DotEnv::get('API_KEY'), Server::key());
        }

        if (array_key_exists(array_key_first($action->getRoute()), $routes[$method])) {
            $call = $routes[$method][array_key_first($action->getRoute())];
            $action->setClass($call['method'][0]);
            $action->setMethod($call['method'][1]);
        }
        else {
            throw new Exception('No method defined for the route');
        }

        return $action;
    }

    /**
     * Build the request from the URL
     * 
     * @method buildUriRequest
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @return Request
     */
    public function buildUriRequest($routes, $method, $uri) : Request
    {
        $request = new Request();
        $request->setAction($this->handle($routes, $method, $uri));

        $route_args = $this->routeArgs($request->getAction()->getRoute());
        $route_params = $this->routeParams($request->getAction()->getUri());

        /** shift array when the key has no arg assigned to it */
        if (sizeof($route_params) > sizeof($route_args)) {
            $route_params = array_splice($route_params, (sizeof($route_params) - sizeof($route_args)));
        }

        foreach($route_args as $key => $value) {
            if ($key == 0) {continue;}

            if ($key %2 == 0) {
                $param = substr($value, 1, -1);
                if ($param !== 'id') {
                    if ($param !== $route_params[$key-1])
                        throw new Exception('Invalid route params');
                }

                $attribute = substr($route_args[$key], 1, -1);
                if (isset($route_params[$key])) {
                    if (!RouteHelper::isAlphaNumeric($route_params[$key])) {
                        throw new Exception('Route contains invalid characters');
                    }
                    $request->$attribute = $route_params[$key];
                }
            }
        }

        return $request;
    }

    /**
     * Build the request from form data
     * 
     * @method buildRequest
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @param array $form
     * @return Request
     */
    public function buildFormRequest($routes, $method, $uri, $form) : Request
    {
        $request = new Request();
        $request->setAction($this->handle($routes, $method, $uri));

        foreach((array) $form as $key => $value) {
            $request->$key = $value;
        }

        return $request;
    }

    /**
     * Map the routes
     * 
     * @method map
     * @param array $routes
     * @param string $method
     * @return array mapped_routes
     */
    public function map($routes, $method)
    {
        if (!$routes[$method])
            throw new Exception("No routes for method {$method}");

        return array_map(
            fn($r) => $r['route'],
            $routes[$method]
        );
    }

    /**
     * Find the matching route
     * 
     * @method match
     * @param array $mapped_routes
     * @param string $method
     * @param string $uri
     * @return array
     */
    public function match($mapped_routes, $uri)
    {
        return array_filter(
            $mapped_routes,
            function($route) use ($uri) {
                $route_args = RouteHelper::routeMatchArgs($route);
                if (stripos($uri, $route_args[0]) !== false) {
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
    public function routeArgs($route_match)
    {
        return RouteHelper::routeArgs($route_match);
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
        return RouteHelper::routeParams($uri);
    }
}