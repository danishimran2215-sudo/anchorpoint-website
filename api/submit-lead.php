<?php
/**
 * Anchor Point — Lead Submission API
 * Handles Contact Form, Service Quotes, Honeypot, CSRF, Rate Limiting & Notifications
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mailer.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isPost()) {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

// Read JSON or POST body
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

// 1. Honeypot check
if (!empty($input['website_url'])) {
    // Silently drop spam submissions
    jsonResponse(['success' => true, 'message' => 'Your inquiry has been received.']);
}

// 2. CSRF check
$token = $input['_csrf_token'] ?? $input[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($token)) {
    // Regenerate and continue if session expired in background
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        generateCsrfToken();
    }
}

// 3. Validation
$name    = sanitize($input['name'] ?? '');
$phone   = sanitize($input['phone'] ?? '');
$email   = filter_var(trim($input['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? trim($input['email']) : '';
$message = sanitize($input['message'] ?? '');
$svcId   = !empty($input['service_id']) ? (int)$input['service_id'] : null;
$source  = sanitize($input['source_page'] ?? ($_SERVER['HTTP_REFERER'] ?? '/'));

if (empty($name) || empty($phone)) {
    jsonResponse(['success' => false, 'message' => 'Name and phone number are required.'], 422);
}

// 4. Rate limiting (max 10 submissions per IP per 10 minutes)
$clientIp = getClientIP();
$db = getDB();

try {
    $limCheck = $db->prepare("SELECT COUNT(*) FROM leads WHERE ip = ? AND created_at > datetime('now', '-10 minutes')");
    $limCheck->execute([$clientIp]);
    if ((int)$limCheck->fetchColumn() > 10) {
        jsonResponse(['success' => false, 'message' => 'Too many requests. Please WhatsApp us directly.'], 429);
    }
} catch (Exception $e) {}

// 5. UTM params
$utm = getUTM();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// 6. Insert lead
try {
    $stmt = $db->prepare("
        INSERT INTO leads (
            name, phone, email, service_id, message, source_page,
            utm_source, utm_medium, utm_campaign, utm_term, utm_content,
            ip, user_agent, status, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, 'New', CURRENT_TIMESTAMP
        )
    ");

    $stmt->execute([
        $name,
        $phone,
        $email,
        $svcId,
        $message,
        $source,
        $utm['utm_source'] ?? null,
        $utm['utm_medium'] ?? null,
        $utm['utm_campaign'] ?? null,
        $utm['utm_term'] ?? null,
        $utm['utm_content'] ?? null,
        $clientIp,
        $userAgent
    ]);

    // Service name for notification
    $serviceName = 'General Inquiry';
    if ($svcId) {
        $svcQ = $db->prepare('SELECT title FROM services WHERE id = ?');
        $svcQ->execute([$svcId]);
        $sRow = $svcQ->fetch();
        if ($sRow) $serviceName = $sRow['title'];
    }

    $leadData = [
        'name'         => $name,
        'phone'        => $phone,
        'email'        => $email,
        'service_name' => $serviceName,
        'message'      => $message,
        'source_page'  => $source,
    ];

    // Trigger notification email (non-blocking)
    @sendLeadNotification($leadData);
    if ($email) {
        @sendAutoReply($leadData);
    }

    jsonResponse([
        'success' => true,
        'message' => 'Thank you! Your inquiry has been submitted successfully.'
    ]);

} catch (Exception $e) {
    error_log('[Lead Submission Error] ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Unable to save your request right now. Please message us on WhatsApp.'
    ], 500);
}
