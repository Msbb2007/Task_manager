<?php

namespace controller\Authcontroller;

use model\modelDao\UserDao;
use model\mainClasses\users;
use model\mainClasses\role;

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

            if ($user->getRole()->value ==='admin') {
                return 'admin';
            } else {
                return'member';
            }
            exit; 
        } else {
            return $eror='نام کاربری یا رمز عبور اشتباه است';
        }
    } 
    
    public function register(string $username, string $email, string $password):string {
        $this->user=new users();

        $this->user->setUsername($username);
        $this->user->setPassword($password);
        $this->user->setEmail($email);
        $this->user->setRole(role::Member);

        $isRegistered = $this->userDao->createUser($this->user);
        if ($isRegistered) {
            return $massage="ثبت نام انجام شد";
        }else{
            return $eror=" این نام کاربری قبلا ثبت شده است";
        }
}
}