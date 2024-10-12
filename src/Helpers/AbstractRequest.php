<?php

namespace Rockberpro\RestRouter\Helpers;

use Rockberpro\RestRouter\Auth;
use Rockberpro\RestRouter\Helpers\Interfaces\AbstractRequestInterface;
use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Server;
use Rockberpro\RestRouter\Utils\Cors;
use Rockberpro\RestRouter\Utils\DotEnv;
use Rockberpro\RestRouter\Utils\Sop;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.1
 * @package Rockberpro\RestRouter\Helpers
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

        $prefix = preg_split('/({\w+})/', end($request->getAction()->getRoute()))[0];

        $_uri = str_replace($prefix, '', $request->getAction()->getUri());
        $_route = str_replace($prefix, '', end($request->getAction()->getRoute()));

        $split_uri = explode('/', $_uri);
        $split_route = explode('/', $_route);

        foreach($split_route as $key => $value)
        {
            if ($key %2 === 0) {
                $attribute = substr($value, 1, -1);

                if (isset($split_uri[$key])) {
                    if (!RouteHelper::isAlphaNumeric($split_uri[$key])) {
                        throw new Exception('Route contains invalid characters');
                    }
                    $request->$attribute = $split_uri[$key];
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
                $prefix = RouteHelper::routeMatchArgs($route)[0];

                if (stripos($uri, $prefix) !== false) {
                    $route_parts = explode('/', $route);
                    $uri_parts = explode('/', $uri);

                    if (sizeof($uri_parts) == sizeof($route_parts)) {
                        return true;
                    }

                    return false;
                }
            }
        );
    }
}