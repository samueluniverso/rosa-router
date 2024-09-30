<?php

namespace Rosa\Router;

use Exception;
use Rosa\Router\Helpers\GetRequest;
use Rosa\Router\Helpers\PostRequest;
use Rosa\Router\Helpers\PutRequest;
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

    public function handle($method, $uri, $query = null, $data)
    {
        global $routes;
        if (is_null($routes))
            throw new Exception('No registered routes');

        $path = UrlParser::path($uri);
        if (!is_null($query)) {
            $path = UrlParser::path($query);
        }

        $segments = explode('/', $path);
        array_shift($segments);
        if ($segments[0] !== 'api') {
            http_response_code(404);
            throw new Exception('Not found');
        }

        $request = null;
        switch ($method) {
            case 'GET':
                $request = (new GetRequest())->buildRequest($routes, $method, $path);
                break;
            case 'POST':
                $request = (new PostRequest())->buildRequest($routes, $method, $path, $data);
                break;
            case 'PUT':
                $request = (new PutRequest())->buildRequest($routes, $method, $path, $data);
                break;
            case 'DELETE':
                $request = (new GetRequest())->buildRequest($routes, $method, $path);
                break;
            default: break;
        }
        if (is_null($request))
            throw new Exception('It was not possible to match your request');

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