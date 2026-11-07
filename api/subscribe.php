<?php
/**
 * Anchor Point — Newsletter Subscription Endpoint
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isPost()) {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$email = filter_var(trim($input['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$email) {
    jsonResponse(['success' => false, 'message' => 'Please provide a valid email address.'], 422);
}

// Store lead for newsletter
$db = getDB();
try {
    $stmt = $db->prepare("INSERT INTO leads (name, phone, email, message, source_page, status) VALUES ('Newsletter Subscriber', '-', ?, 'Subscribed to newsletter updates', '/newsletter', 'New')");
    $stmt->execute([$email]);
    jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
} catch (Exception $e) {
    jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
}
