<?php

namespace Rosa\Router;

use Exception;
use Rosa\Router\Helpers\GetRequest;
use Rosa\Router\Helpers\RequestAction;
use Rosa\Router\Utils\Json;
use Rosa\Router\Utils\UrlParser;

class Request
{
    private RequestAction $action;

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
        if (is_null($routes))
            throw new Exception('No registered routes');

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
        switch ($method) {
            case 'GET':
                $getRequest = (new GetRequest());
                $request = $getRequest->buildRequest($routes, $method, $uri);
            default: break;
        }

        $class = $request->getAction()->getClass();
        $method = $request->getAction()->getMethod();

        (new $class)->$method($request);
    }

    public function setAction(RequestAction $action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }
}