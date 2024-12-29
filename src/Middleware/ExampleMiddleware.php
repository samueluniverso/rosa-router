<?php

namespace Rockberpro\RestRouter\Middleware;

use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Response;

class ExampleMiddleware
{
    public function handle(Request $request)
    {
        Response::json(['message' => 'ExampleMiddleware'], Response::OK);
    }
}