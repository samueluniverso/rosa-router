<?php

namespace Rosa\Router;

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