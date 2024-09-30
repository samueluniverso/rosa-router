<?php

namespace Rosa\Router\Utils;

class UrlParser
{
    public static function query($uri)
    {
        $vars = [];
        parse_str(
            parse_url($uri, PHP_URL_QUERY),
            $vars
        );

        return $vars['path'];
    }

    public static function path($uri)
    {
        $vars = [];
        parse_str(
            parse_url($uri, PHP_URL_PATH),
            $vars
        );

        return array_key_first($vars);
    }
}