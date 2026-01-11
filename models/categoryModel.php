<?php
require_once('db.php');

function getAllCategories() {
    $con = getConnection();
    $result = mysqli_query($con, "SELECT * FROM categories ORDER BY id DESC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function getCategoryById($id) {
    $con = getConnection();
    $id = (int)$id;
    $result = mysqli_query($con, "SELECT * FROM categories WHERE id=$id");
    return mysqli_fetch_assoc($result);
}

function addCategory($name, $image) {
    $con = getConnection();
    $name = mysqli_real_escape_string($con, $name);
    $image = mysqli_real_escape_string($con, $image);
    $sql = "INSERT INTO categories (name, image) VALUES ('$name', '$image')";
    return mysqli_query($con, $sql);
}

function updateCategory($id, $name, $image) {
    $con = getConnection();
    $id = (int)$id;
    $name = mysqli_real_escape_string($con, $name);
    
    if ($image != "") {
        $image = mysqli_real_escape_string($con, $image);
        $sql = "UPDATE categories SET name='$name', image='$image' WHERE id=$id";
    } else {
        $sql = "UPDATE categories SET name='$name' WHERE id=$id";
    }
    return mysqli_query($con, $sql);
}

function deleteCategory($id) {
    $con = getConnection();
    $id = (int)$id;
    return mysqli_query($con, "DELETE FROM categories WHERE id=$id");
}
?>