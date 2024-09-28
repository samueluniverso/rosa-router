<?php

namespace Rosa\Controllers;

use Rosa\Router\Request;
use Rosa\Router\Response;

class PostController
{
    public function get(Request $request)
    {
        Response::json([
            'message' => "Post: {$request->post}, Comment: {$request->comment}"
        ], 200);
    }
}