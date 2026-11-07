<?php
/**
 * Anchor Point — Admin Logout
 */
require_once __DIR__ . '/../config.php';

logActivity('Logged Out', 'users', $_SESSION['admin_id'] ?? 0, 'User logged out');
logoutAdmin();
redirect(SITE_URL . '/admin/login');
