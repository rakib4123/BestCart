<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestCart | Homepage</title>

    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="home.php" class="logo">
                <img src="../../assets/images/logo.png" alt="BestCart">
            </a>

            <div class="categories-dropdown">
                <a href="categories.php">☰ Categories</a>
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
                <a id="profileBtn" href="login.php" class="nav-btn">🙎🏻 Sign In</a>
            </div>
        </div>
    </header>

    <section class="banner-wrap">
        <div class="container">
            <section class="banner">
                <div id="slider-container">
                    <img id="slider" src="" alt="Banner Image">
                    <button id="prevBtn" type="button">⟨</button>
                    <button id="nextBtn" type="button">⟩</button>
                </div>
            </section>
        </div>
    </section>


    <section id="category-section" class="categories-container">
        <h1 class="section-title">Categories</h1>
        <div id="category-grid" class="category-grid-circle"></div>
    </section>


    <section id="featured-products" class="featured-products-container">
        <h1 class="section-title">Featured Products</h1>
        <div id="featured-grid" class="featured-grid"></div>
        <button id="load-more-btn" class="load-more-btn">Load More</button>
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
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>;

        const profileBtn = document.getElementById("profileBtn");

        if (isLoggedIn) {
            profileBtn.innerText = "🙎🏻‍♂️ Profile";
            profileBtn.href = "../../controllers/loginHomeController.php";
        } else {
            profileBtn.innerText = "🙎🏻 Sign In";
            profileBtn.href = "login.php";
        }
    </script>
</body>
</html>