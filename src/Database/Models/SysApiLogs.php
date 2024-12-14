<?php

namespace Rockberpro\RestRouter\Database\Models;

use Rockberpro\RestRouter\Database\PDOConnection;
use Rockberpro\RestRouter\Request;
use Rockberpro\RestRouter\Server;
use PDO;

/**
 * @author Samuel Oberger Rockenbach
 * 
 * @version 1.0
 * @package Rockberpro\RestRouter\Database\Models
 */
class SysApiLogs
{
    const ENTITY = 'sys_api_logs';

    private const SUBJECT = 'rosa-router';

    private static ?PDOConnection $connection = null;

    /**
     * Adds a new log
     * 
     * @method add
     * @param Request $request
     * @return void
     */
    public static function write(Request $request)
    {
        $subject = SysApiLogs::SUBJECT;
        $client_token = Server::authorization();
        $client_key = Server::key();
        $remote_address = Server::remoteAddress();
        $target_address = Server::targetAddress();
        $user_agent = Server::userAgent();
        $request_method = Server::requestMethod();

        $request_uri = Server::requestUri();
        $request_body = json_encode($request->getParameters());

        $endpoint = $request->getAction()->getUri();
        $class = $request->getAction()->getClass();
        $method = $request->getAction()->getMethod();

        $access_at = date('Y-m-d H:i:s');

        $con = self::getConnection();
        $con->beginTransaction();
        $con->createPreparedStatement("
            INSERT INTO sys_api_logs
            (
                subject,
                client_token,
                client_key,
                remote_address,
                target_address,
                user_agent,
                request_method,
                request_uri,
                request_body,
                endpoint,
                class,
                method,
                access_at
            )
            VALUES
            (
                :subject,
                :client_token,
                :client_key,
                :remote_address,
                :target_address,
                :user_agent,
                :request_method,
                :request_uri,
                :request_body,
                :endpoint,
                :class,
                :method,
                :access_at
            )
        ");
        $con->bindParameter(':subject', $subject, PDO::PARAM_STR);
        $con->bindParameter(':client_token', $client_token, PDO::PARAM_STR);
        $con->bindParameter(':client_key', $client_key, PDO::PARAM_STR);
        $con->bindParameter(':remote_address', $remote_address, PDO::PARAM_STR);
        $con->bindParameter(':target_address', $target_address, PDO::PARAM_STR);
        $con->bindParameter(':user_agent', $user_agent, PDO::PARAM_STR);
        $con->bindParameter(':request_method', $request_method, PDO::PARAM_STR);
        $con->bindParameter(':request_uri', $request_uri, PDO::PARAM_STR);
        $con->bindParameter(':request_body', $request_body, PDO::PARAM_STR);
        $con->bindParameter(':endpoint', $endpoint, PDO::PARAM_STR);
        $con->bindParameter(':class', $class, PDO::PARAM_STR);
        $con->bindParameter(':method', $method, PDO::PARAM_STR);
        $con->bindParameter(':access_at', $access_at, PDO::PARAM_STR);
        $con->insert();
        $con->commitTransaction();
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