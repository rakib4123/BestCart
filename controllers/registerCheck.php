<?php
session_start();
require_once('../models/userModel.php');

if(isset($_POST['submit'])){

    $email    = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $gender   = $_POST['gender'];
    $address  = $_POST['address'];
    $phone    = $_POST['phone'];

    $user = [
        'email'    => $email,
        'password' => $password,
        'username' => $username,
        'gender'   => $gender,
        'address'  => $address,
        'phone'    => $phone
    ];

    $status = addUser($user);

    if($status){
        header('location: ../views/client/login.php');
        exit(); 
    }else{
        echo "<script>alert('This email is already used. Please sign in.'); 
    window.location.href='../views/client/register.php';</script>";
    exit();

    }

}else{
    header('location: ../views/client/register.php');
    exit();
}
