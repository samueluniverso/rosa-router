<?php

namespace Rockberpro\Controllers\V2;

use Rockberpro\Router\Request;
use Rockberpro\Router\Response;

class ExampleController
{
    public function get(Request $request)
    {
        Response::json([
            'namespace' => 'V2',
            'message' => "id: {$request->id}"
        ], Response::OK);
    }

    public function post(Request $request)
    {
        Response::json([
            'namespace' => 'V2',
            'name' => $request->name
        ], Response::OK);
    }
}