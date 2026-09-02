<?php

namespace model\db;

use PDO;
use PDOException;

class connector {
    private static $host = "127.0.0.1";
    private static $port = "3307"; 
    private static $username = "root";
    private static $dbname = "taskdb"; 

    public static function getConnection() {
        $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbname . ";charset=utf8mb4";
        return new PDO($dsn, self::$username, "");
    }

    public static function getBaseConnection() {
        $dsn = "mysql:host=" . self::$host . ";port=" . self::$port;
        return new PDO($dsn, self::$username, "");
    }

    public static function createDb() {
        try {
            $conn = self::getBaseConnection();
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "CREATE DATABASE IF NOT EXISTS " . self::$dbname;
            $conn->exec($sql);
        } catch(PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}