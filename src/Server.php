<?php

namespace Rosa\Router;

/**
 * @author ROSA
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
        return $_SERVER['HTTP_KEY'];
    }
}