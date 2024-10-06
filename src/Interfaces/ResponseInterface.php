<?php

namespace Rosa\Router\Interfaces;

use Rosa\Router\Helpers\RequestAction;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rosa\Router
 */
interface ResponseInterface
{
    public static function json($data, $code);
}