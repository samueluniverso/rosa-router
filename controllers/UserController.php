<?php

namespace Rockberpro\Controllers;

use Rockberpro\Router\Request;
use Rockberpro\Router\Response;

class UserController
{
    public function get(Request $request)
    {
        Response::json([
            'method' => 'get',
            'message' => "Hello, user number {$request->id}!"
        ], Response::OK);
    }

    public function post(Request $request)
    {
        Response::json([
            'method' => 'post',
            'id' => "User {$request->id} is created!"
        ], Response::OK);
    }

    public function put(Request $request)
    {
        Response::json([
            'method' => 'put',
            'id' => "User {$request->id} is updated!"
        ], Response::OK);
    }

    public function patch(Request $request)
    {
        Response::json([
            'method' => 'patch',
            'id' => "User {$request->id} is patched!"
        ], Response::OK);
    }

    public function delete(Request $request)
    {
        Response::json([
            'method' => 'delete',
            'message' => "User {$request->id} is deleted!"
        ], Response::OK);
    }
}