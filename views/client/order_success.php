<?php
require_once '../../controllers/authCheck.php';
require_once '../../controllers/clientHelpers.php';
require_once '../../models/orderModel.php';

$id = (int)($_GET['id'] ?? 0);
$order = $id > 0 ? getOrderById($id) : null;
if(!$order || ($order['email'] ?? '') !== ($_SESSION['email'] ?? '')){
    $_SESSION['flash_error'] = 'Order not found.';
    redirectClient('home.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestCart | Order Success</title>
    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/cart.css?v=<?php echo time(); ?>">
</head>
<body>
<header>
    <div class="header-container">
        <a href="home.php" class="logo">
            <img src="../../assets/images/logo.png" alt="BestCart">
        </a>
        <div class="nav-actions">
            <a href="cart.php" class="nav-btn">🛒 Cart</a>
            <a href="../../controllers/loginHomeController.php" class="nav-btn">🙎🏻‍♂️ Profile</a>
        </div>
    </div>
</header>

<section class="page-wrap">
    <div class="page-container">
        <div class="card">
            <h1 class="page-title">Order Placed Successfully ✅</h1>
            <p class="small">Your order ID is <strong>#<?php echo (int)$order['id']; ?></strong></p>
            <p class="small">Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>
            <p class="small">Total: <strong><?php echo moneyTaka($order['total_amount']); ?></strong></p>

            <div class="actions">
                <a class="btn btn-primary" href="order_invoice.php?id=<?php echo (int)$order['id']; ?>">View Invoice</a>
                <a class="btn btn-outline" href="order_history.php">Order History</a>
                <a class="btn btn-outline" href="home.php">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<footer class="main-footer">
    <div class="footer-copyright">
        © Copyright 2025 BestCart. All Rights Reserved.
    </div>
</footer>
</body>
</html>
