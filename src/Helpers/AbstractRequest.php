<?php

namespace Rockberpro\RestRouter\Helpers;

use Rockberpro\RestRouter\Auth;
use Rockberpro\RestRouter\Jwt;
use Rockberpro\RestRouter\Helpers\Interfaces\AbstractRequestInterface;
use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Server;
use Rockberpro\RestRouter\Utils\Cors;
use Rockberpro\RestRouter\Utils\DotEnv;
use Rockberpro\RestRouter\Utils\Sop;
use Rockberpro\RestRouter\Utils\UrlParser;
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
        if (empty($action->getRoute())) {
            throw new Exception('No matching route');
        }

        /** handling authentication */
        $match = $routes[$method][array_key_first($action->getRoute())];
        if($match['public'] == false) {
            Sop::check();
            if (DotEnv::get('API_AUTH_METHOD') == 'JWT') {
                Jwt::validate(Server::authorization());
            }
            if (DotEnv::get('API_AUTH_METHOD') == 'KEY') {
                Auth::check(DotEnv::get('API_KEY'), Server::key());
            }
            Cors::allowOrigin();
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

        $prefix = RouteHelper::routeMatchArgs(end($request->getAction()->getRoute()))[0];

        $_uri = str_replace($prefix, '', $request->getAction()->getUri());
        $_route = str_replace($prefix, '', end($request->getAction()->getRoute()));

        $uri_parts = explode('/', $_uri);
        $route_parts = explode('/', $_route);

        /** handle path params */
        foreach($route_parts as $key => $value)
        {
            $attribute = substr($value, 1, -1);

            if (isset($uri_parts[$key])) {
                if ($value === $uri_parts[$key]) {
                    continue;
                }

                if (stripos($value, '{') === false || stripos($value, '}') === false) {
                    if ($value !== $uri_parts[$key]) {
                        throw new Exception('Route does not match');
                    }
                }

                if (!RouteHelper::isAlphaNumeric($uri_parts[$key])) {
                    throw new Exception('Route contains invalid characters');
                }

                $request->$attribute = $uri_parts[$key];
            }
        }

        /** handle query params */
        if (stripos(Server::query(), 'path=') !== false) {
            $parts = UrlParser::query(Server::query());
            if (!empty($parts)) {
                foreach($parts as $key => $value) {
                    $request->$key = $value;
                }
            }
        }
        else if (stripos(Server::uri(), '?') !== false) {
            $parts = [];
            $query = Server::query();
            parse_str($query, $parts);
            if (!empty($query)) {
                foreach($parts as $key => $value) {
                    $request->$key = $value;
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

                $_route_sufixes = explode($prefix, $route);
                $route_sufixes = explode('/', end($_route_sufixes));

                $_uri_sufixes = explode($prefix, $uri)[1];
                $uri_sufixes = explode('/', $_uri_sufixes);

                if (stripos($uri, $prefix) !== false) {
                    $route_parts = explode('/', $route);
                    $uri_parts = explode('/', $uri);

                    if (sizeof($uri_parts) === sizeof($route_parts)) {
                        if (sizeof($uri_sufixes) === sizeof($route_sufixes)) {
                            return true;
                        }

                        return false;
                    }

                    return false;
                }
            }
        );
    }
}