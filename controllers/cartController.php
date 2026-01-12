<?php
require_once __DIR__ . '/authCheck.php';
require_once __DIR__ . '/clientHelpers.php';
require_once __DIR__ . '/../models/productModel.php';

function ensureCart(): void {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = []; // [productId => ['id','name','price','qty','image']]
    }
}

function effectivePrice(array $p): float {
    $dp = isset($p['discount_price']) ? (float)$p['discount_price'] : 0.0;
    $price = isset($p['price']) ? (float)$p['price'] : 0.0;
    return ($dp > 0) ? $dp : $price;
}

$action = $_GET['action'] ?? 'index';
ensureCart();

if ($action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $qtyToAdd = (int)($_GET["qty"] ?? 1);
    if ($qtyToAdd < 1) $qtyToAdd = 1;
    if ($qtyToAdd > 99) $qtyToAdd = 99;

    if ($id <= 0) redirectClient('../views/client/cart.php');

    $p = getProductById($id);
    if (!$p) {
        $_SESSION['flash_error'] = 'Product not found.';
        redirectClient('../views/client/cart.php');
    }

    $stock = (int)($p['quantity'] ?? 0);
    $existing = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id]['qty'] : 0;
    if ($stock > 0 && ($existing + $qtyToAdd) > $stock) {
        $_SESSION['flash_error'] = 'Only '.$stock.' item(s) available for '.$p['name'].'.';
        redirectClient('../views/client/cart.php');
    }

    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            'id' => (int)$p['id'],
            'name' => $p['name'],
            'price' => effectivePrice($p),
            'qty' => $qtyToAdd,
            'image' => $p['image'] ?: 'default.png',
        ];
    } else {
        $_SESSION['cart'][$id]['qty'] += $qtyToAdd;
    }

    redirectClient('../views/client/cart.php');
}

if ($action === 'buynow') {
    $id = (int)($_GET['id'] ?? 0);
    $qtyToAdd = (int)($_GET['qty'] ?? 1);
    if ($qtyToAdd < 1) $qtyToAdd = 1;
    if ($qtyToAdd > 99) $qtyToAdd = 99;

    if ($id <= 0) redirectClient('../views/client/cart.php');

    $p = getProductById($id);
    if (!$p) {
        $_SESSION['flash_error'] = 'Product not found.';
        redirectClient('../views/client/cart.php');
    }

    $stock = (int)($p['quantity'] ?? 0);
    $existing = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id]['qty'] : 0;
    if ($stock > 0 && ($existing + $qtyToAdd) > $stock) {
        $_SESSION['flash_error'] = 'Only '.$stock.' item(s) available for '.$p['name'].'.';
        redirectClient('../views/client/cart.php');
    }

    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            'id' => (int)$p['id'],
            'name' => $p['name'],
            'price' => effectivePrice($p),
            'qty' => $qtyToAdd,
            'image' => $p['image'] ?: 'default.png',
        ];
    } else {
        $_SESSION['cart'][$id]['qty'] += $qtyToAdd;
    }

    redirectClient('../views/client/checkout.php');
}

if ($action === 'remove') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) unset($_SESSION['cart'][$id]);
    redirectClient('../views/client/cart.php');
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    redirectClient('../views/client/cart.php');
}

if ($action === 'update') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectClient('../views/client/cart.php');
    if (!csrfValidate($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash_error'] = 'Invalid request. Please try again.';
        redirectClient('../views/client/cart.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 1);

    if ($id > 0 && isset($_SESSION['cart'][$id])) {
        if ($qty < 1) $qty = 1;
        if ($qty > 99) $qty = 99;

        $p = getProductById($id);
        if ($p) {
            $stock = (int)($p['quantity'] ?? 0);
            if ($stock > 0 && $qty > $stock) {
                $qty = $stock;
                $_SESSION['flash_error'] = 'Only '.$stock.' item(s) available for '.$_SESSION['cart'][$id]['name'].'.';
            }
        }

        $_SESSION['cart'][$id]['qty'] = $qty;
    }

    redirectClient('../views/client/cart.php');
}

// default: index is just rendering the view
redirectClient('../views/client/cart.php');
