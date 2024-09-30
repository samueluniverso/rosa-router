<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Request;

class DeleteRequest extends AbstractRequest 
{
    /**
     * Build the request for Delete method
     * 
     * @method buildRequest
     * @param RequestAction $action
     * @return Request
     */
    public function buildRequest($routes, $method, $uri) : Request
    {
        return parent::buildUriRequest($routes, $method, $uri);
    }
}