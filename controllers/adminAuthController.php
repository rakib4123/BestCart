<?php
require_once('helpers.php');
require_once('../models/userModel.php');

// LOGIN (AJAX form posts here)
if (isset($_POST['submit'])) {
    require_csrf();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // PHP validation
    [$okU, $u] = v_required($username, 2, 50);
    if (!$okU) { if (isAjax()) jsonOut(false, "Username is required"); header("Location: ../views/admin/login.php?err=1"); exit; }

    [$okP, $p] = v_required($password, 1, 200);
    if (!$okP) { if (isAjax()) jsonOut(false, "Password is required"); header("Location: ../views/admin/login.php?err=1"); exit; }

    // Prefer DB-based admin users (role = admin)
    $user = getUserByUsername($u);
    $isAdmin = $user && isset($user['role']) && strtolower($user['role']) === 'admin';

    $passOk = false;
    if ($isAdmin) {
        $stored = $user['password'] ?? '';
        if (is_string($stored) && (substr($stored,0,4)==='$2y$' || substr($stored,0,6)==='$argon')) {
            $passOk = password_verify($p, $stored);
        } else {
            // fallback if old data stored in plain text (not recommended)
            $passOk = hash_equals((string)$stored, (string)$p);
        }
    }

    // Backward-compatible demo credentials (you can remove this later)
    if (!$passOk && $u === "admin" && $p === "password") {
        $isAdmin = true;
        $passOk = true;
    }

    if ($isAdmin && $passOk) {
        session_regenerate_id(true);
        $_SESSION['admin_status'] = true;
        $_SESSION['admin_user'] = $u;

        // Cookie (remember-me)
        if (!empty($_POST['remember'])) {
            remember_cookie_set($u, 7);
        } else {
            remember_cookie_clear();
        }

        if (isAjax()) {
            jsonOut(true, "Login successful", ['redirect'=>'dashboard.php']);
            exit;
        }
        header("Location: ../views/admin/dashboard.php");
        exit;
    }

    if (isAjax()) {
        jsonOut(false, "Invalid credentials");
        exit;
    }
    header("Location: ../views/admin/login.php?err=1");
    exit;
}

// LOGOUT
if (isset($_GET['logout'])) {
    startSecureSession();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    remember_cookie_clear();

    if (isAjax()) {
        jsonOut(true, "Logged out", ['redirect'=>'../views/client/login.php']);
        exit;
    }
    header("Location: ../views/client/login.php");
    exit;
}

if (isAjax()) {
    jsonOut(false, "Invalid request");
    exit;
}
header("Location: ../views/client/login.php");
exit;
?>