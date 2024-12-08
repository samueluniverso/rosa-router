<?php

namespace Rockberpro\RestRouter\Controllers;

use Rockberpro\RestRouter\Response;

class HelloWorld
{
    public function hello()
    {
        Response::json([
            'message' => 'Hello World'
        ], Response::OK);
    }
}