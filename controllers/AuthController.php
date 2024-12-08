<?php

namespace Rockberpro\RestRouter\Controllers;

use Rockberpro\RestRouter\Database\Models\SysApiTokens;
use Rockberpro\RestRouter\Jwt;
use Rockberpro\RestRouter\Response;
use Rockberpro\RestRouter\Server;
use Rockberpro\RestRouter\Utils\DotEnv;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
class AuthController
{
    public function refresh()
    {
        if (DotEnv::get('API_AUTH_METHOD') != 'JWT') {
            Response::json(['message' => 'Invalid auth method'], Response::UNAUTHORIZED);
        }

        /** validate */
        if (!Server::secret()) {
            Response::json(['message' => 'Secret not provided'], Response::BAD_REQUEST);
        }
        if (!Server::audience()) {
            Response::json(['message' => 'Audience not provided'], Response::BAD_REQUEST);
        }

        $hash = hash('sha256', Server::secret());
        if (hash_equals(DotEnv::get('JWT_API_KEY'), $hash)) {

            $sysApiTokens = new SysApiTokens();
            $last_token = $sysApiTokens->getLastValidToken(Server::audience());
            if ($last_token) {
                $sysApiTokens->revoke($last_token);
            }

            $refresh_token = Jwt::getRefreshToken(Server::audience());
            try {
                $sysApiTokens->add($refresh_token, Server::audience());
            }
            catch (Exception $e) {
                Response::json(['message' => $e->getMessage()], Response::INTERNAL_SERVER_ERROR);
            }

            Response::json([
                'refresh-token' => "Bearer {$refresh_token}"
            ], Response::OK);
        }

        Response::json(['message' => 'Invalid secret'], Response::UNAUTHORIZED);
    }

    public function access()
    {
        if (DotEnv::get('API_AUTH_METHOD') != 'JWT') {
            Response::json(['message' => 'Invalid auth method'], Response::UNAUTHORIZED);
        }

        /** validate */
        if (!Server::authorization()) {
            Response::json(['message' => 'Refresh-token not provided'], Response::BAD_REQUEST);
        }

        $token = explode(' ', Server::authorization())[1];
        $sysApiTokens = new SysApiTokens();
        if ($sysApiTokens->isRevoked($token)) {
            Response::json(['message' => 'Token revoked'], Response::UNAUTHORIZED);
        }

        Jwt::validate(Server::authorization(), 'refresh');

        $access_token = Jwt::getAccessToken();
        Response::json([
            'access-token' => "Bearer {$access_token}"
        ], Response::OK);
    }
}