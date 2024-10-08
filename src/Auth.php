<?php

namespace Rockberpro\RestRouter;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
class Auth
{
    /**
     * Check if API-Key is valid
     * 
     * * The method used is the HTTP_X_API_KEY Header
     * @example On the REST-client just inform the param "X-API-KEY: <your_key>" on the Header
     * 
     * @param string $api_key
     * @param string $client_key
     * @return void
     */
    public static function check($api_key, $client_key)
    {
        if (!$api_key)
            Response::json([
                'message' => 'API-Key could not be loaded'
            ], Response::FORBIDDEN);

        if (!$client_key)
            Response::json([
                'message' => 'API-Key was not provided'
            ], Response::FORBIDDEN);

        if (!hash_equals($api_key, $client_key))
            Response::json([
                'message' => 'Incorrect API-Key provided'
            ], Response::FORBIDDEN);
    }
}