<?php

namespace App\models;

use Wilbispaulo\DBmodel\DBModel;

class User extends DBmodel
{
    public function __construct()
    {
        $this->table = 'ap_usuario';
        $this->setConnection(
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD']
        );
    }
}
