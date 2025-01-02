<?php

use Rockberpro\RestRouter\Database\Models\SysApiKeys;
use Rockberpro\RestRouter\Database\Models\SysApiUsers;
use Rockberpro\RestRouter\Utils\DotEnv;
use Rockberpro\RestRouter\Utils\Uuid;

require_once "../vendor/autoload.php";

DotEnv::load('../.env');

$uuid = new Uuid();
$key = $uuid->uidv4Base64();

print('X-Api-Key: ' . $key . PHP_EOL);

$sysApiKey = new SysApiKeys();
$sysApiKey->add($key, 'postman');

print(PHP_EOL);

$sysApiUser = new SysApiUsers();
$sysApiUser->add('api', 'api', 'generic');
print('Username: api' . PHP_EOL);
print('Password: api' . PHP_EOL);