<?php

namespace Rockberpro\Controllers;

use Rockberpro\Router\Request;
use Rockberpro\Router\Response;

class PostController
{
    public function get(Request $request)
    {
        Response::json([
            'message' => "Post: {$request->route('post')}, Comment: {$request->route('comment')}"
        ], Response::OK);
    }
}