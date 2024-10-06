<?php

namespace Rosa\Router\Interfaces;

use Rosa\Router\Helpers\RequestAction;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
 */
interface RequestInterface
{
    public static function form();
    public function handle($method, $uri, $query = null, $form = null);
    public function setAction(RequestAction $action);
    public function getAction();
    public function route($key);
}