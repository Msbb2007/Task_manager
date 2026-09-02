<?php

namespace controller\Authcontroller;

use model\modelDao\UserDao;
use model\mainClasses\users;

class AuthController {
    private $userDao;
    private $user;

    public function __construct(UserDao $userDao) {
        $this->userDao = $userDao;
    }

    public function login(string $username, string $password):string {
        $eror='';

        $user = $this->userDao->login($username, $password);

        if ($user) {
            session_start();

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['user_role'] = $user->getRole();

            if ($user->getRole() === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: member_dashboard.php');
            }
            exit; 
        } else {
            return $eror='نام کاربری یا رمز عبور اشتباه است';
        }
    } 
    
    public function register(string $username, string $password, string $email):string {
        if (empty($username) || empty($password) || empty($email)) {
            header("Location: index.php?error=empty_fields");
            exit();
        }
        $this->user=new users();

        $this->user->setUsername($username);
        $this->user->setPassword($password);
        $this->user->setEmail($email);

        $isRegistered = $this->userDao->createUser($this->user);

    if ($isRegistered) {
        header("Location: login.php?status=success");
        exit();
    } else {
        header("Location: register.php?error=registration_failed");
        exit();
    }
}
}