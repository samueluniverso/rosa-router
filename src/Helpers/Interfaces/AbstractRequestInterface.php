<?php

namespace Rosa\Router\Helpers\Interfaces;

use Rosa\Router\Request;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
 */
interface AbstractRequestInterface
{
    public function handle($routes, $method, $uri);
    public function buildUriRequest($routes, $method, $uri) : Request;
    public function buildFormRequest($routes, $method, $uri, $form) : Request;
    public function map($routes, $method);
    public function match($mapped_routes, $uri);
    public function routeArgs($route_match);
    public function routeParams($uri);
}