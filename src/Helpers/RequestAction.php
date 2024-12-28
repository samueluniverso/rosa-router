<?php

namespace Rockberpro\RestRouter\Helpers;

use Rockberpro\RestRouter\Helpers\Interfaces\RequestActionInterface;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter\Helpers
 */
class RequestAction implements RequestActionInterface
{
    private $middleware;
    private $method;
    private $clojure;
    private $route;
    private $private;
    private $class;
    private $uri;

    public function getMiddleware() {
        return $this->middleware;
    }
    public function setMiddleware($middleware) {
        $this->middleware = $middleware;
    }

    public function getMethod() {
        return $this->method;
    }
    public function setMethod($method) {
        $this->method = $method;
    }

    public function getClojure() {
        return $this->clojure;
    }
    public function setClojure($clojure) {
        $this->clojure = $clojure;
    }

    public function getRoute() {
        return $this->route;
    }
    public function setRoute($route) {
        $this->route = $route;
    }

    public function getPrivate() {
        return $this->private;
    }
    public function setPrivate($private) {
        $this->private = $private;
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