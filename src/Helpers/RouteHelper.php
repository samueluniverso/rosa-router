<?php

namespace Rosa\Router\Helpers;

use Rosa\Router\Helpers\Interfaces\RouteHelperInterface;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router\Helpers
 */
class RouteHelper implements RouteHelperInterface
{
    /**
     * Get the route arguments
     * 
     * @method routeArgs
     * @param array $route_match
     * @return array
     */
    public static function routeArgs($route_match)
    {
        return preg_split('/(\/[\w]+\/)({[\w]+})/', $route_match[array_key_first($route_match)], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Get the route matching arguments
     * 
     * @method routeMatchArgs
     * @param string $route
     * @return array
     */
    public static function routeMatchArgs($route)
    {
        return preg_split('/({[\w]+})/', $route, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Get the route params
     * 
     * @method routeParams
     * @param string $uri
     * @return array
     */
    public static function routeParams($uri)
    {
        $route_params = explode('/', $uri);
        array_shift($route_params);

        return $route_params;
    }

    /**
     * Get the route vars
     * 
     * @method routeVars
     * @param string $route
     * @return array
     */
    public static function routeVars($route)
    {
        return preg_split('/({[\w]+})/', $route, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Check if route is alphanumeric
     * 
     * @method isAlphaNumeric
     * @param string $string
     * @return bool
     */
    public static function isAlphaNumeric($string)
    {
        return preg_match('/^[a-zA-Z0-9_]*$/', $string);
    }
}