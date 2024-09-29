<?php

use Rosa\Router\Request;
use Rosa\Router\Response;
use Rosa\Router\Server;

require_once "routes/routes.php";

$uri = Server::uri();
$data = Request::body();
$method = Server::method();
//$query = UrlParser::query($uri); /// for when using engine.php?path=/api/route on the url

try
{
    $request = (new Request())->handle($method, $uri, $data);
}
catch(Throwable $th)
{
    Response::json([
        'message' => $th->getMessage()
    ], 500);
}