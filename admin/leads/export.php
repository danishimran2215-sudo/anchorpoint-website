<?php
/**
 * Anchor Point — Leads CSV Export
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$db = getDB();
$filename = 'anchorpoint-leads-' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
// UTF-8 BOM for Excel
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($out, ['ID', 'Name', 'Phone', 'Email', 'Service', 'Message', 'Source Page', 'Status', 'Notes', 'Created At']);

try {
    $stmt = $db->query('
        SELECT l.id, l.name, l.phone, l.email, s.title as service_title, l.message, l.source_page, l.status, l.notes, l.created_at
        FROM leads l
        LEFT JOIN services s ON l.service_id = s.id
        ORDER BY l.created_at DESC
    ');
    while ($row = $stmt->fetch()) {
        fputcsv($out, [
            $row['id'],
            $row['name'],
            $row['phone'],
            $row['email'],
            $row['service_title'] ?: 'General Advisory',
            $row['message'],
            $row['source_page'],
            $row['status'],
            $row['notes'],
            $row['created_at'],
        ]);
    }
} catch (Exception $e) {}

fclose($out);
exit;
