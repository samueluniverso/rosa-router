<?php

namespace Rosa\Router\Helpers;

use Exception;
use Rosa\Router\Request;

class GetRequest extends AbstractRequest 
{
    /**
     * Build the request for Get method
     * 
     * @method buildRequest
     * @param RequestAction $action
     * @return Request
     */
    public function buildRequest($routes, $method, $uri) : Request
    {
        $request = new Request();
        $request->setAction($this->handle($routes, $method, $uri));

        $route_args = $this->routeArgs($request->getAction()->getRoute());
        $route_params = $this->routeParams($request->getAction()->getUri());

        foreach($route_args as $key => $value) {
            if ($key == 0) {continue;}

            if ($key %2 == 0) {
                $param = substr($value, 1, -1);
                if ($param !== 'id') {
                    if ($param !== $route_params[$key-1])
                        throw new Exception('Invalid route params');
                }

                $attribute = substr($route_args[$key], 1, -1);
                if (isset($route_params[$key]))
                    $request->$attribute = $route_params[$key];
            }
        }

        return $request;
    }
}