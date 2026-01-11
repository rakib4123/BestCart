<?php
require_once('helpers.php');
requireAdmin();

require_once('../models/userModel.php');

// ADD USER
if (isset($_POST['add_user_btn'])) {
    require_csrf();
    $data = [
        'username' => $_POST['username'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'role' => $_POST['role'] ?? 'customer'
    ];

    
    // PHP validation
    [$okU, $u] = v_required($data['username'], 2, 50);
    if (!$okU) { if (isAjax()) jsonOut(false, "Username is required"); header("Location: ../views/admin/manage_users.php?err=1"); exit; }
    [$okE, $e] = v_email($data['email']);
    if (!$okE) { if (isAjax()) jsonOut(false, "Valid email is required"); header("Location: ../views/admin/manage_users.php?err=1"); exit; }
    [$okP, $p] = v_required($data['password'], 6, 200);
    if (!$okP) { if (isAjax()) jsonOut(false, "Password must be at least 6 characters"); header("Location: ../views/admin/manage_users.php?err=1"); exit; }
    $role = strtolower(trim((string)$data['role']));
    if (!in_array($role, ['admin','customer'], true)) $role = 'customer';
    $data['username'] = $u; $data['email'] = $e; $data['password'] = $p; $data['role'] = $role;

$ok = addUser($data['username'], $data['email'], $data['password'], $data['role']);

    if (isAjax()) {
        if ($ok) jsonOut(true, "User added");
        jsonOut(false, "Failed to add user");
    } else {
        header("Location: ../views/admin/manage_users.php");
        exit;
    }
}

// UPDATE USER
if (isset($_POST['update_user'])) {
    require_csrf();
    $id = (int)($_POST['user_id'] ?? 0);
    $data = [
        'id' => $id,
        'username' => $_POST['username'] ?? '',
        'email' => $_POST['email'] ?? '',
        'role' => $_POST['role'] ?? 'customer'
    ];

    
    // PHP validation
    [$okId, $id] = v_int($data['id'], 1, null);
    if (!$okId) { if (isAjax()) jsonOut(false, "Invalid user"); header("Location: ../views/admin/manage_users.php?err=1"); exit; }
    [$okU, $u] = v_required($data['username'], 2, 50);
    if (!$okU) { if (isAjax()) jsonOut(false, "Username is required"); header("Location: ../views/admin/manage_users.php?err=1"); exit; }
    [$okE, $e] = v_email($data['email']);
    if (!$okE) { if (isAjax()) jsonOut(false, "Valid email is required"); header("Location: ../views/admin/manage_users.php?err=1"); exit; }
    $role = strtolower(trim((string)$data['role']));
    if (!in_array($role, ['admin','customer'], true)) $role = 'customer';
    $data['id'] = $id; $data['username'] = $u; $data['email'] = $e; $data['role'] = $role;

$ok = updateUser($data);

    if (isAjax()) {
        if ($ok) jsonOut(true, "User updated");
        jsonOut(false, "Failed to update user");
    } else {
        header("Location: ../views/admin/manage_users.php");
        exit;
    }
}

// DELETE USER
if (isset($_GET['delete'])) {
    require_csrf();
    $id = (int)$_GET['delete'];
    $ok = deleteUser($id);

    if (isAjax()) {
        if ($ok) jsonOut(true, "User deleted", ['id'=>$id]);
        jsonOut(false, "Failed to delete user");
    } else {
        header("Location: ../views/admin/manage_users.php");
        exit;
    }
}

if (isAjax()) jsonOut(false, "Invalid request");
header("Location: ../views/admin/manage_users.php");
exit;
?>
