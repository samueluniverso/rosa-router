<?php

namespace Rockberpro\RestRouter\Middleware;

use Rockberpro\RestRouter\Response;
use Rockberpro\RestRouter\Server;
use Rockberpro\RestRouter\Utils\Cors;
use Rockberpro\RestRouter\Utils\Sop;
use Rockberpro\RestRouter\Utils\DotEnv;
use Rockberpro\RestRouter\Database\Models\SysApiKeys;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.1
 * @package Rockberpro\RestRouter\Helpers
 */
class AuthMiddleware
{
    /**
     * Secure the request
     * 
     * @method handle
     * @return void
     */
    public function handle()
    {
        Sop::check();

        if (DotEnv::get('API_AUTH_METHOD') === 'JWT') {
            Jwt::validate(Server::authorization(), 'access');
        }

        if (DotEnv::get('API_AUTH_METHOD') === 'KEY') {
            $sysApiKey = new SysApiKeys();
            $hash = hash('sha256', Server::key());
            if (!$sysApiKey->exists($hash)) {
                Response::json(['message' => "Access denied"], Response::UNAUTHORIZED);
            }
            if ($sysApiKey->isRevoked($hash)) {
                Response::json(['message' => "Access denied"], Response::UNAUTHORIZED);
            }
        }

        Cors::allowOrigin();
    }
}