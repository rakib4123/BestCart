<?php
// controllers/helpers.php

/**
 * Basic security bootstrap for the admin area:
 * - secure session cookie flags (HttpOnly, SameSite, Secure if HTTPS)
 * - security headers
 * - CSRF helpers
 * - AJAX-safe JSON responses
 */

// CHANGE THIS in production (keep it secret)
if (!defined('BESTCART_APP_SECRET')) {
    define('BESTCART_APP_SECRET', 'change-this-secret-key-32+chars');
}

/** Start a hardened session (call before any output) */
function startSecureSession(){
    if (session_status() === PHP_SESSION_ACTIVE) return;

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    // Security-focused session settings
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0');

    // PHP 7.3+ supports array options
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    session_start();
}

/** Basic security headers for admin pages */
function setSecurityHeaders(){
    // Avoid duplicate header warnings
    if (headers_sent()) return;

    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('X-XSS-Protection: 0'); // modern browsers ignore; keep explicit

    // Minimal CSP: allow self + lucide CDN script (used in layout.php)
    // Note: if you remove lucide CDN, you can tighten CSP further.
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' https://unpkg.com 'unsafe-inline';");
}

/**
 * Detect AJAX (fetch) request.
 */
function isAjax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

/**
 * Output JSON and stop execution.
 */
function jsonOut($status, $message, $data = []){
    // Clean any accidental buffered output so JSON stays valid
    while (ob_get_level() > 0) { @ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>$status, 'message'=>$message, 'data'=>$data]);
    exit;
}

/**
 * For AJAX calls, prevent PHP notices/warnings/fatals from breaking JSON.
 * - Converts warnings/notices into JSON
 * - Converts fatal shutdown errors into JSON
 */
function enableAjaxSafeErrors(){
    if (!isAjax()) { return; }

    // Buffer any accidental output (warnings/echo)
    if (!ob_get_level()) { ob_start(); }

    // Convert notices/warnings to JSON
    set_error_handler(function($errno, $errstr, $errfile, $errline){
        if (!(error_reporting() & $errno)) { return false; }

        while (ob_get_level() > 0) { @ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'  => false,
            'message' => 'PHP warning/notice: '.$errstr,
            'data'    => ['file'=>$errfile, 'line'=>$errline]
        ]);
        exit;
    });

    // Convert uncaught exceptions to JSON
    set_exception_handler(function($ex){
        while (ob_get_level() > 0) { @ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status'  => false,
            'message' => 'Uncaught exception: '.$ex->getMessage(),
            'data'    => ['file'=>$ex->getFile(), 'line'=>$ex->getLine()]
        ]);
        exit;
    });

    // Catch fatal errors at shutdown
    register_shutdown_function(function(){
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            while (ob_get_level() > 0) { @ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status'  => false,
                'message' => 'Fatal error: '.$err['message'],
                'data'    => ['file'=>$err['file'], 'line'=>$err['line']]
            ]);
            exit;
        }
    });
}

/** HTML escape helper (for XSS prevention in views) */
function e($str){
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** CSRF token: create/get */
function csrf_token(){
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** CSRF verify (POST or X-CSRF-Token header) */
function verify_csrf(){
    startSecureSession();
    $token = $_POST['csrf_token'] ?? '';
    if (!$token && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/** Require CSRF for state-changing requests */
function require_csrf(){
    if (!verify_csrf()) {
        if (isAjax()) jsonOut(false, "Invalid CSRF token");
        http_response_code(403);
        echo "Invalid CSRF token";
        exit;
    }
}

/** Cookie-based remember-me (signed) */
function remember_cookie_set($username, $days = 7){
    $exp = time() + ($days * 86400);
    $payload = $username.'|'.$exp;
    $sig = hash_hmac('sha256', $payload, BESTCART_APP_SECRET);
    $value = base64_encode($payload.'|'.$sig);

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    setcookie('bestcart_admin', $value, [
        'expires'  => $exp,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/** Clear remember-me cookie */
function remember_cookie_clear(){
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    setcookie('bestcart_admin', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/** Attempt auto-login from remember cookie */
function tryAutoLoginFromCookie(){
    startSecureSession();

    if (isset($_SESSION['admin_status']) && $_SESSION['admin_status'] === true) return true;
    if (empty($_COOKIE['bestcart_admin'])) return false;

    $raw = base64_decode($_COOKIE['bestcart_admin'], true);
    if (!$raw) return false;

    $parts = explode('|', $raw);
    if (count($parts) !== 3) return false;

    [$username, $exp, $sig] = $parts;
    if (!ctype_digit($exp) || (int)$exp < time()) return false;

    $payload = $username.'|'.$exp;
    $expected = hash_hmac('sha256', $payload, BESTCART_APP_SECRET);
    if (!hash_equals($expected, $sig)) return false;

    // Cookie is valid -> restore admin session
    $_SESSION['admin_status'] = true;
    $_SESSION['admin_user'] = $username;
    return true;
}

/**
 * Require admin session.
 */
function requireAdmin(){
    startSecureSession();
    // Try cookie restore first
    tryAutoLoginFromCookie();

    if (!isset($_SESSION['admin_status'])) {
        if (isAjax()) jsonOut(false, "Unauthorized");

        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        // If the current script is in controllers/, go up to views/admin/
        $login = (strpos($script, '/controllers/') !== false) ? '../views/admin/login.php' : 'login.php';
        header("Location: $login");
        exit;
    }
}

/** Simple server-side validators (PHP validation) */
function v_required($val, $minLen = 1, $maxLen = 255){
    $s = trim((string)$val);
    if (strlen($s) < $minLen) return [false, "Required field is too short"];
    if (strlen($s) > $maxLen) return [false, "Value is too long"];
    return [true, $s];
}
function v_email($val){
    $s = trim((string)$val);
    if (!filter_var($s, FILTER_VALIDATE_EMAIL)) return [false, "Invalid email"];
    return [true, $s];
}
function v_int($val, $min = null, $max = null){
    if ($val === '' || $val === null) $val = 0;
    if (filter_var($val, FILTER_VALIDATE_INT) === false) return [false, "Invalid integer"];
    $i = (int)$val;
    if ($min !== null && $i < $min) return [false, "Must be >= $min"];
    if ($max !== null && $i > $max) return [false, "Must be <= $max"];
    return [true, $i];
}
function v_float($val, $min = null, $max = null){
    if ($val === '' || $val === null) $val = 0;
    if (filter_var($val, FILTER_VALIDATE_FLOAT) === false && !is_numeric($val)) return [false, "Invalid number"];
    $f = (float)$val;
    if ($min !== null && $f < $min) return [false, "Must be >= $min"];
    if ($max !== null && $f > $max) return [false, "Must be <= $max"];
    return [true, $f];
}

// Enable AJAX-safe error responses early
startSecureSession();
enableAjaxSafeErrors();
?>