<?php

namespace Rockberpro\Router\Interfaces;

use Rockberpro\Router\Helpers\RequestAction;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\Router
 */
interface ResponseInterface
{
    public static function json($data, $code);
}