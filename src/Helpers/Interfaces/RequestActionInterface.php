<?php

namespace Rosa\Router\Helpers\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
 */
interface RequestActionInterface
{
    public function getMethod();
    public function setMethod($method);
    public function getRoute();
    public function setRoute($route);
    public function getClass();
    public function setClass($class);
    public function getUri();
    public function setUri($uri);
}