<?php
session_start();
require_once('../models/userModel.php');

if (!isset($_POST['submit'])) {
    header("Location: ../views/client/login.php");
    exit();
}

$identifier = $_POST['email'];
$password   = $_POST['password'];

$user = ['email' => $identifier, 'password' => $password];
$user_data = login($user);

if ($user_data) {
    setcookie('status', 'true', time()+3000, '/');

   
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['role']  = $user_data['role'];

    if (strtolower($_SESSION['role']) === 'admin') {
        $_SESSION['admin_status'] = true;
        header("Location: ../views/admin/dashboard.php");
        exit();
    } else {
        header("Location: ../views/client/home.php");
        exit();
    }
} else {
    echo "<script>alert('Invalid Email/Phone or Password'); window.location.href='../views/client/login.php';</script>";
    exit();
}
