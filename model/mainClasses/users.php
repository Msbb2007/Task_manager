<?php

namespace model\mainClasses;

use DateTime;
use model\mainClasses\role;

class users {
    private int $id;
    private string $username;
    private string $password;
    private string $email;
    private role $role;
    private DateTime $created_at;

    public function __construct(array $data = []) {
        $this->id         = $data['id'] ?? 0;
        $this->username   = $data['username'] ?? '';
        $this->password   = $data['password'] ?? '';
        $this->email      = $data['email'] ?? '';
        $this->role       = isset($data['role']) ? role::from($data['role']) : role::Member;
        $this->created_at = isset($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
    }

    //Getterها
    public function getId(): int { 
        return $this->id; }
    public function getUsername(): string { 
        return $this->username; }
    public function getPassword(): string { 
        return $this->password; }
    public function getEmail(): string { 
        return $this->email; }
    public function getRole(): role { 
        return $this->role; }
    public function getCreatedAt(): DateTime { 
        return $this->created_at; }

    //Setterها
    public function setId(int $id): void { 
        $this->id = $id; }
    public function setUsername(string $username): void { 
        $this->username = $username; }
    
    public function setPassword(string $password): void { 
        $this->password = $password; }
    
    public function setEmail(string $email): void { 
        $this->email = $email; }
    public function setRole(role $role): void { 
        $this->role = $role; }
}
