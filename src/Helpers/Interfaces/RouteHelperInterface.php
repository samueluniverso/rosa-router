<?php

namespace Rosa\Router\Helpers\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
 */
interface RouteHelperInterface
{
    public static function routeArgs($route_match);
    public static function routeMatchArgs($route);
    public static function routeParams($uri);
    public static function routeVars($route);
    public static function isAlphaNumeric($string);
}