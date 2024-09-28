<?php

use Rosa\Router\Request;
use Rosa\Router\Server;

require_once "routes.php";

$uri = Server::uri();
$data = Request::body();
$method = Server::method();
//$query = UrlParser::query($uri); /// for when using engine.php?path=/route on the url

$request = (new Request())->handle($method, $uri, $data);