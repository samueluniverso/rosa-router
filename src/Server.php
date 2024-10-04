<?php

namespace Rosa\Router;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
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
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            return $_SERVER['HTTP_X_API_KEY'];
        }
        return '';
    }

    public static function routeArgv()
    {
        if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
            return explode('path=', $_SERVER['argv'][0])[1];
        }
    }

    public static function documentRoot()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    public static function serverAddress()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    public static function remoteAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}