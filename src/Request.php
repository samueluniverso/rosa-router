<?php

namespace Rockberpro\RestRouter;

use Rockberpro\RestRouter\Database\Models\SysApiLogs;
use Rockberpro\RestRouter\Interfaces\RequestInterface;
use Rockberpro\RestRouter\Helpers\DeleteRequest;
use Rockberpro\RestRouter\Helpers\GetRequest;
use Rockberpro\RestRouter\Helpers\PatchRequest;
use Rockberpro\RestRouter\Helpers\PostRequest;
use Rockberpro\RestRouter\Helpers\PutRequest;
use Rockberpro\RestRouter\Helpers\RequestAction;
use Rockberpro\RestRouter\Utils\UrlParser;
use Rockberpro\RestRouter\Utils\Json;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
class Request implements RequestInterface
{
    private RequestAction $action;

    private array $parameters = [];

    /**
     * Get the form data
     * 
     * @method form
     * @param bool $parse
     * @return mixed
     */
    public static function form($parse = true)
    {
        $input = file_get_contents("php://input");

        if (Json::isJson($input))
            return (array) json_decode($input, true);

        if (!$parse)
            return $input;

        $data = [];
        parse_str($input, $data);
        if (empty($data))
            return null;

        return (array) $data;
    }

    /**
     * @method handle
     * @param string $method
     * @param string $uri
     * @param string $query
     * @param array $form
     */
    public function handle($method, $uri, $query = null, $form = null)
    {
        global $routes;
        if (is_null($routes))
            throw new Exception('No registered routes');

        $path = UrlParser::path($uri);
        if ($query) {
            $path = UrlParser::path($query);
        }

        $segments = explode('/', $path ?? '');
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
                $request = (new PostRequest())->buildRequest($routes, $method, $path, $form);
                break;
            case 'PUT':
                $request = (new PutRequest())->buildRequest($routes, $method, $path, $form);
                break;
            case 'PATCH':
                $request = (new PatchRequest())->buildRequest($routes, $method, $path, $form);
                break;
            case 'DELETE':
                $request = (new DeleteRequest())->buildRequest($routes, $method, $path);
                break;
            default: break;
        }
        if (is_null($request))
            throw new Exception('It was not possible to match your request');

        $clojure = $request->getAction()->getClojure();
        if ($clojure) {
            $clojure($request);
            return;
        }

        $class = $request->getAction()->getClass();
        $method = $request->getAction()->getMethod();

        try {
            SysApiLogs::write($request);
        }
        catch(Exception $e) {
            throw new Exception("It was not possible to write the request log: {$e->getMessage()}");
        }

        (new $class)->$method($request);
    }

    /**
     * Set the route action
     * 
     * @method setAction
     * @param RequestAction $action
     * @return void
     */
    public function setAction(RequestAction $action)
    {
        $this->action = $action;
    }

    /**
     * Get the route action
     * 
     * @method getAction
     * @return RequestAction
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get all route parameters
     * 
     * @method getParameters
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get a route variable
     * 
     * @method get
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function get($key)
    {
        return $this->parameters[$key];
    }

    /**
     * Set the route parameters
     * 
     * @method parameters
     * @return array
     */
    public function __set($key, $value)
    {
        return $this->parameters[$key] = $value;
    }

    /**
     * Get the route parameters
     * 
     * @method parameters
     * @return array
     */
    public function __get($key)
    {
        return $this->parameters[$key];
    }
}