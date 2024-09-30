<?php

namespace Rosa\Router;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router\Helpers
 */
class Response
{
    /**
     * Get request
     * 
     * @method json
     * @param arary $data
     * @param int $code
     */
    public static function json($data, $code)
    {
        http_response_code($code);
        exit(
            json_encode($data)
        );
    }
}