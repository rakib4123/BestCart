<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('../../models/productModel.php');
require_once('../../models/categoryModel.php');

// Partial AJAX refresh: return only table rows (no full page reload)
if (isAjax() && (isset($_GET['partial']) && $_GET['partial'] === 'product_rows')) {
    $products = getAllProducts();
    if (!empty($products)) {
        foreach ($products as $p) {
            ?>
            <tr>
                <td>
                    <img src="../../uploads/<?= htmlspecialchars($p['image']) ?>" class="thumb">
                </td>
                <td>
                    <b><?= htmlspecialchars($p['name']) ?></b><br>
                    <span style="background:#e0f2fe; color:#0369a1; font-size:0.75rem; padding:2px 6px; border-radius:4px;">
                        <?= isset($p['category']) ? htmlspecialchars($p['category']) : 'Uncategorized' ?>
                    </span>
                </td>
                <td>
                    <?php if ($p['discount_price'] > 0) { ?>
                        <span style="text-decoration:line-through; color:#aaa;">৳<?= $p['price'] ?></span><br>
                        <b style="color:#10b981;">৳<?= $p['discount_price'] ?></b>
                    <?php } else { ?>
                        ৳<?= $p['price'] ?>
                    <?php } ?>
                </td>
                <td><?= $p['quantity'] ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="padding:5px 10px;">Edit</a>
                    <a
                      href="../../controllers/adminProductController.php?delete=<?= $p['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this product?"
                      data-refresh-target="#productsTableBody"
                      data-refresh-url="manage_products.php?partial=product_rows"
                    >Del</a>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>No Products Found</td></tr>";
    }
    exit;
}

require_once('layout.php');

// --- FETCH DATA ---
$products = getAllProducts();
$categories = getAllCategories();
?>

<div class="header-title">Manage Products</div>

<div class="card">
    <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Add New Product</h3>
    <form method="post" enctype="multipart/form-data" action="../../controllers/adminProductController.php" data-ajax="true" data-reset="true" data-refresh-target="#productsTableBody" data-refresh-url="manage_products.php?partial=product_rows">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="input-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="input-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <?php foreach($categories as $c){ ?>
                        <option value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-row">
             <div class="input-group" style="grid-column: span 2;">
                <label>Description</label>
                <textarea name="desc" class="form-control" placeholder="Product details..."></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="input-group">
                <label>Original Price (৳)</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="input-group">
                <label>Discount Price (৳)</label>
                <input type="number" step="0.01" name="discount" class="form-control" value="0.00">
            </div>
        </div>
        <div class="form-row">
            <div class="input-group">
                <label>Stock Quantity</label>
                <input type="number" name="qty" class="form-control" required>
            </div>
            <div class="input-group">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control" style="padding: 7px;">
            </div>
        </div>
        <button type="submit" name="add_product_btn" class="btn btn-primary">Add Product</button>
    </form>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    
    <div class="table-header" style="padding:15px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
        <h4 style="margin:0;">Product List</h4>
        <input type="text" id="productSearchInput" class="form-control" placeholder="Search..." onkeyup="filterProducts()" style="width:250px;">
    </div>

    <table id="productsTable">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name & Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <?php if(!empty($products)){
                foreach($products as $p){ ?>
                <tr>
                    <td>
                        <img src="../../uploads/<?= htmlspecialchars($p['image']) ?>" class="thumb">
                    </td>
                    <td>
                        <b><?= htmlspecialchars($p['name']) ?></b><br>
                        <span style="background:#e0f2fe; color:#0369a1; font-size:0.75rem; padding:2px 6px; border-radius:4px;">
                            <?= isset($p['category']) ? htmlspecialchars($p['category']) : 'Uncategorized' ?>
                        </span>
                    </td>
                    <td>
                        <?php if($p['discount_price'] > 0){ ?>
                            <span style="text-decoration:line-through; color:#aaa;">৳<?= $p['price'] ?></span><br>
                            <b style="color:#10b981;">৳<?= $p['discount_price'] ?></b>
                        <?php } else { ?>
                            ৳<?= $p['price'] ?>
                        <?php } ?>
                    </td>
                    <td><?= $p['quantity'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-secondary" style="padding:5px 10px;">Edit</a>
                        <a
                          href="../../controllers/adminProductController.php?delete=<?= $p['id'] ?>"
                          class="btn btn-danger"
                          style="padding:5px 10px;"
                          data-ajax-link="true"
                          data-confirm="true"
                          data-confirm-text="Delete this product?"
                          data-refresh-target="#productsTableBody"
                          data-refresh-url="manage_products.php?partial=product_rows"
                        >Del</a>
                    </td>
                </tr>
            <?php } } else {
                echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>No Products Found</td></tr>";
            } ?>
        </tbody>
    </table>
</div>

<?php require_once('footer.php'); ?>

<script>
    function filterProducts() {
        var input = document.getElementById("productSearchInput");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("productsTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var tdName = tr[i].getElementsByTagName("td")[1]; // Column 1: Name
            if (tdName) {
                var txtValue = tdName.textContent || tdName.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; 
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>