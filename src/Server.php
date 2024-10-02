<?php

namespace Rosa\Router;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router\Helpers
 */
class Server
{
    public static function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function key()
    {
        return $_SERVER['HTTP_X_API_KEY'];
    }

    public static function routeArgv()
    {
        return explode('path=', $_SERVER['argv'][0])[1];
    }

    public static function documentRoot()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
}