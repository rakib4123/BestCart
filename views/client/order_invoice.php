<?php
require_once '../../controllers/authCheck.php';
require_once '../../controllers/clientHelpers.php';
require_once '../../models/orderModel.php';

$id = (int) ($_GET['id'] ?? 0);
$order = $id > 0 ? getOrderById($id) : null;
if (!$order || ($order['email'] ?? '') !== ($_SESSION['email'] ?? '')) {
    $_SESSION['flash_error'] = 'Invoice not found.';
    redirectClient('order_history.php');
}

$items = [];
if (!empty($order['order_items'])) {
    $decoded = json_decode($order['order_items'], true);
    if (is_array($decoded))
        $items = $decoded;
}

$shipping = [];
if (!empty($order['shipping_address'])) {
    $sd = json_decode($order['shipping_address'], true);
    if (is_array($sd))
        $shipping = $sd;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestCart | Invoice #<?php echo (int) $order['id']; ?></title>
    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/cart.css?v=<?php echo time(); ?>">
    <style>
        .invoice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inv-table th,
        .inv-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f1f1;
            text-align: left;
        }

        .inv-table th {
            color: #444;
        }
    </style>
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
                <h1 class="page-title">Invoice #<?php echo (int) $order['id']; ?></h1>

                <div class="invoice-grid">
                    <div>
                        <p class="small"><strong>Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?>
                        </p>
                        <p class="small"><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                        <p class="small"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    </div>
                    <div>
                        <p class="small"><strong>Shipping:</strong></p>
                        <p class="small">
                            <?php echo htmlspecialchars(($shipping['name'] ?? $order['customer_name']) ?: ''); ?></p>
                        <p class="small"><?php echo htmlspecialchars(($shipping['phone'] ?? '') ?: ''); ?></p>
                        <p class="small"><?php echo htmlspecialchars(($shipping['address'] ?? '') ?: ''); ?></p>
                        <p class="small"><?php echo htmlspecialchars(($shipping['city'] ?? '') ?: ''); ?>
                            <?php echo htmlspecialchars(($shipping['postal'] ?? '') ?: ''); ?></p>
                    </div>
                </div>

                <h2 class="page-title" style="font-size:22px;margin-top:16px;">Items</h2>
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subTotal = 0.0;
                        foreach ($items as $it):
                            $price = (float) ($it['price'] ?? 0);
                            $qty = (int) ($it['qty'] ?? 0);
                            $line = $price * $qty;
                            $subTotal += $line;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($it['name'] ?? ($it['title'] ?? '')); ?></td>
                                <td><?php echo moneyTaka($price); ?></td>
                                <td><?php echo $qty; ?></td>
                                <td><?php echo moneyTaka($line); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="max-width:420px;margin-left:auto;margin-top:14px;">
                    <div class="summary-row"><span>Subtotal</span><strong><?php echo moneyTaka($subTotal); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Delivery</span><strong><?php echo moneyTaka(max(0, (float) $order['total_amount'] - $subTotal)); ?></strong>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span><span><?php echo moneyTaka($order['total_amount']); ?></span></div>
                </div>

                <div class="actions" style="margin-top:16px;">
                    <a class="btn btn-outline" href="order_history.php">Back</a>
                    <a class="btn btn-primary" href="home.php">Shop More</a>
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
</body>

</html>