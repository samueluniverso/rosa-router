<?php

namespace Rosa\Controllers\V1;

use Rosa\Router\Request;
use Rosa\Router\Response;

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