<?php
// Client-side helpers (separate from controllers/helpers.php which is admin-focused)

function ensureSessionStarted(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function csrfToken(): string {
    ensureSessionStarted();
    if (empty($_SESSION['csrf_token_client'])) {
        $_SESSION['csrf_token_client'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token_client'];
}

function csrfField(): string {
    $t = htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="'.$t.'">';
}

function csrfValidate(?string $token): bool {
    ensureSessionStarted();
    if (empty($_SESSION['csrf_token_client'])) return false;
    if (!$token) return false;
    return hash_equals($_SESSION['csrf_token_client'], $token);
}

function redirectClient(string $path): void {
    header('Location: '.$path);
    exit();
}

function moneyTaka($amount): string {
    $n = number_format((float)$amount, 2, '.', ',');
    return '৳'.$n;
}
