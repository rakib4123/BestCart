<?php
session_start();

session_unset();
session_destroy();

setcookie('status', 'true', time()-10, '/');

header('Location: ../views/client/login.php');
exit();
?>
