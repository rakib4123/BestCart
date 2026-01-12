<?php
require_once '../../controllers/authCheck.php';
require_once '../../controllers/clientHelpers.php';
require_once '../../models/orderModel.php';

$email = $_SESSION['email'] ?? '';
$orders = $email ? getOrdersByEmail($email) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestCart | Order History</title>
    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/cart.css?v=<?php echo time(); ?>">
    <style>
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th,
        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f1f1;
            text-align: left;
        }

        .history-table th {
            color: #444;
        }

        .status-pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #3978ff;
            color: #ffffff;
            font-weight: 600;
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
                <h1 class="page-title">Order History</h1>

                <?php if (empty($orders)): ?>
                    <p class="small">No orders found. <a class="muted-link" href="home.php">Start shopping</a></p>
                <?php else: ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td>#<?php echo (int) $o['id']; ?></td>
                                    <td><?php echo htmlspecialchars($o['order_date']); ?></td>
                                    <td><?php echo moneyTaka($o['total_amount']); ?></td>
                                    <td><span class="status-pill"><?php echo htmlspecialchars($o['status']); ?></span></td>
                                    <td><a class="muted-link"
                                            href="order_invoice.php?id=<?php echo (int) $o['id']; ?>">Invoice</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="actions" style="margin-top:14px;">
                        <a class="btn btn-outline" href="home.php">Back to Home</a>
                    </div>
                <?php endif; ?>
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