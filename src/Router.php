<?php

namespace Rosa\Router;

use Exception;
use Throwable;

class Router
{
    private array $routes = [];
    
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @method handle
     * @param string $path
     * @param string $action
     * @return void
     */
    public function handle($method, $path)
    {
        try
        {
            $action = explode('?', $path)[0];
            $this->defaultRoute($action, $method);

            $controller = $this->routes[$method][$action][0];
            $action     = $this->routes[$method][$action][1];

            if (!class_exists($controller)) {
                throw new Exception("Controller {$controller} doesn't exist.");
            }

            $_controller = new $controller();
            if (!method_exists($_controller, $action)) {
                throw new Exception("Method {$action} from Controller {$controller} doesn't exist.");
            }

            /**
             * Route for View or for API
             */
            if (str_contains($this->parsePath($path), 'api'))
            {
                $_controller->$action($this->parseParams($path));
            }
        }
        catch(Throwable $e)
        {
            echo $e->getMessage();
        }
    }

    private function parseParams($url)
    {
        $param = [];
        parse_str($this->parseQuery($url), $param);

        if (!isset($param['cipher']))
            $param['cipher'] = '';
        if (!isset($param['sword']))
            $param['sword'] = '';

        return $param;
    }

    private function parsePath($url)
    {
        return parse_url($url)['path'];
    }

    private function parseQuery($url)
    {
        return parse_url($url)['query'];
    }

    /**
     * Fallback to root if request is invalid
     * 
     * @method defaultRoute
     * @param string $action
     * @param string $method
     * @return void
     */
    private function defaultRoute($action, $method)
    {
        if (
           str_contains($action, 'api')
        && !array_key_exists($action, $this->routes[$method])
        ){
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json");
            http_response_code(404);
            exit(
                json_encode([
                    'message' => 'Route not found'
                ])
            );
        }
    }
}