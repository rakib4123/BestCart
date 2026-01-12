<?php
require_once('../../controllers/helpers.php');
requireAdmin();
require_once('layout.php');
require_once('../../models/userModel.php');


if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    header("location: manage_users.php");
    exit();
}

$id = (int)$_GET['id'];
$user = getUserById($id);

if (!$user) {
    header("location: manage_users.php");
    exit();
}


$roleVal = strtolower(trim($user['role'] ?? ''));
$isAdmin = ($roleVal === 'admin');
?>
<div class="header-title">Edit User</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h3 style="margin-bottom:20px;">Edit Profile: <?= htmlspecialchars($user['username']) ?></h3>

    <form method="post" action="../../controllers/adminUserController.php" data-ajax="true">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

        <div class="form-row">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Role</label>

                <!-- IMPORTANT FIX:
                     Use lowercase values so manage_users.php (which checks strtolower(trim(role)) === 'admin')
                     always works, even if DB previously had "Admin"/"User".
                -->
                <select name="role" class="form-control">
                    <option value="customer" <?= !$isAdmin ? 'selected' : '' ?>>Customer</option>
                    <option value="admin" <?= $isAdmin ? 'selected' : '' ?>>Admin</option>
                </select>

                <small style="color:#64748b; margin-top:5px;">
                    <b>Warning:</b> 'Admin' role gives full access to this dashboard.
                </small>
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" name="update_user" class="btn btn-primary" style="flex:1;">
                <i data-lucide="save"></i> Save Changes
            </button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once('footer.php'); ?>
