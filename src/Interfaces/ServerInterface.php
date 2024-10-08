<?php

namespace Rockberpro\Router\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\Router
 */
interface ServerInterface
{
    public static function uri();
    public static function method();
    public static function key();
    public static function routeArgv();
    public static function documentRoot();
    public static function serverAddress();
    public static function remoteAddress();
}