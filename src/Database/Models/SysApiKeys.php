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
class SysApiKeys
{
    private const ENTITY = 'sys_api_keys';

    private static ?PDOConnection $pdonection = null;

    /**
     * Get the key
     * 
     * @method get
     * @param string $key
     * @return stdClass
     */
    public function get($key)
    {
        $pdo = self::getConnection();

        $sysApiKey = new stdClass();
        $sysApiKey->key = ['=', $key];

        return $pdo->fetchObject($sysApiKey, SysApiKeys::ENTITY);
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
        $pdo = self::getConnection();

        $pdo->createPreparedStatement("
            SELECT key FROM sys_api_keys WHERE audience = :audience AND revoked_at IS NULL ORDER BY created_at DESC LIMIT 1
        ");
        $pdo->bindParameter(':audience', $audience, PDO::PARAM_STR);

        return $pdo->fetch()->key;
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
        $hash = hash('sha256', $key);
        if ($this->exists($hash)) {
            throw new Exception('Key already in use');
        }

        $pdo = self::getConnection();

        $sysApiKey = new stdClass();
        $sysApiKey->key = $hash;
        $sysApiKey->hash_alg = 'sha256';
        $sysApiKey->audience = $audience;
        $sysApiKey->created_at = date('Y-m-d H:i:s');

        $pdo->beginTransaction();
        $pdo->insertObject($sysApiKey, SysApiKeys::ENTITY);
        $pdo->commitTransaction();
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
        $pdo = self::getConnection();

        $pdo->createPreparedStatement("
            SELECT 1 FROM sys_api_keys WHERE key = :key
        ");
        $pdo->bindParameter(':key', $key, PDO::PARAM_STR);

        return (bool) $pdo->rowCount();
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
        $pdo = self::getConnection();
        
        $pdo->beginTransaction();
        $pdo->createPreparedStatement("
            UPDATE sys_api_keys SET revoked_at = :revoked_at WHERE key = :key
        ");
        $pdo->bindParameter(':key', $key, PDO::PARAM_STR);
        $pdo->bindParameter(':revoked_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $pdo->update();
        $pdo->commitTransaction();
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
        $pdo = self::getConnection();

        $pdo->createPreparedStatement("
            SELECT 1 FROM sys_api_keys WHERE key = :key AND revoked_at IS NULL
        ");
        $pdo->bindParameter(':key', $key, PDO::PARAM_STR);

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