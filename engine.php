<?php

use Rosa\Router\Utils\DotEnv;
use Rosa\Router\Request;
use Rosa\Router\Response;
use Rosa\Router\Server;
use Rosa\Router\Utils\UrlParser;

require_once "vendor/autoload.php";
require_once "routes/routes.php";

$uri = Server::uri();
$data = Request::body();
$method = Server::method();
$query = UrlParser::query($uri); /// engine.php?path=/api/route

try
{
    DotEnv::load('.env');
    $request = (new Request())->handle($method, $uri, $query, $data);
}
catch(Throwable $th)
{
    if (DotEnv::get('API_DEBUG'))
    {
        Response::json([
            'error' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
            'stack' => $th->getTrace(),
        ], 500);
    }

    Response::json([
        'error' => $th->getMessage(),
    ], 500);
}