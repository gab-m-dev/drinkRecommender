<?php

namespace App\Models;
use \PDO;

class DB
{
    
    private $host = '192.168.0.160';
    private $dbname = 'dbTest';

    public function connect()
    {
        $config = include(__DIR__ . '\..\local.php');
        $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $config['db']['user'], $config['db']['password']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}