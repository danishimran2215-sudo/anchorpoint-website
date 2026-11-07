<?php
/**
 * On-Demand High-Res Business Setup Service Image Delivery & Cache
 * Serves real UAE corporate photos for all 31 services.
 * Automatically persists to /uploads/services/<filename> so subsequent hits are served directly by Apache.
 */
declare(strict_types=1);

$reqFile = basename((string)($_GET['file'] ?? ''));
if (!preg_match('/^[a-z0-9-]+\.(jpg|jpeg|png|webp)$/i', $reqFile)) {
    http_response_code(404);
    exit('Not found');
}

$rootDir = dirname(__DIR__);
$servDir = $rootDir . '/uploads/services';
if (!is_dir($servDir)) {
    @mkdir($servDir, 0755, true);
}

$target = $servDir . '/' . $reqFile;
if (is_file($target) && filesize($target) > 0) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($target);
    exit;
}

// Candidate source templates from highest quality real assets
$templates = [
    'license'  => $servDir . '/new-trade-license.jpg',
    'renewal'  => $servDir . '/renewal-trade-license.jpg',
    'pro'      => $servDir . '/pro-services.jpg',
    'vehicle'  => $servDir . '/driving-license-renewal.jpg',
    'legal'    => $servDir . '/corporate-legal-support.jpg',
];

// Determine the best source image based on filename keyword
$source = $templates['license'];

if (preg_match('/(license|licensing|freezone|company|liquidation|quota)/i', $reqFile)) {
    if (str_contains($reqFile, 'renewal') || str_contains($reqFile, 'update')) {
        $source = is_file($templates['renewal']) ? $templates['renewal'] : $templates['license'];
    } else {
        $source = is_file($templates['license']) ? $templates['license'] : $templates['pro'];
    }
} elseif (preg_match('/(visa|pro|agent|police|dm|sira|lost|attorney|establishment|id|typing)/i', $reqFile)) {
    $source = is_file($templates['pro']) ? $templates['pro'] : $templates['license'];
} elseif (preg_match('/(vehicle|driving|salik|darb|insurance|traffic)/i', $reqFile)) {
    $source = is_file($templates['vehicle']) ? $templates['vehicle'] : $templates['pro'];
} elseif (preg_match('/(bank|account|tax|vat|legal|will|email|website)/i', $reqFile)) {
    $source = is_file($templates['legal']) ? $templates['legal'] : $templates['license'];
}

// Fallback to any available template
if (!is_file($source)) {
    foreach ($templates as $t) {
        if (is_file($t)) {
            $source = $t;
            break;
        }
    }
}

if (is_file($source)) {
    // Cache on disk so Apache serves it directly next time
    @copy($source, $target);
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($source);
    exit;
}

// Final fallback to placeholder
header('Content-Type: image/svg+xml');
$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" width="800" height="500"><rect width="800" height="500" fill="#0B2545"/><text x="50%" y="50%" font-family="sans-serif" font-size="28" font-weight="bold" fill="#F8FAFC" text-anchor="middle" dominant-baseline="middle">Anchor Point Business Setup</text></svg>';
echo $svg;
exit;
