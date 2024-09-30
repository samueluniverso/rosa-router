<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Request;

class PutRequest extends AbstractRequest 
{
    /**
     * Build the request for Put method
     * 
     * @method buildRequest
     * @param RequestAction $action
     * @return Request
     */
    public function buildRequest($routes, $method, $uri, $data) : Request
    {
        return parent::buildFormRequest($routes, $method, $uri, $data);
    }
}