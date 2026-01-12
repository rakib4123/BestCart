<?php
require_once('db.php');

// --- SEARCHABLE PRODUCTS ---

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

function addProduct($p)
{
    $con = getConnection();
    $name = mysqli_real_escape_string($con, $p['name']);
    $desc = mysqli_real_escape_string($con, $p['description']);
    $cat = mysqli_real_escape_string($con, $p['category']);
    $img = mysqli_real_escape_string($con, $p['image']);

    $sql = "INSERT INTO products (name, price, discount_price, quantity, category, description, image) 
                VALUES ('$name', '{$p['price']}', '{$p['discount_price']}', '{$p['quantity']}', '$cat', '$desc', '$img')";
    return mysqli_query($con, $sql);
}

function updateProduct($p)
{
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $p['id']);
    $name = mysqli_real_escape_string($con, $p['name']);
    $desc = mysqli_real_escape_string($con, $p['description']);
    $cat = mysqli_real_escape_string($con, $p['category']);

    if ($p['image'] != "") {
        $img = mysqli_real_escape_string($con, $p['image']);
        $sql = "UPDATE products SET name='$name', price='{$p['price']}', discount_price='{$p['discount_price']}', quantity='{$p['quantity']}', category='$cat', description='$desc', image='$img' WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', price='{$p['price']}', discount_price='{$p['discount_price']}', quantity='{$p['quantity']}', category='$cat', description='$desc' WHERE id=$id";
    }
    return mysqli_query($con, $sql);
}

function deleteProduct($id)
{
    $con = getConnection();
    return mysqli_query($con, "DELETE FROM products WHERE id=$id");
}
function searchProducts($term)
{
    $con = getConnection();
    $term = mysqli_real_escape_string($con, $term);

    
    $sql = "SELECT * FROM products 
            WHERE name LIKE '%$term%' 
            OR category LIKE '%$term%' 
            ORDER BY id DESC";

    $result = mysqli_query($con, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
