<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Request;

class PostRequest extends AbstractRequest 
{
    /**
     * Build the request for Post method
     * 
     * @method buildRequest
     * @param RequestAction $action
     * @return Request
     */
    public function buildRequest($routes, $method, $uri, $data) : Request
    {
        $request = new Request();
        $request->setAction($this->handle($routes, $method, $uri));

        foreach((array) $data as $key => $value) {
            $request->$key = $value;
        }

        return $request;
    }
}