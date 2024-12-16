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
 * @package Alfa\RestRouter\Database\Models
 */
class SysApiUsers
{
    const ENTITY = 'sys_api_users';

    private static ?PDOConnection $connection = null;
   
    /**
     * Get an api-client (user)
     * 
     * @method get
     * @param string $username
     * @return stdClass
     */
    public function get($username)
    {
        $con = self::getConnection();

        $sysApiUser = new stdClass();
        $sysApiUser->username = ['=', $username];

        return $con->fetchObject($sysApiUser, SysApiUsers::ENTITY);
    }

    /**
     * Add a new api-client (user)
     * 
     * @method add
     * @param string $username
     * @param string $password
     * @param string $audience
     * @return void
     */
    public function add($username, $password, $audience)
    {
        if ($this->exists($username)) {
            throw new Exception('User already exists');
        }

        $con = self::getConnection();

        $sysApiUser = new stdClass();
        $sysApiUser->username = $username;
        $sysApiUser->password = hash('sha256', $password);
        $sysApiUser->hash_alg = 'sha256';
        $sysApiUser->audience = $audience;
        $sysApiUser->created_at = date('Y-m-d H:i:s');

        $con->beginTransaction();
        $con->insertObject($sysApiUser, SysApiUsers::ENTITY);
        $con->commitTransaction();
    }

    /**
     * Check if the user already exists
     * 
     * @method exists
     * @param string $username
     * @return bool
     */
    public function exists($username)
    {
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT 1 FROM sys_api_users WHERE username = :username
        ");
        $con->bindParameter(':username', $username, PDO::PARAM_STR);

        return (bool) $con->rowCount();
    }

    /**
     * Revokes the user
     * 
     * @method revoke
     * @param string $username
     * @return void
     */
    public function revoke($username)
    {
        $con = self::getConnection();
        
        $con->beginTransaction();
        $con->createPreparedStatement("
            UPDATE sys_api_users SET revoked_at = :revoked_at WHERE username = :username
        ");
        $con->bindParameter(':username', $username, PDO::PARAM_STR);
        $con->bindParameter(':revoked_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $con->update();
        $con->commitTransaction();
    }

    /**
     * Check if the user is revoked
     * 
     * @method isRevoked
     * @param string $username
     * @return bool
     */
    public function isRevoked($username)
    {
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT 1 FROM sys_api_users WHERE username = :username AND revoked_at IS NULL
        ");
        $con->bindParameter(':username', $username, PDO::PARAM_STR);

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
    private static  function getConnection() : PDOConnection {
        if (is_null(self::$connection)) {
            self::$connection = new PDOConnection();
        }

        return self::$connection;
    }
}