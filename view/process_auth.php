<?php
session_start();

require_once '../model/db/connector.php';
require_once '../model/modelDao/UserDao.php';
require_once '../controller/AuthController.php';

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

        if ($error === null) {
            header('Location:');
            exit();
        }

    } elseif ($action === 'register') {
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];

        $error = $authController->register($name, $phone, $password);

        if ($error === null) {
            $_SESSION['success_message'] = "ثبت نام شما با موفقیت انجام شد. لطفاً وارد شوید.";
            header('Location: index.php'); 
            exit();
        }
    }

    if ($error !== null) {
        $_SESSION['error_message'] = $error;
        header('Location: index.php');
        exit();
    }

} else {
    header('Location: index.php');
    exit();
}
?>
