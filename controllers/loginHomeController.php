<?php
session_start();

if (isset($_SESSION['email'])) {
    header("Location: ../views/client/profile.php");
    exit();
} else {
    header("Location: ../views/client/login.php");
    exit();
}