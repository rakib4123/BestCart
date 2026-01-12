<?php
require_once '../../controllers/authCheck.php';
require_once '../../controllers/clientHelpers.php';
require_once '../../models/productModel.php';

// Ensure cart exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$cartItems = array_values($_SESSION['cart']);
$total = 0.0;
foreach ($cartItems as $it) {
    $total += ((float)($it['price'] ?? 0)) * ((int)($it['qty'] ?? 0));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestCart | Cart</title>

    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/cart.css?v=<?php echo time(); ?>">
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
                <h1 class="page-title">Your Cart</h1>

                <?php if($flashError): ?>
                    <div class="alert"><?php echo htmlspecialchars($flashError); ?></div>
                <?php endif; ?>

                <?php if(empty($cartItems)): ?>
                    <p class="small">Your cart is empty. <a class="muted-link" href="home.php">Continue shopping</a></p>
                <?php else: ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cartItems as $it): 
                                $pid = (int)($it['id'] ?? 0);
                                $qty = (int)($it['qty'] ?? 1);
                                $price = (float)($it['price'] ?? 0);
                                $sub = $price * $qty;
                                $img = $it['image'] ?? 'default.png';
                                $imgPath = '../../uploads/' . $img;
                            ?>
                            <tr>
                                <td>
                                    <div class="item">
                                        <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="" onerror="this.src='../../assets/images/logo.png';">
                                        <div>
                                            <div class="item-name"><?php echo htmlspecialchars($it['name'] ?? ''); ?></div>
                                            <div class="small">ID: <?php echo $pid; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo moneyTaka($price); ?></td>
                                <td>
                                    <form class="qty-form" action="../../controllers/cartController.php?action=update" method="post">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="id" value="<?php echo $pid; ?>">
                                        <input class="qty-input" type="number" min="1" max="99" name="qty" value="<?php echo $qty; ?>">
                                        <button class="btn btn-outline" type="submit">Update</button>
                                    </form>
                                </td>
                                <td><?php echo moneyTaka($sub); ?></td>
                                <td>
                                    <a class="btn btn-danger" href="../../controllers/cartController.php?action=remove&id=<?php echo $pid; ?>" onclick="return confirm('Remove this item?')">Remove</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="actions">
                        <a class="btn btn-outline" href="../../controllers/cartController.php?action=clear" onclick="return confirm('Clear the whole cart?')">Clear Cart</a>
                        <a class="btn btn-outline" href="home.php">Continue Shopping</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2 class="page-title" style="font-size:22px;">Order Summary</h2>
                <div class="summary-row"><span>Subtotal</span><strong><?php echo moneyTaka($total); ?></strong></div>
                <div class="summary-row"><span>Delivery</span><strong><?php echo moneyTaka(empty($cartItems) ? 0 : 80); ?></strong></div>
                <div class="summary-row summary-total"><span>Total</span><span><?php echo moneyTaka($total + (empty($cartItems) ? 0 : 80)); ?></span></div>

                <div class="actions">
                    <?php if(!empty($cartItems)): ?>
                        <a class="btn btn-primary" href="checkout.php">Proceed to Checkout</a>
                    <?php else: ?>
                        <a class="btn btn-primary" href="home.php">Shop Now</a>
                    <?php endif; ?>
                </div>

                <p class="small" style="margin-top:12px;">Need to view orders? <a class="muted-link" href="order_history.php">Order history</a></p>
            </div>
        </div>
    </div>
</section>

<footer class="main-footer">
        <div class="container footer-content-container">
            <div class="footer-column contact-column">
                <p class="contact-detail">Rockib Regnum Center, level-9, Chattogram, Bangladesh</p>
                <p class="contact-detail">📞+8801612975300</p>
                <p class="contact-detail hours">8 am - 10 pm (Everyday)</p>
                <p class="contact-detail">customer.care@bestcart.com</p>
            </div>

            <div class="footer-column link-group">
                <h4 class="footer-heading">BestCart</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">BestCart Blog</a></li>
                    <li><a href="#">Join the Affiliate Program</a></li>
                    <li><a href="#">Cookies Policy</a></li>
                </ul>
            </div>

            <div class="footer-column link-group">
                <h4 class="footer-heading">Customer Care</h4>
                <ul>
                    <li><a href="#">Returns & Refunds</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Warranty Policy</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">EMI Policy</a></li>
                </ul>
            </div>

            <div class="footer-column payment-methods">
                <h4 class="footer-heading">Payment Methods</h4>
                <div class="payment-grid">
                    <img src="../../assets/images/bkash.png" alt="bKash" class="payment-icon" onerror="this.style.display='none'">
                    <img src="../../assets/images/nagad.png" alt="Nagad" class="payment-icon" onerror="this.style.display='none'">
                    <img src="../../assets/images/cod.png" alt="Cash on Delivery" class="payment-icon wide" onerror="this.style.display='none'">
                </div>
            </div>
        </div>

        <div class="footer-copyright">
            © Copyright 2025 BestCart. All Rights Reserved.
        </div>
    </footer>

<script src="../../assets/js/shop.js?v=<?php echo time(); ?>"></script>
</body>
</html>
