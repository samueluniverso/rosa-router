<?php

namespace Rockberpro\Router\Helpers;

use Rockberpro\Router\Request;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\Router\Helpers
 */
class GetRequest extends AbstractRequest 
{
    /**
     * Build the request for Get method
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