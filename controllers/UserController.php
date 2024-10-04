<?php

namespace Rosa\Controllers;

use Rosa\Router\Request;
use Rosa\Router\Response;

class UserController
{
    public function post(Request $request)
    {
        Response::json([
            'message' => "Hello, {$request->name}!"
        ], Response::OK);
    }
}