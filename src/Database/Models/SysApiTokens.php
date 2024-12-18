<?php

namespace Rockberpro\RestRouter\Database\Models;

use Rockberpro\RestRouter\Database\PDOConnection;

use PDO;
use stdClass;
use Exception;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter
 */
class SysApiTokens
{
    private const ENTITY = 'sys_api_tokens';

    private static ?PDOConnection $pdonection = null;

    /**
     * Get the token
     * 
     * @method get
     * @param string $token
     * @return stdClass
     */
    public function get($token)
    {
        $pdo = self::getConnection();

        $sysApiToken = new stdClass();
        $sysApiToken->token = ['=', $token];

        return $pdo->fetchObject($sysApiToken, SysApiTokens::ENTITY);
    }

    /**
     * Get the last valid token
     * 
     * @method getLastValidToken
     * @param string $audience
     * @return string
     */
    public function getLastValidToken($audience)
    {
        $pdo = self::getConnection();

        $pdo->createPreparedStatement("
            SELECT token FROM sys_api_tokens WHERE audience = :audience AND revoked_at IS NULL ORDER BY created_at DESC LIMIT 1
        ");
        $pdo->bindParameter(':audience', $audience, PDO::PARAM_STR);

        return $pdo->fetch()->token;
    }

    /**
     * Add a new token
     * 
     * @method add
     * @param string $token
     * @param string $audience
     * @return void
     */
    public function add($token, $audience)
    {
        if ($this->exists($token)) {
            throw new Exception('Token already in use');
        }

        $pdo = self::getConnection();

        $sysApiToken = new stdClass();
        $sysApiToken->token = $token;
        $sysApiToken->type = 'Bearer';
        $sysApiToken->audience = $audience;
        $sysApiToken->created_at = date('Y-m-d H:i:s');

        $pdo->beginTransaction();
        $pdo->insertObject($sysApiToken, SysApiTokens::ENTITY);
        $pdo->commitTransaction();
    }

    /**
     * Check if the token exists
     * 
     * @method exists
     * @param string $tokens
     * @return bool
     */
    public function exists($tokens)
    {
        $pdo = self::getConnection();

        $pdo->createPreparedStatement("
            SELECT 1 FROM sys_api_tokens WHERE token = :token
        ");
        $pdo->bindParameter(':token', $tokens, PDO::PARAM_STR);

        return (bool) $pdo->rowCount();
    }

    /**
     * Revoke the key
     * 
     * @method revoke
     * @param string $token
     * @return void
     */
    public function revoke($token)
    {
        $pdo = self::getConnection();
        
        $pdo->beginTransaction();
        $pdo->createPreparedStatement("
            UPDATE sys_api_tokens SET revoked_at = :revoked_at WHERE token = :token
        ");
        $pdo->bindParameter(':token', $token, PDO::PARAM_STR);
        $pdo->bindParameter(':revoked_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $pdo->update();
        $pdo->commitTransaction();
    }

    /**
     * Check if the token is revoked
     * 
     * @method isRevoked
     * @param string $token
     * @return bool
     */
    public function isRevoked($token)
    {
        $pdo = self::getConnection();

        $pdo->createPreparedStatement("
            SELECT 1 FROM sys_api_tokens WHERE token = :token AND revoked_at IS NULL
        ");
        $pdo->bindParameter(':token', $token, PDO::PARAM_STR);

        if ($pdo->fetch()) {
            return false;
        }

        return true;
    }

    /**
     * Singleton connection
     * 
     * @method getConnection
     * @return PDOConnection
     */
    private static  function  getConnection() : PDOConnection {
        if (is_null(self::$pdonection)) {
            self::$pdonection = new PDOConnection();
        }

        return self::$pdonection;
    }
}