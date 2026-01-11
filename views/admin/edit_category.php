<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('../../models/categoryModel.php');

    if (!isset($_GET['id'])) {
        header("location: manage_categories.php");
        exit();
    }

    $id = $_GET['id'];
    $cat = getCategoryById($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Category</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
<script src="../../assets/js/ajax.js?v=<?php echo time(); ?>"></script>
</head>
<body>

    <?php include('menu.php'); ?>

    <div class="container">
        <div class="header-title">Edit Category</div>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <form method="post" enctype="multipart/form-data" action="../../controllers/adminCategoryController.php" data-ajax="true">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">

                
                <div style="text-align:center; margin-bottom:20px;">
                    <label>Current Image:</label><br>
                    <img src="../../uploads/<?= htmlspecialchars($cat['image']) ?>" style="width:100px; height:100px; object-fit:cover; border-radius:10px; border:1px solid #ddd; margin-top:5px;">
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label>New Image (Optional)</label>
                        <input type="file" name="image" class="form-control" style="padding:5px;">
                        <small style="color:#64748b;">Leave empty to keep current image.</small>
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" name="update_cat" class="btn btn-primary" style="flex:1">
                        <i data-lucide="save"></i> Save Changes
                    </button>
                    <a href="manage_categories.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>