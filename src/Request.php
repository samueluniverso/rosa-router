<?php

namespace Rosa\Router;

use Rosa\Router\Utils\Json;
use Rosa\Router\Utils\UrlParser;

class Request
{
    public static function body($parse = true)
    {
        $input = file_get_contents("php://input");

        if (Json::isJson($input))
            return json_decode($input, true);        

        if (!$parse)
            return $input;

        $data = [];
        parse_str($input, $data);
        if (empty($data))
            return null;

        return (object) $data;
    }

    public function handle($method, $uri, $data)
    {
        global $routes;
        if (is_null($routes)) {
            Response::json([
                'message' => 'No registered routes'
            ], 403);
        }

        $path = UrlParser::path($uri);
        $segments = explode('/', $path);
        array_shift($segments);
        if ($segments[0] !== 'api') {
            http_response_code(404);
            exit(json_encode([
                'message' => 'Not found'
            ]));
        }

        /** mapping routes */
        $routes_map = array_map(
            fn($r) => $r['route'],
            $routes[$method]
        );

        /** find matching route */
        $route_match = array_filter(
            $routes_map,
            function($route) use ($uri) {
                $route_args = preg_split('/({[\w]+})/', $route, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                if (str_contains($uri, $route_args[0]))
                {
                    return true;
                }
            }
        );
        if (empty($route_match)) {
            Response::json([
                'message' => 'No matching route'
            ], 403);
        }

        /** build request object */
        $route_args = preg_split('/(\/[\w]+\/)({[\w]+})/', $route_match[array_key_first($route_match)], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $route_params = explode('/', $uri);
        array_shift($route_params);
        
        foreach($route_args as $key => $value) {
            if ($key == 0) {continue;}

            if ($key %2 == 0) {
                $attribute = $route_params[$key-1];
                if (isset($route_params[$key]))
                    $this->$attribute = $route_params[$key];
            }
        }

        $call = $routes[$method][array_key_first($route_match)];

        $class = $call['method'][0];
        $method = $call['method'][1];

        (new $class())->$method($this);
    }
}