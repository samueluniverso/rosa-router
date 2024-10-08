<?php

namespace Rockberpro\RestRouter\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
interface RouteInterface
{
    public static function get($route, $method);
    public static function post($route, $method);
    public static function put($route, $method);
    public static function patch($route, $method);
    public static function delete($route, $method);
    public static function prefix($prefix);
    public function group($clojure);
    public function private();
    public function public();
    public static function getRoutes();
}