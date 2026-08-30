<?php

namespace model\modelDao;

use model\db\connector;
use model\mainClasses\role;
use model\mainClasses\users;

class UserDao{
    private \PDO $db;
    public function __construct(Connector $connector) {
        $this->db = $connector->getConnection();
    }

    public function createUser(users $user):bool{
        $sql1 = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql1);
        $stmt->execute([$user->getUsername()]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($result){
            return false;
        }
        
        $sql2 = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql2);

        return $stmt->execute([
            $user->getUsername(),
            $user->getPassword(),
            $user->getEmail(),
            $user->getRole()->value
        ]);
    }

    public function login(string $username, string $password): ?users{
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!$result){
            return null;
        }
        
        if (password_verify($password, $result['password'])) {
            return new users($result);
        }
    
        return null; 
    }

    public function getUserByUsername(string $username): ?users {
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return new users($result);
    }
    public function getAllUsers(): array {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->query($sql);
        
        $users = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = new users($result);
        }
        return $users;
    }
    public function updateUser(users $user): bool {
        $sql = "UPDATE users SET password = ?, email = ?, role = ? WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $user->getPassword(),
            $user->getEmail(),
            $user->getRole()->value,
            $user->getUsername()
        ]);
    }
    public function deleteUser(string $username): bool {
        $sql = "DELETE FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username]);
    }
}