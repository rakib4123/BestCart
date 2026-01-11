<?php
    $host = "127.0.0.1";
    $dbname = "webtech";
    $dbuser = "root";
    $dbpass = "";

    function getConnection(){
        global $host, $dbname, $dbuser, $dbpass;
        return mysqli_connect($host, $dbuser, $dbpass, $dbname);
    }
?>