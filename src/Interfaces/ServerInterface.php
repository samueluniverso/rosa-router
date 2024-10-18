<?php

namespace Rockberpro\RestRouter\Interfaces;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
interface ServerInterface
{
    public static function uri();
    public static function query();
    public static function method();
    public static function key();
    public static function routeArgv();
    public static function documentRoot();
    public static function serverAddress();
    public static function remoteAddress();
}