<?php
/**
 * Authentication & Security — Anchor Point Admin (SQLite + MySQL compatible)
 */

function generateCsrfToken(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = generateToken(32);
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verifyCsrfToken(string $token): bool {
    $stored = $_SESSION[CSRF_TOKEN_NAME] ?? '';
    if (empty($stored) || empty($token)) return false;
    return hash_equals($stored, $token);
}

function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . e($token) . '">';
}

function csrfMeta(): string {
    $token = generateCsrfToken();
    return '<meta name="csrf-token" content="' . e($token) . '">';
}

function checkBruteForce(string $ip): bool {
    try {
        $db     = getDB();
        $window = date('Y-m-d H:i:s', time() - BRUTE_FORCE_WINDOW * 60);
        $st     = $db->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > ?');
        $st->execute([$ip, $window]);
        return (int)$st->fetchColumn() >= BRUTE_FORCE_MAX;
    } catch (Exception $e) {
        return false;
    }
}

function recordFailedAttempt(string $ip): void {
    try {
        $db = getDB();
        $db->prepare('INSERT INTO login_attempts (ip, attempted_at) VALUES (?, ?)')
           ->execute([$ip, date('Y-m-d H:i:s')]);
    } catch (Exception $e) {}
}

function clearFailedAttempts(string $ip): void {
    try {
        $db = getDB();
        $db->prepare('DELETE FROM login_attempts WHERE ip = ?')
           ->execute([$ip]);
    } catch (Exception $e) {}
}

function isAdminLoggedIn(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_role']);
}

function loginAdmin(int $userId, string $role, string $name): void {
    session_regenerate_id(true);
    $_SESSION['admin_id']   = $userId;
    $_SESSION['admin_role'] = $role;
    $_SESSION['admin_name'] = $name;
    $_SESSION['admin_login_time'] = time();
    try {
        $db = getDB();
        $db->prepare('UPDATE users SET last_login = ? WHERE id = ?')
           ->execute([date('Y-m-d H:i:s'), $userId]);
    } catch (Exception $e) {}
}

function logoutAdmin(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function requireAdmin(string $role = ''): void {
    if (!isAdminLoggedIn()) {
        redirect('/admin/login?ref=' . urlencode($_SERVER['REQUEST_URI'] ?? '/admin/dashboard'));
    }
    if ($role && $_SESSION['admin_role'] !== 'superadmin' && $_SESSION['admin_role'] !== $role) {
        http_response_code(403);
        die('Access denied.');
    }
    $maxIdle = SESSION_LIFETIME;
    if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > $maxIdle) {
        logoutAdmin();
        redirect('/admin/login?timeout=1');
    }
    $_SESSION['admin_login_time'] = time();
}

function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

function logActivity(string $action, string $entityType = '', int $entityId = 0, string $details = ''): void {
    try {
        $db = getDB();
        $db->prepare(
            'INSERT INTO activity_log (user_id, action, entity_type, entity_id, details, ip, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $_SESSION['admin_id'] ?? null,
            $action,
            $entityType,
            $entityId,
            $details,
            getClientIP(),
            date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {}
}
