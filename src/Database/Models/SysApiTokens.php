<?php

namespace Rockberpro\RestRouter\Database\Models;

use Rockberpro\RestRouter\Database\PDOConnection;

use PDO;
use stdClass;
use Exception;

class SysApiTokens
{
    private const ENTITY = 'sys_api_tokens';

    private static ?PDOConnection $connection = null;

    /**
     * Get the token
     * 
     * @method get
     * @param string $token
     * @return stdClass
     */
    public function get($token)
    {
        $con = self::getConnection();

        $sysApiToken = new stdClass();
        $sysApiToken->token = ['=', $token];

        return $con->fetchObject($sysApiToken, SysApiTokens::ENTITY);
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
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT token FROM sys_api_tokens WHERE audience = :audience AND revoked_at IS NULL ORDER BY created_at DESC LIMIT 1
        ");
        $con->bindParameter(':audience', $audience, PDO::PARAM_STR);

        return $con->fetch()->token;
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

        $con = self::getConnection();

        $sysApiToken = new stdClass();
        $sysApiToken->token = $token;
        $sysApiToken->type = 'Bearer';
        $sysApiToken->audience = $audience;
        $sysApiToken->created_at = date('Y-m-d H:i:s');

        $con->beginTransaction();
        $con->insertObject($sysApiToken, SysApiTokens::ENTITY);
        $con->commitTransaction();
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
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT 1 FROM sys_api_tokens WHERE token = :token
        ");
        $con->bindParameter(':token', $tokens, PDO::PARAM_STR);

        return (bool) $con->rowCount();
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
        $con = self::getConnection();
        
        $con->beginTransaction();
        $con->createPreparedStatement("
            UPDATE sys_api_tokens SET revoked_at = :revoked_at WHERE token = :token
        ");
        $con->bindParameter(':token', $token, PDO::PARAM_STR);
        $con->bindParameter(':revoked_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $con->update();
        $con->commitTransaction();
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
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT 1 FROM sys_api_tokens WHERE token = :token AND revoked_at IS NULL
        ");
        $con->bindParameter(':token', $token, PDO::PARAM_STR);

        if ($con->fetch()) {
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
        if (is_null(self::$connection)) {
            self::$connection = new PDOConnection();
        }

        return self::$connection;
    }
}