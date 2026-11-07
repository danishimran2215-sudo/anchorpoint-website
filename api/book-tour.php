<?php
/**
 * Customer Information Form Submission Endpoint — PDF Section 10
 * 1. Saves enquiry to tour_bookings (never loses a lead)
 * 2. Emails company: "New Enquiry: [Activity] - [Customer name] - [Date]"
 * 3. Emails customer auto-confirmation ("We received your request and will reply within 30 minutes on WhatsApp.")
 * 4. Redirects to /thank-you?ref=...
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . 'mailer.php';

if (!isPost()) {
    redirect('/book');
}

$wantsJson = str_contains(strtolower($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

$returnError = function (string $msg) use ($wantsJson): never {
    if ($wantsJson) {
        jsonResponse(['success' => false, 'message' => $msg], 422);
    }
    $_SESSION['form_error'] = $msg;
    $_SESSION['form_old']   = $_POST;
    $back = sanitize($_POST['source_page'] ?? '/book');
    if ($back === '' || !str_starts_with($back, '/')) {
        $back = '/book';
    }
    redirect($back . '#enquiryFormSection');
};

// 14a. Honeypot check (bots fill hidden website_url)
if (!empty($_POST['website_url'])) {
    redirect('/thank-you');
}

// 14b. CSRF verification (allow seamless retry if session rotated only for JSON tests with explicit flag, otherwise enforce)
$csrfToken = $_POST[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    $returnError('Your form session expired. Please submit your enquiry again.');
}

// 14c. Optional Cloudflare Turnstile verification
$turnstileSecret = trim(getSetting('turnstile_secret_key', ''));
if ($turnstileSecret !== '') {
    $cfToken = trim((string)($_POST['cf-turnstile-response'] ?? ''));
    if ($cfToken === '') {
        $returnError('Please complete the security check before submitting.');
    }
    $verifyPayload = http_build_query([
        'secret'   => $turnstileSecret,
        'response' => $cfToken,
        'remoteip' => getClientIP(),
    ]);
    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $verifyPayload,
            'timeout' => 8,
        ],
    ]);
    $cfRaw = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $ctx);
    $cfRes = $cfRaw ? json_decode($cfRaw, true) : null;
    if (empty($cfRes['success'])) {
        $returnError('Security verification failed. Please try again or message us directly on WhatsApp.');
    }
}

// Collect & validate fields 1–13
$customerName    = sanitize($_POST['customer_name'] ?? '');
$customerEmail   = sanitize($_POST['customer_email'] ?? '');
$countryCode     = sanitize($_POST['country_code'] ?? '+971') ?: '+971';
$customerPhone   = sanitize($_POST['customer_phone'] ?? '');
$categoryName    = sanitize($_POST['category_name'] ?? '');
$tourId          = (int)($_POST['tour_id'] ?? 0);
$bookingDate     = sanitize($_POST['booking_date'] ?? '');
$preferredTime   = sanitize($_POST['preferred_time'] ?? 'Flexible');
$adultsCount     = max(1, (int)($_POST['adults_count'] ?? 1));
$childrenCount   = max(0, (int)($_POST['children_count'] ?? 0));
$childrenAges    = sanitize($_POST['children_ages'] ?? '');
$pickupLocation  = sanitize($_POST['pickup_location'] ?? '');
$nationality     = sanitize($_POST['nationality'] ?? '');
$specialRequests = sanitize($_POST['special_requests'] ?? '');
$consentGiven    = !empty($_POST['consent_given']) ? 1 : 0;
$sourcePage      = sanitize($_POST['source_page'] ?? '/book');

if ($customerName === '' || mb_strlen($customerName) < 2) {
    $returnError('Please enter your full name.');
}
if ($customerEmail === '' || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    $returnError('Please enter a valid email address.');
}
if ($customerPhone === '' || strlen(preg_replace('/[^0-9]/', '', $customerPhone)) < 6) {
    $returnError('Please enter a valid phone or WhatsApp number.');
}
if ($bookingDate === '' || $bookingDate < date('Y-m-d')) {
    $returnError('Please choose today or a future date for your experience.');
}
if ($consentGiven !== 1) {
    $returnError('Please agree to the privacy policy and contact consent checkbox.');
}

$db = getDB();
$tourRow = null;
if ($tourId > 0) {
    $st = $db->prepare('SELECT t.*, c.name AS category_name, c.slug AS category_slug FROM tours t JOIN tour_categories c ON t.category_id = c.id WHERE t.id = ? LIMIT 1');
    $st->execute([$tourId]);
    $tourRow = $st->fetch() ?: null;
}

$tourTitle = $tourRow ? $tourRow['title'] : sanitize($_POST['tour_title'] ?? 'UAE Tour Enquiry');
if ($tourRow && $categoryName === '') {
    $categoryName = $tourRow['category_name'];
}
if ($tourRow && (int)($tourRow['has_pickup'] ?? 1) === 0) {
    $pickupLocation = '';
}

$guestsTotal = $adultsCount + $childrenCount;
$adultPrice  = $tourRow ? (float)$tourRow['price_aed'] : 0.0;
$childPrice  = $tourRow ? ((float)$tourRow['child_price_aed'] > 0 ? (float)$tourRow['child_price_aed'] : $adultPrice) : 0.0;
$estTotalAed = ($adultsCount * $adultPrice) + ($childrenCount * $childPrice);

$bookingRef = 'AP-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
$utms = [
    'utm_source'   => sanitize($_POST['utm_source'] ?? ($_SESSION['utm']['utm_source'] ?? '')),
    'utm_medium'   => sanitize($_POST['utm_medium'] ?? ($_SESSION['utm']['utm_medium'] ?? '')),
    'utm_campaign' => sanitize($_POST['utm_campaign'] ?? ($_SESSION['utm']['utm_campaign'] ?? '')),
    'utm_term'     => sanitize($_POST['utm_term'] ?? ($_SESSION['utm']['utm_term'] ?? '')),
    'utm_content'  => sanitize($_POST['utm_content'] ?? ($_SESSION['utm']['utm_content'] ?? '')),
];
$clientIp = getClientIP();
$nowStr   = date('Y-m-d H:i:s');

// 1. Save to database first so zero enquiries are ever lost
$ins = $db->prepare('
    INSERT INTO tour_bookings (
        booking_ref, tour_id, category_name, tour_title,
        customer_name, customer_email, country_code, customer_phone,
        booking_date, preferred_time, adults_count, children_count, children_ages, guests_count,
        pickup_location, nationality, special_requests, consent_given,
        source_page, utm_source, utm_medium, utm_campaign, utm_term, utm_content,
        total_price_aed, status, email_sent, ip, created_at
    ) VALUES (
        ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?, 0, ?, ?
    )
');
$ins->execute([
    $bookingRef,
    $tourRow ? (int)$tourRow['id'] : null,
    $categoryName,
    $tourTitle,
    $customerName,
    $customerEmail,
    $countryCode,
    $customerPhone,
    $bookingDate,
    $preferredTime,
    $adultsCount,
    $childrenCount,
    $childrenAges,
    $guestsTotal,
    $pickupLocation,
    $nationality,
    $specialRequests,
    $consentGiven,
    $sourcePage,
    $utms['utm_source'],
    $utms['utm_medium'],
    $utms['utm_campaign'],
    $utms['utm_term'],
    $utms['utm_content'],
    $estTotalAed,
    'New',
    $clientIp,
    $nowStr,
]);
$bookingId = (int)$db->lastInsertId();

$enquiryPayload = [
    'id'               => $bookingId,
    'booking_ref'      => $bookingRef,
    'category_name'    => $categoryName,
    'tour_title'       => $tourTitle,
    'customer_name'    => $customerName,
    'customer_email'   => $customerEmail,
    'country_code'     => $countryCode,
    'customer_phone'   => $customerPhone,
    'booking_date'     => $bookingDate,
    'preferred_time'   => $preferredTime,
    'adults_count'     => $adultsCount,
    'children_count'   => $childrenCount,
    'children_ages'    => $childrenAges,
    'pickup_location'  => $pickupLocation,
    'nationality'      => $nationality,
    'special_requests' => $specialRequests,
    'source_page'      => $sourcePage,
    'utm_source'       => $utms['utm_source'],
    'utm_medium'       => $utms['utm_medium'],
    'utm_campaign'     => $utms['utm_campaign'],
    'utm_term'         => $utms['utm_term'],
    'utm_content'      => $utms['utm_content'],
];

// 2 & 3. Send Company Notification + Customer Auto-Confirmation
$sentCompany  = false;
$sentCustomer = false;
try {
    $sentCompany  = sendEnquiryNotificationToCompany($enquiryPayload);
    $sentCustomer = sendEnquiryConfirmationToCustomer($enquiryPayload);
    if ($sentCompany || $sentCustomer) {
        $db->prepare('UPDATE tour_bookings SET email_sent = 1 WHERE id = ?')->execute([$bookingId]);
    }
} catch (Throwable $e) {
    error_log('[AnchorPoint Enquiry Mail] ' . $e->getMessage());
}

// Store in session for /thank-you pre-filled WhatsApp & conversion tracking
$_SESSION['last_booking'] = [
    'booking_ref'    => $bookingRef,
    'tour_title'     => $tourTitle,
    'category_name'  => $categoryName,
    'customer_name'  => $customerName,
    'booking_date'   => $bookingDate,
    'adults_count'   => $adultsCount,
    'children_count' => $childrenCount,
    'total_price'    => $estTotalAed,
];

if ($wantsJson) {
    jsonResponse([
        'success'      => true,
        'booking_ref'  => $bookingRef,
        'redirect_url' => '/thank-you?ref=' . urlencode($bookingRef),
        'whatsapp_url' => waLinkCheckout($tourTitle, $customerName),
    ]);
}

redirect('/thank-you?ref=' . urlencode($bookingRef));
