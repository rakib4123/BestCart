<?php $page = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar">

    <div class="logo">
        
        <span>BestCart</span>
    </div>

    <div class="nav-links">
        <a href="dashboard.php" class="nav-item <?= $page == 'dashboard.php' ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a>
        <a href="manage_products.php" class="nav-item <?= $page == 'manage_products.php' ? 'active' : '' ?>">
            <i data-lucide="package"></i> Products
        </a>
        <a href="manage_orders.php" class="nav-item <?= $page == 'manage_orders.php' ? 'active' : '' ?>">
            <i data-lucide="shopping-cart"></i> Orders
        </a>
        <a href="manage_users.php" class="nav-item <?= $page == 'manage_users.php' ? 'active' : '' ?>">
            <i data-lucide="users"></i> Users
        </a>
        <a href="manage_sliders.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'manage_sliders.php' ? 'active' : '' ?>">
            <i data-lucide="image"></i> Sliders
        </a>
        <a href="manage_categories.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'manage_categories.php' ? 'active' : '' ?>">
            <i data-lucide="layers"></i> Categories
        </a>
    </div>

    <div>
        <a href="../../controllers/adminAuthController.php?logout=1" class="nav-item logout-btn">
            <i data-lucide="log-out"></i> Logout
        </a>
    </div>
</nav>