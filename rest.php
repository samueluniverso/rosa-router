<?php

use Rosa\Router\Request;
use Rosa\Router\Response;
use Rosa\Router\Server;
use Rosa\Router\Utils\DotEnv;
use Rosa\Router\Utils\UrlParser;

require_once "vendor/autoload.php";
require_once "routes/routes.php";

$uri = Server::uri(); /// if request: /api
$form = Request::form();
$method = Server::method();
$route = Server::routeArgv(); /// if request: .htaccess redirect
$query = UrlParser::query($uri); /// if request: rest.php?path=/api/route

try
{
    DotEnv::load('.env');
    $request = (new Request())->handle($method, $uri, $query, $form);
}
catch(Throwable $th)
{
    if (DotEnv::get('API_DEBUG'))
    {
        Response::json([
            'error' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
            'trace' => $th->getTrace(),
        ], Response::INTERNAL_SERVER_ERROR);
    }

    Response::json([
        'error' => $th->getMessage(),
    ], Response::INTERNAL_SERVER_ERROR);
}