<?php
require_once('helpers.php');
requireAdmin();

require_once('../models/categoryModel.php');
require_once('../views/admin/file_handler.php'); 


if (isset($_POST['add_cat'])) {
    require_csrf();
    $name = $_POST['name'] ?? '';
    [$okName, $nameClean] = v_required($name, 2, 80);
    if (!$okName) {
        if (isAjax()) jsonOut(false, "Category name is required");
        header("Location: ../views/admin/manage_categories.php?err=1");
        exit;
    }
    $name = $nameClean;
    $image = uploadImage($_FILES['image'] ?? []);

    $ok = addCategory($name, $image);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Category added");
        jsonOut(false, "Failed to add category");
    } else {
        header("Location: ../views/admin/manage_categories.php");
        exit;
    }
}


if (isset($_POST['update_cat'])) {
    require_csrf();
    $id = (int)($_POST['category_id'] ?? 0);
    $name = $_POST['name'] ?? '';
    [$okName, $nameClean] = v_required($name, 2, 80);
    if (!$okName) {
        if (isAjax()) jsonOut(false, "Category name is required");
        header("Location: ../views/admin/manage_categories.php?err=1");
        exit;
    }
    $name = $nameClean;

    $cat = getCategoryById($id);
    if (!$cat) {
        if (isAjax()) jsonOut(false, "Category not found");
        header("Location: ../views/admin/manage_categories.php");
        exit;
    }

    $image = $cat['image'];
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $image = uploadImage($_FILES['image']);
    }

    $ok = updateCategory($id, $name, $image);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Category updated");
        jsonOut(false, "Failed to update category");
    } else {
        header("Location: ../views/admin/manage_categories.php");
        exit;
    }
}


if (isset($_GET['delete'])) {
    require_csrf();
    $id = (int)$_GET['delete'];
    $ok = deleteCategory($id);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Category deleted", ['id'=>$id]);
        jsonOut(false, "Failed to delete category");
    } else {
        header("Location: ../views/admin/manage_categories.php");
        exit;
    }
}

if (isAjax()) jsonOut(false, "Invalid request");
header("Location: ../views/admin/manage_categories.php");
exit;
?>
