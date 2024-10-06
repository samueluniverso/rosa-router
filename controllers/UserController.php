<?php

namespace Rosa\Controllers;

use Rosa\Router\Request;
use Rosa\Router\Response;

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
            'data' => $request->name
        ], Response::OK);
    }

    public function put(Request $request)
    {
        Response::json([
            'method' => 'put',
            'id' => $request->id
        ], Response::OK);
    }

    public function patch(Request $request)
    {
        Response::json([
            'method' => 'patch',
            'id' => $request->id
        ], Response::OK);
    }

    public function delete(Request $request)
    {
        Response::json([
            'method' => 'delete',
            'message' => "Hello, user number {$request->id}!"
        ], Response::OK);
    }
}