<?php
require_once('helpers.php');
requireAdmin();

require_once('../models/sliderModel.php');
require_once('../views/admin/file_handler.php'); 


if (isset($_POST['add_slider'])) {
    require_csrf();
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';

    
    [$okT, $title] = v_required($title, 2, 80);
    if (!$okT) { if (isAjax()) jsonOut(false, "Title is required"); header("Location: ../views/admin/manage_sliders.php?err=1"); exit; }
    $subtitle = trim((string)$subtitle);
    if (strlen($subtitle) > 120) { if (isAjax()) jsonOut(false, "Subtitle too long"); header("Location: ../views/admin/manage_sliders.php?err=1"); exit; }
    $image = uploadImage($_FILES['image'] ?? []);

    $ok = addSlider($title, $subtitle, $image);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Slider added");
        jsonOut(false, "Failed to add slider");
    } else {
        header("Location: ../views/admin/manage_sliders.php");
        exit;
    }
}


if (isset($_GET['delete'])) {
    require_csrf();
    $id = (int)$_GET['delete'];
    $ok = deleteSlider($id);

    if (isAjax()) {
        if ($ok) jsonOut(true, "Slider deleted", ['id'=>$id]);
        jsonOut(false, "Failed to delete slider");
    } else {
        header("Location: ../views/admin/manage_sliders.php");
        exit;
    }
}

if (isAjax()) jsonOut(false, "Invalid request");
header("Location: ../views/admin/manage_sliders.php");
exit;
?>
