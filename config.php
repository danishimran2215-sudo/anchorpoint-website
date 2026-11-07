<?php
/**
 * Anchor Point Business Setup & Travel — Central Configuration
 */

// Optional local/server secret overrides (gitignored)
if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// ── Environment ────────────────────────────────────────────
defined('APP_ENV') || define('APP_ENV', 'production'); // 'development' | 'production'

// ── Error Reporting ────────────────────────────────────────
@header_remove('X-Powered-By');
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/logs/php-errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// ── Database ───────────────────────────────────────────────
defined('DB_HOST')    || define('DB_HOST', 'localhost');
defined('DB_NAME')    || define('DB_NAME', 'YOUR_DB_NAME');
defined('DB_USER')    || define('DB_USER', 'YOUR_DB_USER');
defined('DB_PASS')    || define('DB_PASS', 'YOUR_DB_PASSWORD');
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');

// ── Site ───────────────────────────────────────────────────
$detectedProto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';
$detectedHost  = $_SERVER['HTTP_HOST'] ?? 'anchorpointbiz.org';
defined('SITE_URL')     || define('SITE_URL', defined('CUSTOM_SITE_URL') ? CUSTOM_SITE_URL : ($detectedProto . $detectedHost));
defined('SITE_NAME')    || define('SITE_NAME', 'Anchor Point Travel & Tourism');
defined('SITE_TAGLINE') || define('SITE_TAGLINE', 'Unforgettable Tours & Experiences Across the UAE');

// ── Contact Details ────────────────────────────────────────
defined('PHONE_1')         || define('PHONE_1', '+971 52 537 6838');
defined('PHONE_1_RAW')     || define('PHONE_1_RAW', '+971525376838');
defined('PHONE_2')         || define('PHONE_2', '+971 52 529 1312');
defined('PHONE_2_RAW')     || define('PHONE_2_RAW', '+971525291312');
defined('WHATSAPP_NUMBER') || define('WHATSAPP_NUMBER', '971525376838'); // International format, no + or spaces
defined('ADMIN_EMAIL')     || define('ADMIN_EMAIL', 'info@anchorpointbiz.org');
defined('OFFICE_ADDRESS')  || define('OFFICE_ADDRESS', 'Office #08, Sheikh Rashid Rd, near Financial Centre Metro, Al Jafiliya, Dubai, UAE');
defined('OFFICE_HOURS')    || define('OFFICE_HOURS', 'Daily: 9:00 AM – 6:00 PM (GST)');

// ── SMTP Email (Also editable via Admin > Site Settings) ───
defined('SMTP_HOST')       || define('SMTP_HOST', '');
defined('SMTP_PORT')       || define('SMTP_PORT', 587);
defined('SMTP_USER')       || define('SMTP_USER', '');
defined('SMTP_PASS')       || define('SMTP_PASS', '');
defined('SMTP_FROM')       || define('SMTP_FROM', 'info@anchorpointbiz.org');
defined('SMTP_FROM_NAME')  || define('SMTP_FROM_NAME', 'Anchor Point Travel & Tourism');
defined('SMTP_ENCRYPTION') || define('SMTP_ENCRYPTION', 'tls');

// ── Uploads ────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('UPLOAD_MAX_SIZE', 8 * 1024 * 1024); // 8 MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);

// ── Security ───────────────────────────────────────────────
define('CSRF_TOKEN_NAME', '_csrf_token');
define('SESSION_LIFETIME', 3600 * 8);
define('BRUTE_FORCE_MAX', 5);
define('BRUTE_FORCE_WINDOW', 15);

// ── Analytics Defaults (editable via Admin > Settings) ─────
defined('GA_ID')      || define('GA_ID', '');
defined('GTM_ID')     || define('GTM_ID', '');
defined('META_PIXEL') || define('META_PIXEL', '');

// ── Paths ──────────────────────────────────────────────────
define('ROOT_PATH', __DIR__);
define('INCLUDES_PATH', __DIR__ . '/includes/');

// ── Session Configuration ──────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $sessionParams = [
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    session_set_cookie_params($sessionParams);
    ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
    session_name('AP_SESSION');
    session_start();
}

// ── Autoload includes ──────────────────────────────────────
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'functions.php';
require_once INCLUDES_PATH . 'auth.php';

// Persist any incoming UTM parameters immediately across pages
getUTM();

// Auto-sync current office address & hours to database settings if empty
try {
    $currAddr = getSetting('office_address', '');
    if (empty($currAddr)) {
        setSetting('office_address', OFFICE_ADDRESS);
    }
    $currHours = getSetting('office_hours', '');
    if (empty($currHours)) {
        setSetting('office_hours', OFFICE_HOURS);
    }
} catch (\Throwable $e) {}
