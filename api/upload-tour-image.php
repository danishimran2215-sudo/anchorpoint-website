<?php
/**
 * Anchor Point Travel & Tourism — Activity Image Upload Endpoint (AJAX, Auto-WebP)
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/activities/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$filesToProcess = [];
$errors = [];

foreach (['image', 'file', 'images', 'photos'] as $key) {
    if (!isset($_FILES[$key])) continue;
    $f = $_FILES[$key];
    if (is_array($f['name'])) {
        for ($i = 0; $i < count($f['name']); $i++) {
            if ($f['error'][$i] === UPLOAD_ERR_OK) {
                $filesToProcess[] = [
                    'name'     => $f['name'][$i],
                    'type'     => $f['type'][$i],
                    'tmp_name' => $f['tmp_name'][$i],
                    'error'    => $f['error'][$i],
                    'size'     => $f['size'][$i],
                ];
            }
        }
    } elseif ($f['error'] === UPLOAD_ERR_OK) {
        $filesToProcess[] = $f;
    }
}

if (empty($filesToProcess)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid image uploaded.']);
    exit;
}

$uploadedUrls = [];
foreach ($filesToProcess as $file) {
    try {
        $savedFilename = uploadFile($file, $uploadDir, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 10 * 1024 * 1024);
        $uploadedUrls[] = [
            'url'      => '/uploads/activities/' . $savedFilename,
            'filename' => $savedFilename,
            'name'     => $file['name'],
        ];
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

if (empty($uploadedUrls)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => implode('; ', $errors) ?: 'Upload failed.']);
    exit;
}

echo json_encode([
    'success' => true,
    'url'     => $uploadedUrls[0]['url'],
    'files'   => $uploadedUrls,
]);
