<?php

namespace Rockberpro\RestRouter\Helpers;

use Rockberpro\RestRouter\Jwt;
use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Server;
use Rockberpro\RestRouter\Utils\Cors;
use Rockberpro\RestRouter\Utils\DotEnv;
use Rockberpro\RestRouter\Utils\Sop;
use Rockberpro\RestRouter\Response;
use Rockberpro\RestRouter\Database\Models\SysApiKeys;
use Rockberpro\RestRouter\Helpers\Interfaces\AbstractRequestInterface;
use Closure;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.1
 * @package Rockberpro\RestRouter\Helpers
 */
abstract class AbstractRequest implements AbstractRequestInterface
{
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
        $request = $this->pathParams($request, $route_parts, $uri_parts);

        /** handle query params */
        $request = $this->queryParams($request);

        /** handle middleware */
        if ($middleware = $request->getAction()->getMiddleware()) {
            $this->middleware($middleware, $request);
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

        /** handle form params */
        foreach((array) $form as $key => $value) {
            $request->$key = $value;
        }

        /** handle middleware */
        if ($middleware = $request->getAction()->getMiddleware()) {
            $this->middleware($middleware, $request);
        }

        return $request;
    }

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
        $routes_map = $this->map($routes, $method, $uri);
        $match = $this->match($routes_map, $uri);
        if (empty($match)) {
            throw new Exception('No matching route');
        }

        $action = $this->buildAction($routes, $method, $uri, $match);
        $match = $routes[$method][array_key_first($action->getRoute())];

        /** middleware */
        if (isset($match['middleware'])) {
            $action->setMiddleware($match['middleware']);
        }

        /** authentication */
        if($match['public'] === false) {
            $this->secure();
        }

        return $action;
    }

    /**
     * Handle the path params
     * 
     * @method pathParams
     * @param Request $request
     * @param array $route_parts
     * @param array $uri_parts
     * @return Request
     */
    private function pathParams(Request &$request, $route_parts, $uri_parts)
    {
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

        return $request;
    }

    /**
     * Handle the query params
     * 
     * @method queryParams
     * @param Request $request
     * @return Request
     */
    private function queryParams(Request &$request)
    {
        if (stripos(Server::query(), 'path=') !== false) {
            $parts = [];
            $query = Server::query();
            parse_str($query, $parts);
            if (!empty($parts)) {
                foreach($parts as $key => $value) {
                    if ($key !== 'path') {
                        $request->$key = $value;
                    }
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
     * Build the action
     * 
     * @method buildAction
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @param array $match
     * @return RequestAction
     */
    private function buildAction($routes, $method, $uri, $match)
    {
        $action = new RequestAction();
        $action->setUri($uri);
        $action->setRoute($match);

        if (array_key_exists(array_key_first($action->getRoute()), $routes[$method])) {
            $call = $routes[$method][array_key_first($action->getRoute())];

            if ($call['target'] instanceof Closure) {
                $action->setClojure($call['target']);
            }
            else if (gettype($call['target']) === 'array') {
                $class = $call['target'][0];
                $method = $call['target'][1];
                if (!class_exists($class)) {
                    throw new Exception("Class not found: {$class}");
                }
                if (!method_exists($class, $method)) {
                    throw new Exception("Method not found: {$method}");
                }
                $action->setClass($class);
                $action->setMethod($method);
            }
            else {
                throw new Exception('Invalid route target');
            }
        }
        else {
            throw new Exception('No method defined for the route');
        }

        return $action;
    }

    /**
     * Secure the route
     * 
     * @method secure
     * @return void
     */
    private function secure()
    {
        Sop::check();

        if (DotEnv::get('API_AUTH_METHOD') === 'JWT') {
            Jwt::validate(Server::authorization(), 'access');
        }

        if (DotEnv::get('API_AUTH_METHOD') === 'KEY') {
            $sysApiKey = new SysApiKeys();
            $hash = hash('sha256', Server::key());
            if (!$sysApiKey->exists($hash)) {
                Response::json(['message' => "Access denied"], Response::UNAUTHORIZED);
            }
            if ($sysApiKey->isRevoked($hash)) {
                Response::json(['message' => "Access denied"], Response::UNAUTHORIZED);
            }
        }

        Cors::allowOrigin();
    }

    /**
     * Middleware
     * 
     * @method middleware
     * @param string $middleware
     * @param Request $request
     * @return void
     */
    private function middleware($middleware, Request $request)
    {
        if (!class_exists($middleware)) {
            throw new Exception("Middleware not found: {$middleware}");
        }
        if (!method_exists($middleware, 'handle')) {
            throw new Exception("Method 'handle' nod implemented for middleware: {$middleware}");
        }
        $middleware = new $middleware();
        $middleware->handle($request);
    }

    /**
     * Map the routes
     * 
     * @method map
     * @param array $routes
     * @param string $method
     * * @param string $uri
     * @return array mapped_routes
     */
    public function map($routes, $method, $uri)
    {
        if (!$routes[$method])
            throw new Exception("No routes for method {$method}");

        $filter = array_filter(
            $routes[$method],
            function($route) use (&$uri) {
                $parts = explode($route['prefix'], $uri);
                if ($parts[0] === '') {
                    return $route['route'];
                }
            }
        );

        $map = array_map(
            function($route) {
                return $route['route'];
            },
            $filter
        );
   
        return $map;
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