<?php
require_once '../../controllers/authCheck.php';
require_once '../../controllers/clientHelpers.php';

$errors = $_SESSION['checkout_errors'] ?? ['name'=>'','phone'=>'','address'=>'','city'=>'','postal'=>''];
$values = $_SESSION['checkout_values'] ?? [
    'name' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'postal' => ''
];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_values']);

$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$cart = $_SESSION['cart'] ?? [];
$items = is_array($cart) ? array_values($cart) : [];
$productTotal = 0.0;
foreach($items as $it){
    $productTotal += ((float)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 0));
}
$deliveryCharge = $productTotal > 0 ? 80 : 0;
$grandTotal = $productTotal + $deliveryCharge;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestCart | Checkout</title>

    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/cart.css?v=<?php echo time(); ?>">

    <style>
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        @media (max-width: 900px){.form-grid{grid-template-columns:1fr;}}
        .field{display:flex;flex-direction:column;gap:6px;margin-bottom:12px;}
        label{font-weight:700;color:#333;}
        .input{padding:12px;border:1px solid #cbd1da;border-radius:10px;}
        .error{color:#b40000;font-size:13px;font-weight:700;}
        .items-mini{margin-top:12px;border-top:1px solid #eee;padding-top:12px;}
        .mini-row{display:flex;justify-content:space-between;margin:6px 0;color:#333;}
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <a href="home.php" class="logo">
            <img src="../../assets/images/logo.png" alt="BestCart">
        </a>
        <div class="categories-dropdown">
            <a href="#">☰ Categories</a>
            <div id="category-list"></div>
        </div>
        <div class="search-center">
            <form class="search-box" action="search.php" method="get">
                <input type="text" name="query" placeholder="Search for products...">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="nav-actions">
            <a href="cart.php" class="nav-btn">🛒 Cart</a>
            <a href="../../controllers/loginHomeController.php" class="nav-btn">🙎🏻‍♂️ Profile</a>
        </div>
    </div>
</header>

<section class="page-wrap">
    <div class="page-container">
        <div class="grid">
            <div class="card">
                <h1 class="page-title">Checkout</h1>

                <?php if($flashError): ?>
                    <div class="alert"><?php echo htmlspecialchars($flashError); ?></div>
                <?php endif; ?>

                <?php if(empty($items)): ?>
                    <p class="small">Your cart is empty. <a class="muted-link" href="home.php">Shop now</a></p>
                <?php else: ?>
                    <form action="../../controllers/checkoutController.php" method="post">
                        <?php echo csrfField(); ?>

                        <div class="form-grid">
                            <div>
                                <div class="field">
                                    <label>Full Name</label>
                                    <input class="input" type="text" name="name" value="<?php echo htmlspecialchars($values['name']); ?>" required>
                                    <?php if(!empty($errors['name'])): ?><div class="error"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label>Email (from your account)</label>
                                    <input class="input" type="email" value="<?php echo htmlspecialchars($values['email']); ?>" readonly>
                                </div>

                                <div class="field">
                                    <label>Phone (11 digits)</label>
                                    <input class="input" type="text" name="phone" value="<?php echo htmlspecialchars($values['phone']); ?>" placeholder="01XXXXXXXXX" required>
                                    <?php if(!empty($errors['phone'])): ?><div class="error"><?php echo htmlspecialchars($errors['phone']); ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div>
                                <div class="field">
                                    <label>Address</label>
                                    <input class="input" type="text" name="address" value="<?php echo htmlspecialchars($values['address']); ?>" required>
                                    <?php if(!empty($errors['address'])): ?><div class="error"><?php echo htmlspecialchars($errors['address']); ?></div><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label>City</label>
                                    <input class="input" type="text" name="city" value="<?php echo htmlspecialchars($values['city']); ?>" required>
                                    <?php if(!empty($errors['city'])): ?><div class="error"><?php echo htmlspecialchars($errors['city']); ?></div><?php endif; ?>
                                </div>

                                <div class="field">
                                    <label>Postal Code (4 digits)</label>
                                    <input class="input" type="text" name="postal" value="<?php echo htmlspecialchars($values['postal']); ?>" placeholder="1207" required>
                                    <?php if(!empty($errors['postal'])): ?><div class="error"><?php echo htmlspecialchars($errors['postal']); ?></div><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="actions">
                            <a class="btn btn-outline" href="cart.php">Back to Cart</a>
                            <button class="btn btn-primary" type="submit">Place Order</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2 class="page-title" style="font-size:22px;">Summary</h2>
                <div class="summary-row"><span>Subtotal</span><strong><?php echo moneyTaka($productTotal); ?></strong></div>
                <div class="summary-row"><span>Delivery</span><strong><?php echo moneyTaka($deliveryCharge); ?></strong></div>
                <div class="summary-row summary-total"><span>Total</span><span><?php echo moneyTaka($grandTotal); ?></span></div>

                <div class="items-mini">
                    <?php foreach($items as $it): ?>
                        <div class="mini-row">
                            <span class="small"><?php echo htmlspecialchars($it['name'] ?? ''); ?> × <?php echo (int)($it['qty'] ?? 0); ?></span>
                            <strong class="small"><?php echo moneyTaka(((float)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 0))); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="main-footer">
    <div class="footer-copyright">
        © Copyright 2025 BestCart. All Rights Reserved.
    </div>
</footer>

<script src="../../assets/js/shop.js?v=<?php echo time(); ?>"></script>
</body>
</html>
