<?php

namespace Rockberpro\RestRouter\Database\Models;

use PDO;
use stdClass;
use Exception;
use Rockberpro\RestRouter\Database\PDOConnection;

class SysApiKeys
{
    private const ENTITY = 'sys_api_keys';

    private static ?PDOConnection $connection = null;

    /**
     * Get the key
     * 
     * @method get
     * @param string $key
     * @return stdClass
     */
    public function get($key)
    {
        $con = self::getConnection();

        $sysApiKey = new stdClass();
        $sysApiKey->key = ['=', $key];

        return $con->fetchObject($sysApiKey, SysApiKeys::ENTITY);
    }

    /**
     * Get the last valid key
     * 
     * @method getLastValidKey
     * @param string $audience
     * @return string
     */
    public function getLastValidKey($audience)
    {
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT key FROM sys_api_keys WHERE audience = :audience AND revoked_at IS NULL ORDER BY created_at DESC LIMIT 1
        ");
        $con->bindParameter(':audience', $audience, PDO::PARAM_STR);

        return $con->fetch()->key;
    }

    /**
     * Add a new key
     * 
     * @method add
     * @param string $key
     * @param string $audience
     * @return void
     */
    public function add($key, $audience)
    {
        if ($this->exists($key)) {
            throw new Exception('Key already in use');
        }
        if ($this->isRevoked($key)) {
            throw new Exception('Key revoked');
        }

        $con = self::getConnection();

        $sysApiKey = new stdClass();
        $sysApiKey->key = $key;
        $sysApiKey->audience = $audience;
        $sysApiKey->created_at = date('Y-m-d H:i:s');

        $con->beginTransaction();
        $con->insertObject($sysApiKey, SysApiKeys::ENTITY);
        $con->commitTransaction();
    }

    /**
     * Check if the key exists
     * 
     * @method exists
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT 1 FROM sys_api_keys WHERE key = :key
        ");
        $con->bindParameter(':key', $key, PDO::PARAM_STR);

        return (bool) $con->rowCount();
    }

    /**
     * Revoke the key
     * 
     * @method revoke
     * @param string $key
     * @return void
     */
    public function revoke($key)
    {
        $con = self::getConnection();
        
        $con->beginTransaction();
        $con->createPreparedStatement("
            UPDATE sys_api_keys SET revoked_at = :revoked_at WHERE key = :key
        ");
        $con->bindParameter(':key', $key, PDO::PARAM_STR);
        $con->bindParameter(':revoked_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $con->update();
        $con->commitTransaction();
    }

    /**
     * Check if the key is revoked
     * 
     * @method isRevoked
     * @param string $key
     * @return bool
     */
    public function isRevoked($key)
    {
        $con = self::getConnection();

        $con->createPreparedStatement("
            SELECT 1 FROM sys_api_keys WHERE key = :key AND revoked_at IS NULL
        ");
        $con->bindParameter(':key', $key, PDO::PARAM_STR);

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