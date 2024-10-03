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
class CORS
{
    public static function allowOrigin()
    {
        $origins = DotEnv::get('API_ALLOW_ORIGIN');
        $_origins = explode(',', $origins);
        $_allow = array_filter($_origins, function($origin) {
            return Server::remoteAddress() == $origin;
        });
        $allow = $_allow[array_key_first($_allow)];
        if ($allow) {
            header("Access-Control-Allow-Origin: {$allow}");
        }
    }
}