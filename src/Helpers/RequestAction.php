<?php

namespace Rosa\Router\Helpers;

/**
 * @author ROSA
 * 
 * @version 1.0
 * @package Rosa\Router\Helpers
 */
class RequestAction
{
    private $method;
    private $route;
    private $class;
    private $uri;

    public function getMethod() {
        return $this->method;
    }
    public function setMethod($method) {
        $this->method = $method;
    }

    public function getRoute() {
        return $this->route;
    }
    public function setRoute($route) {
        $this->route = $route;
    }

    public function getClass() {
        return $this->class;
    }
    public function setClass($class) {
        $this->class = $class;
    }

    public function getUri() {
        return $this->uri;
    }
    public function setUri($uri) {
        $this->uri = $uri;
    }
}