<?php

namespace Rockberpro\RestRouter\Helpers\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
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