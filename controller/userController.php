<?php

namespace controller;

use model\db\connector;
use model\modelDao\UserDao;
use model\mainClasses\users;
use model\mainClasses\role;

require_once __DIR__ . '/../model/db/connection.php';
require_once __DIR__ . '/../model/modelDao/UserDao.php';
require_once __DIR__ . '/../model/mainClasses/users.php';
require_once __DIR__ . '/../model/mainClasses/role.php';

class UserController {
    private UserDao $userDao;

    public function __construct() {
        $connector = new connector();
        $this->userDao = new UserDao($connector);
    }

    public function getAllUsers(): array {
        return $this->userDao->getAllUsers();
    }

    public function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_user'])) {
                $this->createUser();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['username'])) {
                $this->deleteUser($_GET['username']);
            }
        }
    }

    private function createUser(): void {
        $username = trim($_POST['username'] ?? $_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleValue = $_POST['role_id'] ?? 'member';

        if (empty($username) || empty($email) || empty($password)) {
            header('Location: ../view/admin/users_list.php?error=empty_fields');
            exit();
        }

        $userData = [
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'role'     => $roleValue
        ];
        
        $user = new users($userData);

        if ($this->userDao->createUser($user)) {
            header('Location: ../view/admin/users_list.php?success=user_created');
        } else {
            header('Location: ../view/admin/users_list.php?error=user_exists');
        }
        exit();
    }

    private function deleteUser(string $username): void {
        if ($this->userDao->deleteUser($username)) {
            header('Location: ../view/admin/users_list.php?success=user_deleted');
        } else {
            header('Location: ../view/admin/users_list.php?error=delete_failed');
        }
        exit();
    }
}

$userController = new UserController();
$userController->handleRequest();