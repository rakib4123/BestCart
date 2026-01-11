<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('../../models/categoryModel.php');

if (isAjax() && (isset($_GET['partial']) && $_GET['partial'] === 'category_rows')) {
    $categories = getAllCategories();
    if (!empty($categories)) {
        foreach ($categories as $c) {
            ?>
            <tr>
                <td>
                    <img src="../../uploads/<?= htmlspecialchars($c['image']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
                </td>
                <td><b><?= htmlspecialchars($c['name']) ?></b></td>
                <td>
                    <a href="edit_category.php?id=<?= $c['id'] ?>" class="btn btn-secondary" style="padding:5px 10px; font-size:0.85rem;">Edit</a>
                    <a
                      href="../../controllers/adminCategoryController.php?delete=<?= $c['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px; font-size:0.85rem;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this category?"
                      data-refresh-target="#categoriesTableBody"
                      data-refresh-url="manage_categories.php?partial=category_rows"
                    >Del</a>
                </td>
            </tr>
            <?php
        }
    }
    exit;
}

$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Categories</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
<script src="../../assets/js/ajax.js?v=<?php echo time(); ?>"></script>
</head>
<body>

    <?php include('menu.php'); ?>

    <div class="container">
        <div class="header-title">Manage Categories</div>

        <div class="card">
            <h4 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Create New Category</h4>
            <form method="post" enctype="multipart/form-data" action="../../controllers/adminCategoryController.php" data-ajax="true" data-reset="true" data-refresh-target="#categoriesTableBody" data-refresh-url="manage_categories.php?partial=category_rows">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-row">
                    <div class="input-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Headphones">
                    </div>
                    <div class="input-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" required style="padding:5px;">
                    </div>
                </div>
                <button type="submit" name="add_cat" class="btn btn-primary">
                    <i data-lucide="plus"></i> Add Category
                </button>
            </form>
        </div>

        <div class="card" style="padding:0; overflow:hidden; margin-top:20px;">
            <div class="table-header" style="padding:15px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <h4>Category List</h4>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                    <?php foreach ($categories as $c) { ?>
                        <tr>
                            <td>
                                <img src="../../uploads/<?= htmlspecialchars($c['image']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
                            </td>
                            <td><b><?= htmlspecialchars($c['name']) ?></b></td>
                            <td>
                                <a href="edit_category.php?id=<?= $c['id'] ?>" class="btn btn-secondary" style="padding:5px 10px; font-size:0.85rem;">Edit</a>
                                <a
                                  href="../../controllers/adminCategoryController.php?delete=<?= $c['id'] ?>"
                                  class="btn btn-danger"
                                  style="padding:5px 10px; font-size:0.85rem;"
                                  data-ajax-link="true"
                                  data-confirm="true"
                                  data-confirm-text="Delete this category?"
                                  data-refresh-target="#categoriesTableBody"
                                  data-refresh-url="manage_categories.php?partial=category_rows"
                                >Del</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>