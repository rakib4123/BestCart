<?php
require_once __DIR__ . '/authCheck.php';
require_once __DIR__ . '/clientHelpers.php';
require_once __DIR__ . '/../models/orderModel.php';
require_once __DIR__ . '/../models/productModel.php';
require_once __DIR__ . '/../models/db.php';

function cartItems(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!is_array($cart)) return [];
    return array_values($cart);
}

function calcTotal(array $items): float {
    $t = 0.0;
    foreach ($items as $it) {
        $t += ((float)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 0));
    }
    return $t;
}

$items = cartItems();
$productTotal = calcTotal($items);
if ($productTotal <= 0) {
    $_SESSION['flash_error'] = 'Your cart is empty.';
    redirectClient('../views/client/cart.php');
}

$deliveryCharge = 80; // flat delivery charge (BDT)
$grandTotal = $productTotal + $deliveryCharge;

// Prefill from session (email is authoritative)
$email = $_SESSION['email'] ?? '';
$name  = $_SESSION['username'] ?? '';

$errors = ['name'=>'','phone'=>'','address'=>'','city'=>'','postal'=>''];
$values = ['name'=>$name,'email'=>$email,'phone'=>'','address'=>'','city'=>'','postal'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValidate($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash_error'] = 'Invalid request. Please try again.';
        redirectClient('../views/client/checkout.php');
    }

    $values['name'] = trim($_POST['name'] ?? '');
    $values['phone'] = trim($_POST['phone'] ?? '');
    $values['address'] = trim($_POST['address'] ?? '');
    $values['city'] = trim($_POST['city'] ?? '');
    $values['postal'] = trim($_POST['postal'] ?? '');

    $ok = true;
    if (strlen($values['name']) < 3) { $errors['name'] = 'Name must be at least 3 characters.'; $ok = false; }
    if (!preg_match('/^\d{11}$/', $values['phone'])) { $errors['phone'] = 'Phone must be exactly 11 digits.'; $ok = false; }
    if (strlen($values['address']) < 5) { $errors['address'] = 'Address is too short.'; $ok = false; }
    if (strlen($values['city']) < 2) { $errors['city'] = 'City is too short.'; $ok = false; }
    if (!preg_match('/^\d{4}$/', $values['postal'])) { $errors['postal'] = 'Postal code must be 4 digits.'; $ok = false; }

    // Stock re-check (prevent cheating)
    foreach ($items as $it) {
        $pid = (int)($it['id'] ?? 0);
        $qty = (int)($it['qty'] ?? 0);
        $p = $pid > 0 ? getProductById($pid) : null;
        if (!$p) {
            $_SESSION['flash_error'] = 'A product in your cart no longer exists.';
            redirectClient('../views/client/cart.php');
        }
        $stock = (int)($p['quantity'] ?? 0);
        if ($stock > 0 && $qty > $stock) {
            $_SESSION['flash_error'] = 'Only '.$stock.' item(s) available for '.$p['name'].'.';
            redirectClient('../views/client/cart.php');
        }
    }

    if ($ok) {
        $shipping = [
            'name' => $values['name'],
            'phone' => $values['phone'],
            'address' => $values['address'],
            'city' => $values['city'],
            'postal' => $values['postal'],
        ];

        $orderItemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);
        $shipJson = json_encode($shipping, JSON_UNESCAPED_UNICODE);
        $billJson = $shipJson;

        $orderId = addOrderReturnId([
            'customer_name' => $values['name'],
            'email' => $email,
            'total_amount' => $grandTotal,
            'status' => 'Pending',
            'order_date' => date('Y-m-d'),
            'shipping_address' => $shipJson,
            'billing_address' => $billJson,
            'order_items' => $orderItemsJson,
        ]);

        if (!$orderId) {
            $_SESSION['flash_error'] = 'Failed to place order. Please try again.';
            redirectClient('../views/client/checkout.php');
        }

        // Decrease stock
        $con = getConnection();
        foreach ($items as $it) {
            $pid = (int)($it['id'] ?? 0);
            $qty = (int)($it['qty'] ?? 0);
            if ($pid > 0 && $qty > 0) {
                mysqli_query($con, "UPDATE products SET quantity = GREATEST(quantity - {$qty}, 0) WHERE id = {$pid}");
            }
        }

        // Clear cart
        $_SESSION['cart'] = [];

        redirectClient('../views/client/order_success.php?id='.$orderId);
    }

    // If not ok, fall through to view with errors
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_values'] = $values;
    redirectClient('../views/client/checkout.php');
}

// GET request: just go to checkout page
redirectClient('../views/client/checkout.php');
