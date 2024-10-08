<?php

namespace Rockberpro\RestRouter\Controllers\V1;

use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Response;

class ExampleController
{
    public function get(Request $request)
    {
        Response::json([
            'namespace' => 'V1',
            'message' => "id: {$request->id}"
        ], Response::OK);
    }

    public function post(Request $request)
    {
        Response::json([
            'namespace' => 'V1',
            'name' => $request->name
        ], Response::OK);
    }
}