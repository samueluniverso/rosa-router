<?php

namespace Rosa\Router\Utils;

use Rosa\Router\Server;

/**
 * Cross-origin Resource Sharing
 * 
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router\Utils
 */
class Cors
{
    public static function allowOrigin()
    {
        $origins = DotEnv::get('API_ALLOW_ORIGIN');
        if ($origins == '*') {
            header("Access-Control-Allow-Origin: *");
            return;
        }
        $_origins = explode(',', $origins);
        $_allow = array_filter($_origins, function($origin) {
            return Server::remoteAddress() == $origin;
        });
        $allow = end($_allow);
        if ($allow) {
            header("Access-Control-Allow-Origin: {$allow}");
        }
    }
}