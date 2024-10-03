<?php

namespace Rosa\Controllers;

use Rosa\Router\Request;
use Rosa\Router\Response;

class UserController
{
    public function get(Request $request)
    {
        Response::json([
            'message' => "Hello, {$request->id}!"
        ], Response::OK);
    }
}