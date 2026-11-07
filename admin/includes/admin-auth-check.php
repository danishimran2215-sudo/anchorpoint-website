<?php
/**
 * Anchor Point — Admin Auth Guard
 */
require_once __DIR__ . '/../../config.php';

if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login?ref=' . urlencode($_SERVER['REQUEST_URI']));
}

$currentUser = [
    'id'   => $_SESSION['admin_id'] ?? 0,
    'name' => $_SESSION['admin_name'] ?? 'Administrator',
    'role' => $_SESSION['admin_role'] ?? 'editor'
];
