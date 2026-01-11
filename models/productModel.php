<?php
require_once('db.php');

function getAllProducts($search = "")
{
    $con = getConnection();
    if ($search != "") {
        $s = mysqli_real_escape_string($con, $search);
        $sql = "SELECT * FROM products 
                    WHERE name LIKE '%$s%' 
                    OR category LIKE '%$s%' 
                    ORDER BY id DESC";
    } else {
        $sql = "SELECT * FROM products ORDER BY id DESC";
    }
    $result = mysqli_query($con, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
    return $data;
}

function getProductById($id)
{
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);
    $result = mysqli_query($con, "SELECT * FROM products WHERE id=$id");
    return mysqli_fetch_assoc($result);
}
