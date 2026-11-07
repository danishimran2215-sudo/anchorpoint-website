<?php
/**
 * Anchor Point Travel & Tourism — Customer Enquiries Manager (/admin/tours/bookings)
 * Displays all 14 PDF Section 10 fields, Status Workflow, CSV Export, and 1-Click WhatsApp Reply
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Customer Enquiries & Bookings';
$db = getDB();

// CSV Export
if (($_GET['export'] ?? '') === 'csv') {
    $rows = $db->query('SELECT * FROM tour_bookings ORDER BY id DESC')->fetchAll();
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="anchorpoint_enquiries_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, [
        'ID', 'Reference', 'Created At', 'Status', 'Category', 'Activity',
        'Full Name', 'Email', 'Country Code', 'Phone/WhatsApp', 'Preferred Date', 'Preferred Time',
        'Adults', 'Children', 'Children Ages', 'Pick-up Location', 'Nationality',
        'Special Requests', 'Source Page', 'UTM Source', 'UTM Medium', 'UTM Campaign'
    ]);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'], $r['booking_ref'] ?? '', $r['created_at'] ?? '', $r['status'] ?? 'New',
            $r['category_name'] ?? '', $r['tour_title'] ?? '',
            $r['customer_name'] ?? '', $r['customer_email'] ?? '',
            $r['country_code'] ?? '+971', $r['customer_phone'] ?? '',
            $r['booking_date'] ?? '', $r['preferred_time'] ?? '',
            $r['adults_count'] ?? 1, $r['children_count'] ?? 0, $r['children_ages'] ?? '',
            $r['pickup_location'] ?? '', $r['nationality'] ?? '',
            $r['special_requests'] ?? '', $r['source_page'] ?? '',
            $r['utm_source'] ?? '', $r['utm_medium'] ?? '', $r['utm_campaign'] ?? ''
        ]);
    }
    fclose($out);
    exit;
}

if (isPost() && ($_POST['action'] ?? '') === 'update_status' && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $bookingId = (int)$_POST['id'];
    $newStatus = sanitize($_POST['status'] ?? 'New');
    $db->prepare('UPDATE tour_bookings SET status = ? WHERE id = ?')->execute([$newStatus, $bookingId]);
    logActivity('Updated Enquiry Status', 'tour_bookings', $bookingId, "Status set to $newStatus");
    redirect('/admin/tours/bookings');
}

$bookings = [];
try {
    $bookings = $db->query('SELECT * FROM tour_bookings ORDER BY id DESC LIMIT 300')->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
  <div>
    <input type="text" id="table-search" class="form-control" placeholder="Search name, phone, activity, ref..." style="max-width:320px;">
  </div>
  <div style="display:flex;gap:0.75rem;">
    <a href="/admin/tours/bookings?export=csv" class="btn-gold">Export CSV</a>
    <a href="/admin/tours" class="btn-logout">← Activities</a>
  </div>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref / Received</th>
          <th>Customer &amp; Nationality</th>
          <th>Category &amp; Activity</th>
          <th>Date, Time &amp; Guests</th>
          <th>Pick-up &amp; Notes</th>
          <th>Source / UTM</th>
          <th>Status</th>
          <th style="text-align:right;">WhatsApp</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($bookings)): ?>
        <tr>
          <td colspan="8" style="text-align:center;padding:2.5rem;color:var(--admin-muted);">
            No enquiries submitted yet. Every form submission is saved here automatically.
          </td>
        </tr>
        <?php endif; ?>
        <?php foreach ($bookings as $b):
          $fullPhone = trim(($b['country_code'] ?? '+971') . ' ' . ($b['customer_phone'] ?? ''));
          $waDigits  = preg_replace('/[^0-9]/', '', $fullPhone);
          $waMsg     = 'Hi ' . ($b['customer_name'] ?? '') . ', thank you for your enquiry with Anchor Point for ' . ($b['tour_title'] ?? '') . ' on ' . ($b['booking_date'] ?? '') . '. Here are your booking details:';
          $waUrl     = buildWhatsAppLink($waDigits, $waMsg);
        ?>
        <tr>
          <td style="white-space:nowrap;">
            <strong><?= e($b['booking_ref'] ?: ('#' . $b['id'])) ?></strong><br>
            <span style="font-size:0.76rem;color:var(--admin-muted);"><?= e($b['created_at'] ?? '') ?></span>
          </td>
          <td>
            <div style="font-weight:700;color:var(--admin-text);"><?= e($b['customer_name']) ?></div>
            <div style="font-size:0.84rem;"><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $fullPhone)) ?>" style="color:var(--admin-gold);"><?= e($fullPhone) ?></a></div>
            <div style="font-size:0.78rem;color:var(--admin-muted);"><?= e($b['customer_email']) ?></div>
            <?php if (!empty($b['nationality'])): ?>
              <div style="font-size:0.75rem;color:var(--admin-muted);">Nationality: <?= e($b['nationality']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <strong style="color:var(--admin-text);"><?= e($b['tour_title']) ?></strong><br>
            <span style="font-size:0.78rem;color:var(--admin-gold);"><?= e($b['category_name'] ?? '') ?></span>
          </td>
          <td style="font-size:0.84rem;">
            <strong><?= e($b['booking_date']) ?></strong>
            <?php if (!empty($b['preferred_time'])): ?>
              <div style="font-size:0.76rem;color:var(--admin-muted);"><?= e($b['preferred_time']) ?></div>
            <?php endif; ?>
            <div style="font-size:0.8rem;color:var(--admin-text);margin-top:2px;">
              <?= (int)($b['adults_count'] ?: $b['guests_count']) ?> Adult(s)
              <?php if ((int)($b['children_count'] ?? 0) > 0): ?>
                , <?= (int)$b['children_count'] ?> Child(ren)<?= !empty($b['children_ages']) ? ' (' . e($b['children_ages']) . ')' : '' ?>
              <?php endif; ?>
            </div>
          </td>
          <td style="font-size:0.8rem;max-width:240px;">
            <div><strong>Pick-up:</strong> <?= e($b['pickup_location'] ?: 'N/A') ?></div>
            <?php if (!empty($b['special_requests'])): ?>
              <div style="color:var(--admin-muted);margin-top:0.2rem;"><strong>Note:</strong> <?= e($b['special_requests']) ?></div>
            <?php endif; ?>
          </td>
          <td style="font-size:0.75rem;color:var(--admin-muted);">
            <code><?= e($b['source_page'] ?: '/') ?></code>
            <?php if (!empty($b['utm_source']) || !empty($b['utm_campaign'])): ?>
              <div><?= e(trim(($b['utm_source'] ?? '') . ' / ' . ($b['utm_campaign'] ?? ''), ' /')) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <form action="/admin/tours/bookings" method="POST" style="margin:0;">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="update_status">
              <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <select name="status" class="form-control" style="font-size:0.8rem;padding:0.25rem 0.5rem;width:auto;" onchange="this.form.submit()">
                <?php foreach (['New', 'Contacted', 'Confirmed', 'Completed', 'Cancelled'] as $stOpt): ?>
                  <option value="<?= $stOpt ?>" <?= ($b['status'] ?? 'New') === $stOpt ? 'selected' : '' ?>><?= $stOpt ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
          <td style="text-align:right;">
            <a href="<?= e($waUrl) ?>" target="_blank" class="btn-logout" style="border-color:#25D366;color:#25D366;font-size:0.8rem;padding:0.3rem 0.65rem;">
              Reply on WhatsApp
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

</div><!-- .admin-content -->
</div><!-- .admin-main -->
<script src="/assets/js/admin.js"></script>
</body>
</html>
