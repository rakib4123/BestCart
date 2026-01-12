<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details | BestCart</title>
    <link rel="stylesheet" href="../../assets/css/home.css">
    <link rel="stylesheet" href="../../assets/css/p_details.css">
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

            <div class="nav-actions">
                <a href="cart.php" class="nav-btn">🛒 Cart</a>
                <a href="profile.php" class="nav-btn">🙎🏻‍♂️ Profile</a>
            </div>
        </div>
    </header>

    <div id="details-wrapper" class="details-container">
        <h2 style="text-align:center; width:100%; color:#666;">Loading Product Info...</h2>
    </div>

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
                    <img src="../../assets/images/bkash.jpg" alt="bKash" class="payment-icon" onerror="this.style.display='none'">
                    <img src="../../assets/images/nagad.png" alt="Nagad" class="payment-icon" onerror="this.style.display='none'">
                    <img src="../../assets/images/cod.jpg" alt="Cash on Delivery" class="payment-icon wide" onerror="this.style.display='none'">
                </div>
            </div>
        </div>

        <div class="footer-copyright">
            © Copyright 2025 BestCart. All Rights Reserved.
        </div>
    </footer>

    <script src="../../assets/js/shop.js"></script>
    <script src="../../assets/js/p_details.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetchCategories(); 

            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if(productId) {
                loadProductDetails(productId);
            } else {
                document.getElementById('details-wrapper').innerHTML = "<h3 style='color:red; text-align:center;'>Product ID Missing</h3>";
            }
        });
    </script>
</body>
</html>
