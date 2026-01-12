<?php
session_start();

if (!isset($_SESSION['email'])) {
    // Build an absolute path to avoid "views/views" 404 when this guard is included from inside /views/*
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    // Remove trailing /views/... or /controllers/... or /api/... to get project base
    $base = preg_replace('~/(views|controllers|api)(/.*)?$~', '', $script);
    if ($base === '' || $base === null) { $base = '/'; }
    $loginUrl = rtrim($base, '/') . '/views/client/login.php';
    header("Location: " . $loginUrl);
    exit();
}
