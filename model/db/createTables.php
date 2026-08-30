<?php

use model\db\connector;
class createTables{
    
    public function __construct(){
        try{
            $conn = new connector();
            $connect=$conn->getConnection();
            $connect->exec("USE taskdb");
    
            //create Tables
            $tables = [
                "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('admin', 'user') DEFAULT 'user',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",
                
                "CREATE TABLE IF NOT EXISTS categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL
                )",
                
                "CREATE TABLE IF NOT EXISTS tasks (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    priority ENUM ('low','normal','high') DEFAULT 'normal',
                    status ENUM('in_progress', 'done','not_started') DEFAULT 'not_started',
                    due_date DATE,
                    category_id INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
                )",
                
                "CREATE TABLE IF NOT EXISTS task_assignments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    task_id INT,
                    user_id INT,
                    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )"
            ];
    
            foreach ($tables as $query) {
                $connect->exec($query);
            }
            echo"جدول ها با موفقیت ساخنه شدند";
    
    }catch(PDOException $e) {
        die( $e->getMessage());
    }
    $stmt = null;
    $connect = null;
    }
}