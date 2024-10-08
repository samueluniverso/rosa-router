<?php

namespace Rockberpro\RestRouter\Controllers;

use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Response;

class PostController
{
    public function get(Request $request)
    {
        Response::json([
            'message' => "Post: {$request->route('post')}, Comment: {$request->route('comment')}"
        ], Response::OK);
    }
}