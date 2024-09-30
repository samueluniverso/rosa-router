<?php

namespace Rosa\Router;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router\Helpers
 */
class Auth
{
    /**
     * Check if API-Key is valid
     * 
     * * The method used is the HTTP_KEY Header
     * @example On the REST-client just inform the param KEY: <your_key> on the Header
     * 
     * @param string $api_key
     * @param string $client_key
     * @return void
     */
    public static function check($api_key, $client_key)
    {
        if (!$api_key)
            Response::json([
                'error' => 'API-Key could not be loaded'
            ], 403);

        if (!$client_key)
            Response::json([
                'error' => 'API-Key was not provided'
            ], 403);

        if (!hash_equals($api_key, $client_key))
            Response::json([
                'error' => 'Incorrect API-Key provided'
            ], 403);
    }
}