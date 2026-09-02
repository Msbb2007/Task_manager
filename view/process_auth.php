<?php
session_start();

require_once '../model/db/connection.php';
require_once '../model/modelDao/UserDao.php';
require_once '../controller/AuthController.php';
require_once '../model/mainClasses/users.php';
require_once '../model/mainClasses/role.php';


use model\modelDao\UserDao;
use controller\Authcontroller\AuthController;
use model\db\connector;


$connector = new connector();
$pdo = $connector->getConnection(); 

$userDao = new UserDao($connector); 
$authController = new AuthController($userDao);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        $error = $authController->login($username, $password);

        if ($error === 'admin') {
            header('Location:adminView.php');
            exit();
        }elseif($error === 'member'){
            header('Location:memberView.php');
            exit();
        }

    } elseif ($action === 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $error = $authController->register($name, $email, $password);

        if ($error === "ثبت نام انجام شد") {
            $_SESSION['success_message'] = "ثبت نام شما با موفقیت انجام شد. لطفاً وارد شوید";
            header('Location: index.php'); 
            exit();
        }else{
            $_SESSION['error_message'] = " این نام کاربری قبلا ثبت شده است";
            header('Location: index.php'); 
            exit();
        }
    }

    if ($error === 'نام کاربری یا رمز عبور اشتباه است') {
        $_SESSION['error_message'] = $error;
        header('Location: index.php');
        exit();
    }

} else {
    header('Location: index.php');
    exit();
}
?>
