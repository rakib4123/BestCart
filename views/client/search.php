<?php
session_start();

$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | BestCart</title>

    <link rel="stylesheet" href="../../assets/css/home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/search.css?v=<?php echo time(); ?>">
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
                    <input type="text" name="query" placeholder="Search products..." value="<?= $query ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="nav-actions">
                <a href="cart.php" class="nav-btn">🛒 Cart</a>
                <a id="profileBtn" href="login.php" class="nav-btn">🙎🏻‍♂️ Sign In</a>
            </div>
        </div>
    </header>

    <div class="search-results-container">
        <h1 class="page-title">
        <?php if($category): ?>
            Category: "<?= $category ?>"
        <?php else: ?>
            Search Results for: "<?= $query ?>"
        <?php endif; ?>
        </h1>

        <div class="filter-bar">
            <div class="filter-box">
                <label>Min ৳</label>
                <input type="number" id="minPrice" min="0" placeholder="0">
            </div>

            <div class="filter-box">
                <label>Max ৳</label>
                <input type="number" id="maxPrice" min="0" placeholder="Any">
            </div>

            <div class="filter-box check">
                <input type="checkbox" id="inStockOnly">
                <label for="inStockOnly">In Stock Only</label>
            </div>

            <div class="filter-box">
                <label>Sort</label>
                <select id="sortPrice">
                    <option value="">Default</option>
                    <option value="low">Low → High</option>
                    <option value="high">High → Low</option>
                </select>
            </div>

            <button type="button" id="resetFilterBtn" class="filter-reset">Reset</button>
        </div>

        <div id="search-grid" class="featured-grid search-grid">
            <h3>Searching...</h3>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container footer-content-container">
            <div class="footer-column contact-column">
                <p class="contact-detail">Rahman Regnum Centre, Level-6, 191/1 Tejgaon C/A, Dhaka-1208, Bangladesh</p>
                <p class="contact-detail">📞 +8809613444455</p>
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
                    <img src="../../assets/images/bkash.png" alt="bKash" class="payment-icon"
                        onerror="this.style.display='none'">
                    <img src="../../assets/images/nagad.png" alt="Nagad" class="payment-icon"
                        onerror="this.style.display='none'">
                    <img src="../../assets/images/cod.png" alt="Cash on Delivery" class="payment-icon wide"
                        onerror="this.style.display='none'">
                </div>
            </div>
        </div>

        <div class="footer-copyright">
            © Copyright 2025 BestCart. All Rights Reserved.
        </div>
    </footer>

    <script>
        window.IS_LOGGED_IN = <?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>;
    </script>

    <script src="../../assets/js/shop.js?v=<?php echo time(); ?>"></script>
    <script src="../../assets/js/search.js?v=<?php echo time(); ?>"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
    fetchCategories();

    const q = "<?= $query ?>";
    const cat = "<?= $category ?>";

    if (cat) {
        runSearch(cat, true);
    } else if (q) {
        runSearch(q, false);
    } else {
        document.getElementById('search-grid').innerHTML = "<p>Please enter a keyword to search.</p>";
    }
    });
    </script>
</body>
</html>