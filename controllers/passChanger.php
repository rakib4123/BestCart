<?php
session_start();
require_once('../models/userModel.php');

if (!isset($_SESSION['email'])) {
    header("Location: ../views/client/login.php");
    exit();
}

if (!isset($_POST['submit'])) {
    header("Location: ../views/client/changePassword.php");
    exit();
}

$email = $_SESSION['email'];

    $currentPassword = $_POST['currentPassword'];  
    $newPassword = $_POST['newPass'];  
    $confirmPassword = $_POST['confirmPass']; 

if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
    echo "<script>alert('All fields are required'); window.location.href='../views/client/changePassword.php';</script>";
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo "<script>alert('New password and confirm password do not match');
  window.location.href = '../views/client/changePassword.php';
    </script>";
    exit();
}

$user = ['email' => $email, 'password' => $currentPassword];
$status = login($user);

if (!$status) {
    echo "<script>alert('Incorrect current password'); window.location.href='../views/client/changePassword.php';</script>";
    exit();
}


$updateStatus = updatePassword($email, $newPassword);

if ($updateStatus) {
    echo "<script>alert('Password successfully updated!'); window.location.href='../views/client/profile.php';</script>";
    exit();
} else {
    echo "<script>alert('Error updating the password'); window.location.href='../views/client/changePassword.php';</script>";
    exit();
}
