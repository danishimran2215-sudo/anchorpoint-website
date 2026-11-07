<?php
/**
 * Anchor Point — CSRF Token API Endpoint
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'csrf_token' => generateCsrfToken()
]);
exit;
