<?php
session_start();
require_once('../models/userModel.php');

if (empty($_SESSION['email'])) {
    header('Location: ../views/client/login.php');
    exit();
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/client/editProfile.php');
    exit();
}

$username = $_POST['username'];
$phone    = $_POST['phoneNumber'];
$gender   = $_POST['gender'];
$address  = $_POST['address'];

if ($username === '' || $phone === '' || $gender === '' || $address === '') {
    echo "<script>alert('All fields are required'); window.location.href='../views/client/editProfile.php';</script>";
    exit();
}

$userData = [
    'email' => $email,
    'username' => $username,
    'phone' => $phone,
    'gender' => $gender,
    'address' => $address
];

if (insertOrUpdateProfile($userData)) {
    $_SESSION['username'] = $username;
    echo "<script>alert('Profile updated successfully!'); window.location.href='../views/client/profile.php';</script>";
    exit();
}

echo "<script>alert('Error updating profile'); window.location.href='../views/client/editProfile.php';</script>";
exit();
