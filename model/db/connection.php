<?php

namespace model\db;

use PDO;
use PDOException;
class connector{
    private static $host = "127.0.0.1";
    private static $port = "3307"; 
    private static $username = "root";
    private static $dbname = "taskdb"; 

    public static function getConnection() {
        return new PDO("mysql:host=" . self::$host . ";port=" . self::$port , self::$username,"");
    }

    public static function createDb() {
        try {
            $conn = self::getConnection();
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "CREATE DATABASE IF NOT EXISTS " . self::$dbname;
            $conn->exec($sql);

        } catch(PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>