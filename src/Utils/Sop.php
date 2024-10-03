<?php

namespace Rosa\Router\Utils;

use Rosa\Router\Response;
use Rosa\Router\Server;

/**
 * Same Origin Policy
 * 
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router\Utils
 */
class Sop
{
    public static function check()
    {
        $origins = DotEnv::get('API_ALLOW_ORIGIN');
        if ($origins == '*') {
            return;
        }
        $_origins = explode(',', $origins);
        $_allow = array_filter($_origins,function($origin) {
            return Server::remoteAddress() == $origin;
        });
        $allow = end($_allow);
        if (!$allow) {
            Response::json([
                'message' => 'Access denied for your host'
            ], Response::FORBIDDEN);
        }
    }
}