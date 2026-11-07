<?php
/**
 * On-Demand Activity Image Delivery & Fallback Endpoint
 * Serves /uploads/activities/*.webp with graceful fallback to category cover images.
 */
declare(strict_types=1);

$reqFile = basename((string)($_GET['file'] ?? ''));
if (!preg_match('/^[a-z0-9-]+\.webp$/', $reqFile)) {
    http_response_code(404);
    exit('Not found');
}

$rootDir = dirname(__DIR__);
$actDir  = $rootDir . '/uploads/activities';

$outFile = $actDir . '/' . $reqFile;
if (is_file($outFile) && filesize($outFile) > 0) {
    header('Content-Type: image/webp');
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($outFile);
    exit;
}

// Category-aware WebP fallbacks inside /uploads/activities
$fallback = $actDir . '/evening-desert-safari-with-bbq-dinner-cover.webp';
if (str_contains($reqFile, 'city') || str_contains($reqFile, 'dubai-tour') || str_contains($reqFile, 'abu-dhabi')) {
    $fallback = $actDir . '/dubai-city-tour-half-day-cover.webp';
} elseif (str_contains($reqFile, 'cruise') || str_contains($reqFile, 'yacht')) {
    $fallback = $actDir . '/dhow-cruise-dinner-dubai-marina-cover.webp';
} elseif (str_contains($reqFile, 'water') || str_contains($reqFile, 'jet-ski') || str_contains($reqFile, 'diving')) {
    $fallback = $actDir . '/jet-ski-ride-cover.webp';
} elseif (str_contains($reqFile, 'ticket') || str_contains($reqFile, 'burj') || str_contains($reqFile, 'park')) {
    $fallback = $actDir . '/burj-khalifa-at-the-top-cover.webp';
}

if (is_file($fallback)) {
    header('Content-Type: image/webp');
    header('Cache-Control: public, max-age=86400');
    readfile($fallback);
    exit;
}

$placeholder = $rootDir . '/assets/images/activity-placeholder.svg';
if (is_file($placeholder)) {
    header('Content-Type: image/svg+xml');
    header('Cache-Control: public, max-age=86400');
    readfile($placeholder);
    exit;
}

http_response_code(404);
exit('Image not found');
