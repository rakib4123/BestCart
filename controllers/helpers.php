<?php
// controllers/helpers.php


if (!defined('BESTCART_APP_SECRET')) {
    define('BESTCART_APP_SECRET', 'change-this-secret-key-32+chars');
}


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


function setSecurityHeaders(){
    
    if (headers_sent()) return;

    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('X-XSS-Protection: 0'); 

    
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' https://unpkg.com 'unsafe-inline';");
}


function isAjax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}


function jsonOut($status, $message, $data = []){
    // Clean any accidental buffered output so JSON stays valid
    while (ob_get_level() > 0) { @ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>$status, 'message'=>$message, 'data'=>$data]);
    exit;
}


function enableAjaxSafeErrors(){
    if (!isAjax()) { return; }

    
    if (!ob_get_level()) { ob_start(); }

    
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


function e($str){
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


function csrf_token(){
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function verify_csrf(){
    startSecureSession();
    $token = $_POST['csrf_token'] ?? '';
    if (!$token && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}


function require_csrf(){
    if (!verify_csrf()) {
        if (isAjax()) jsonOut(false, "Invalid CSRF token");
        http_response_code(403);
        echo "Invalid CSRF token";
        exit;
    }
}


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

  
    $_SESSION['admin_status'] = true;
    $_SESSION['admin_user'] = $username;
    return true;
}



function requireAdmin(){
    startSecureSession();
    
    tryAutoLoginFromCookie();

    if (!isset($_SESSION['admin_status'])) {
        if (isAjax()) jsonOut(false, "Unauthorized");

        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        
        $login = (strpos($script, '/controllers/') !== false) ? '../views/admin/login.php' : 'login.php';
        header("Location: $login");
        exit;
    }
}


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


startSecureSession();
enableAjaxSafeErrors();
?>