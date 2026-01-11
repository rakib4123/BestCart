<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('../../models/sliderModel.php');

if (isAjax() && (isset($_GET['partial']) && $_GET['partial'] === 'slider_rows')) {
    $sliders = getAllSliders();
    if (!empty($sliders)) {
        foreach ($sliders as $s) {
            ?>
            <tr>
                <td>
                    <img src="../../uploads/<?= htmlspecialchars($s['image']) ?>" style="width:120px; height:60px; object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                </td>
                <td>
                    <b style="font-size:1.1rem; color:#333;"><?= htmlspecialchars($s['title']) ?></b><br>
                    <span style="color:#64748b;"><?= htmlspecialchars($s['subtitle']) ?></span>
                </td>
                <td>
                    <a
                      href="../../controllers/adminSliderController.php?delete=<?= $s['id'] ?>"
                      class="btn btn-danger"
                      style="padding:5px 10px; font-size:0.85rem;"
                      data-ajax-link="true"
                      data-confirm="true"
                      data-confirm-text="Delete this slide?"
                      data-refresh-target="#slidersTableBody"
                      data-refresh-url="manage_sliders.php?partial=slider_rows"
                    >
                        <i data-lucide="trash-2" style="width:16px;"></i> Delete
                    </a>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='3' style='text-align:center; padding:20px;'>No sliders found.</td></tr>";
    }
    exit;
}

$sliders = getAllSliders();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Sliders</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
<script src="../../assets/js/ajax.js?v=<?php echo time(); ?>"></script>
</head>

<body>

    <?php include('menu.php'); ?>

    <div class="container">

        <div class="header-title">Manage Home Sliders</div>

        <div class="card">
            <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Add New Slide</h3>
            <form method="post" enctype="multipart/form-data" action="../../controllers/adminSliderController.php" data-ajax="true" data-reset="true" data-refresh-target="#slidersTableBody" data-refresh-url="manage_sliders.php?partial=slider_rows">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-row">
                    <div class="input-group">
                        <label>Title (Big Text)</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Super Sale" required>
                    </div>
                    <div class="input-group">
                        <label>Subtitle (Small Text)</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="e.g. Up to 50% Off">
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label>Slider Image</label>
                        <input type="file" name="image" class="form-control" required style="padding:5px;">
                    </div>
                </div>
                <button type="submit" name="add_slider" class="btn btn-primary">
                    <i data-lucide="plus-circle"></i> Add Slide
                </button>
            </form>
        </div>

        <div class="card" style="padding:0; overflow:hidden; margin-top:20px;">
            <div style="padding:15px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <h4 style="margin:0;">Active Sliders</h4>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title & Subtitle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="slidersTableBody">
                    <?php foreach ($sliders as $s) { ?>
                        <tr>
                            <td>
                                <img src="../../uploads/<?= htmlspecialchars($s['image']) ?>" style="width:120px; height:60px; object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                            </td>
                            <td>
                                <b style="font-size:1.1rem; color:#333;"><?= htmlspecialchars($s['title']) ?></b><br>
                                <span style="color:#64748b;"><?= htmlspecialchars($s['subtitle']) ?></span>
                            </td>
                            <td>
                                <a
                                  href="../../controllers/adminSliderController.php?delete=<?= $s['id'] ?>"
                                  class="btn btn-danger"
                                  style="padding:5px 10px; font-size:0.85rem;"
                                  data-ajax-link="true"
                                  data-confirm="true"
                                  data-confirm-text="Delete this slide?"
                                  data-refresh-target="#slidersTableBody"
                                  data-refresh-url="manage_sliders.php?partial=slider_rows"
                                >
                                    <i data-lucide="trash-2" style="width:16px;"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (empty($sliders)) { ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding:20px;">No sliders found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>