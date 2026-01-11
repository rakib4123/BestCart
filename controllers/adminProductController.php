<?php
require_once('helpers.php');
requireAdmin();

require_once('../models/productModel.php');
require_once('../models/categoryModel.php');
require_once('../views/admin/file_handler.php'); 
// ADD PRODUCT (from manage_products.php)
if (isset($_POST['add_product_btn'])) {
    require_csrf();

    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $discount = $_POST['discount'] ?? 0;
    $qty = $_POST['qty'] ?? 0;
    $cat = $_POST['category'] ?? '';
    $desc = $_POST['desc'] ?? '';

    // PHP validation
    [$okName, $name] = v_required($name, 2, 120);
    if (!$okName) { if (isAjax()) jsonOut(false, "Product name is required"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okPrice, $price] = v_float($price, 0, 99999999);
    if (!$okPrice) { if (isAjax()) jsonOut(false, "Invalid price"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okDisc, $discount] = v_float($discount, 0, $price);
    if (!$okDisc) { if (isAjax()) jsonOut(false, "Invalid discount"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okQty, $qty] = v_int($qty, 0, 1000000);
    if (!$okQty) { if (isAjax()) jsonOut(false, "Invalid quantity"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okCat, $cat] = v_required($cat, 1, 80);
    if (!$okCat) { if (isAjax()) jsonOut(false, "Category is required"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    $desc = trim((string)$desc);
    if (strlen($desc) > 2000) { if (isAjax()) jsonOut(false, "Description is too long"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    $image = uploadImage($_FILES['image'] ?? [], 'default.png');

    $data = [
        'name' => $name,
        'price' => $price,
        'discount_price' => $discount,
        'quantity' => $qty,
        'category' => $cat,
        'description' => $desc,
        'image' => $image
    ];

    $ok = addProduct($data);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Product added");
        jsonOut(false, "Failed to add product");
    } else {
        header("Location: ../views/admin/manage_products.php");
        exit;
    }
}

// UPDATE PRODUCT (from edit_product.php)
if (isset($_POST['update_btn'])) {
    require_csrf();

    $id = (int)($_POST['product_id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $discount = $_POST['discount'] ?? 0;
    $qty = $_POST['qty'] ?? 0;
    $cat = $_POST['category'] ?? '';
    $desc = $_POST['desc'] ?? '';

    // PHP validation
    [$okName, $name] = v_required($name, 2, 120);
    if (!$okName) { if (isAjax()) jsonOut(false, "Product name is required"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okPrice, $price] = v_float($price, 0, 99999999);
    if (!$okPrice) { if (isAjax()) jsonOut(false, "Invalid price"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okDisc, $discount] = v_float($discount, 0, $price);
    if (!$okDisc) { if (isAjax()) jsonOut(false, "Invalid discount"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okQty, $qty] = v_int($qty, 0, 1000000);
    if (!$okQty) { if (isAjax()) jsonOut(false, "Invalid quantity"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    [$okCat, $cat] = v_required($cat, 1, 80);
    if (!$okCat) { if (isAjax()) jsonOut(false, "Category is required"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    $desc = trim((string)$desc);
    if (strlen($desc) > 2000) { if (isAjax()) jsonOut(false, "Description is too long"); header("Location: ../views/admin/manage_products.php?err=1"); exit; }

    $p = getProductById($id);
    if (!$p) {
        if (isAjax()) jsonOut(false, "Product not found");
        header("Location: ../views/admin/manage_products.php");
        exit;
    }

    $image = $p['image']; // keep old

    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $p = getProductById($id);
    if (!$p) {
        if (isAjax()) jsonOut(false, "Product not found");
        header("Location: ../views/admin/manage_products.php");
        exit;
    }

    $image = $p['image'] ?? 'default.png';
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] !== '') {
        $image = uploadImage($_FILES['image'], $image);
    }
    }

    $data = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'discount_price' => $discount,
        'quantity' => $qty,
        'category' => $cat,
        'description' => $desc,
        'image' => $image
    ];

    $ok = updateProduct($data);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Product updated");
        jsonOut(false, "Failed to update product");
    } else {
        header("Location: ../views/admin/manage_products.php");
        exit;
    }
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    require_csrf();
    $id = (int)$_GET['delete'];
    $ok = deleteProduct($id);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Product deleted", ['id'=>$id]);
        jsonOut(false, "Failed to delete product");
    } else {
        header("Location: ../views/admin/manage_products.php");
        exit;
    }
}

if (isAjax()) jsonOut(false, "Invalid request");
header("Location: ../views/admin/manage_products.php");
exit;
?>
