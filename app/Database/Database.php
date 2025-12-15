<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    protected static $instance = null;
    protected $connection;

    protected function __construct()
    {
        $config = config('database.connections.mysql');
        
        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public static function connection()
    {
        return self::getInstance()->getConnection();
    }
}



