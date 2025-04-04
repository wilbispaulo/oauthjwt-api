<?php

namespace App\library;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;

class Connection
{
    public static function connect(): DBALConnection
    {
        return DriverManager::getConnection([
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'driver' => 'pdo_mysql'
        ]);
    }
}
