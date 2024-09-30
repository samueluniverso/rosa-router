<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Request;

class PutRequest extends AbstractRequest 
{
    /**
     * Build the request for Put method
     * 
     * @method buildRequest
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @param array $form
     * @return Request
     */
    public function buildRequest($routes, $method, $uri, $form) : Request
    {
        return parent::buildFormRequest($routes, $method, $uri, $form);
    }
}