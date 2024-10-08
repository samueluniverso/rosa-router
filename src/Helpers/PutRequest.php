<?php

namespace Rockberpro\Router\Helpers;

use Rockberpro\Router\Request;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\Router\Helpers
 */
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