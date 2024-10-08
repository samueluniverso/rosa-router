<?php

namespace Rockberpro\RestRouter\Interfaces;

use Rockberpro\RestRouter\Helpers\RequestAction;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
interface ResponseInterface
{
    public static function json($data, $code);
}