<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

/**
 * Secure session initialization with proper cookie flags
 * - HttpOnly: Prevents JavaScript access (XSS protection)
 * - Secure: Only sent over HTTPS (auto-detected)
 * - SameSite: Strict prevents CSRF via cross-site requests
 * @return void
 */
function session_start_secure(): void
{
    $is_https = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'
        || !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';

    $cookie_params = [
        'lifetime' => 60 * 60 * 2,           // 2 hours
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Strict'
    ];

    if ($is_https) {
        $cookie_params['secure'] = true;
    }

    session_set_cookie_params($cookie_params);
    session_start();
}

/**
 * Regenerate session ID after authentication
 * Prevents session fixation attacks
 * Must be called after successful login
 * @return void
 */
function session_regenerate_id_safe(): void
{
    if (isset($_SESSION['user_id']) && !isset($_SESSION['id_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['id_regenerated'] = true;
    }
}

/**
 * Check if session has timed out due to inactivity
 * @param int $max_inactive Maximum inactive seconds (default: 1800 = 30 minutes)
 * @return bool true = timed out, false = active session
 */
function check_session_timeout(int $max_inactive = 1800): bool
{
    if (!isset($_SESSION['last_activity'])) {
        return true; // No activity recorded, treat as timed out
    }

    $elapsed = time() - $_SESSION['last_activity'];
    return $elapsed > $max_inactive;
}

/**
 * Update last activity timestamp
 * Should be called on each page load for active users
 * @return void
 */
function session_touch(): void
{
    $_SESSION['last_activity'] = time();
}

/**
 * Get remaining session time in minutes
 * @return int|false Minutes remaining or false if no session
 */
function get_remaining_session_time(): int|false
{
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }

    $max = 60 * 60 * 2; // 2 hours default
    $elapsed = time() - $_SESSION['last_activity'];
    $remaining = $max - $elapsed;

    return $remaining > 0 ? (int) ($remaining / 60) : 0;
}

/**
 * Validate session security flags are properly set
 * @return array ['secure' => bool, 'httponly' => bool, 'samesite' => string]
 */
function get_session_config(): array
{
    return [
        'secure' => (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'),
        'httponly' => ini_get('session.cookie_httponly') ? true : false,
        'samesite' => ini_get('session.cookie_samesite') ?? 'unknown'
    ];
}