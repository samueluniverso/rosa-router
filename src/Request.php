<?php

namespace Rosa\Router;

use Rosa\Router\Helpers\GetRequest;
use Rosa\Router\Helpers\RequestAction;
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

        $request = new Request();
        $action = new RequestAction();
        switch ($method) {
            case 'GET':
                $getRequest = (new GetRequest());
                $action = $getRequest->handle($routes, $method, $uri);            
                $request = $getRequest->buildRequest();
            default: break;
        }

        $class = $action->getClass();
        $method = $action->getMethod();

        (new $class)->$method($request);
    }
}