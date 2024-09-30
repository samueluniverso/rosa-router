<?php

namespace Rosa\Router;

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