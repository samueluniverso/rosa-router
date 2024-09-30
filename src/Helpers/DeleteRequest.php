<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Request;

/**
 * @author ROSA
 * 
 * @version 1.0
 * @package Rosa\Router\Helpers
 */
class DeleteRequest extends AbstractRequest 
{
    /**
     * Build the request for Delete method
     * 
     * @method buildRequest
     * @param array $routes
     * @param string $method
     * @param string $uri
     * @return Request
     */
    public function buildRequest($routes, $method, $uri) : Request
    {
        return parent::buildUriRequest($routes, $method, $uri);
    }
}