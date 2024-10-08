<?php

namespace Rockberpro\RestRouter\Controllers\V2;

use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Response;

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