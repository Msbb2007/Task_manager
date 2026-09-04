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
            } elseif (isset($_POST['update_profile'])) {
                $this->updateProfile();
            } elseif (isset($_POST['delete_self'])) {
                $this->deleteSelfAccount();
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

    private function updateProfile(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;
    
        if (empty($username) || empty($email) || !$userId) {
            header('Location: ../view/user/dashboard.php?error=empty_fields');
            exit();
        }
    
        $userData = [
            'id' => $userId,
            'username' => $username,
            'email' => $email,
            'password' => $password
        ];
    
        $user = new users($userData);
    
        if ($this->userDao->updateUser($user)) {
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            header('Location: ../view/member/dashbord.php?success=profile_updated');
        } else {
            header('Location: ../view/member/dashboard.php?error=update_failed');
        }
        exit();
    }

    private function deleteSelfAccount(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $username = $_SESSION['username'] ?? '';
    
        if (!empty($username) && $this->userDao->deleteUser($username)) {
            $_SESSION = array();
            session_destroy();
            header('Location: ../view/login.php?success=account_deleted');
        } else {
            header('Location: ../view/user/dashboard.php?error=delete_failed');
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