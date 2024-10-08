<?php

namespace Rockberpro\Router\Helpers\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\Router
 */
interface RouteHelperInterface
{
    public static function routeArgs($route_match);
    public static function routeMatchArgs($route);
    public static function routeParams($uri);
    public static function routeVars($route);
    public static function isAlphaNumeric($string);
}