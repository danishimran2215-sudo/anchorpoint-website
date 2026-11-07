<?php
/**
 * Anchor Point — Admin Index Router
 */
require_once __DIR__ . '/../config.php';

if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/dashboard');
} else {
    redirect(SITE_URL . '/admin/login');
}
