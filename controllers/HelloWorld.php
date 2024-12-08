<?php

namespace Rockberpro\RestRouter\Controllers;

use Rockberpro\RestRouter\Response;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
class HelloWorld
{
    public function hello()
    {
        Response::json([
            'message' => 'Hello World'
        ], Response::OK);
    }
}