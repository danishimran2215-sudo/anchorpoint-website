<?php
/**
 * Anchor Point Travel & Tourism — Admin Dashboard Overview
 * Includes 1-Click Website Price Visibility Toggle + Tourism Metrics + Recent Enquiries
 */
require_once __DIR__ . '/includes/admin-auth-check.php';

$pageTitle = 'UAE Tourism Dashboard';
$db = getDB();

// Handle 1-click Website Price Visibility Toggles
if (isPost()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle_prices' && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $nextVal = arePricesVisible() ? '0' : '1';
        setSetting('show_prices', $nextVal);
        logActivity($nextVal === '1' ? 'Enabled Tour Prices' : 'Hidden Tour Prices', 'settings', 0);
        redirect('/admin/dashboard?price_updated=1');
    }
    if ($action === 'toggle_service_prices' && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $nextVal = areServicePricesVisible() ? '0' : '1';
        setSetting('show_service_prices', $nextVal);
        logActivity($nextVal === '1' ? 'Enabled Business Setup Prices' : 'Hidden Business Setup Prices', 'settings', 0);
        redirect('/admin/dashboard?service_price_updated=1');
    }
}

$pricesVisible        = arePricesVisible();
$servicePricesVisible = areServicePricesVisible();
$priceUpdated         = !empty($_GET['price_updated']);
$servicePriceUpdated  = !empty($_GET['service_price_updated']);

$totalTours      = 0;
$totalCategories = 0;
$totalServices   = 0;
$totalArticles   = 0;
$totalBookings   = 0;
$newBookings     = 0;
$confirmedCount  = 0;
$recentBookings  = [];

try {
    $totalTours      = (int)$db->query('SELECT COUNT(*) FROM tours WHERE is_active = 1')->fetchColumn();
    $totalCategories = (int)$db->query('SELECT COUNT(*) FROM tour_categories WHERE is_active = 1')->fetchColumn();
    $totalServices   = (int)$db->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn();
    $totalArticles   = (int)$db->query('SELECT COUNT(*) FROM blog_posts WHERE is_published = 1')->fetchColumn();
    $totalBookings   = (int)$db->query('SELECT COUNT(*) FROM tour_bookings')->fetchColumn();
    $newBookings     = (int)$db->query("SELECT COUNT(*) FROM tour_bookings WHERE status IN ('New', 'Pending')")->fetchColumn();
    $confirmedCount  = (int)$db->query("SELECT COUNT(*) FROM tour_bookings WHERE status = 'Confirmed'")->fetchColumn();
    $recentBookings  = $db->query('SELECT * FROM tour_bookings ORDER BY id DESC LIMIT 10')->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($priceUpdated): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:#34D399;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1.25rem;font-size:0.88rem;font-weight:600;">
    ✓ Tour pricing visibility updated! Tour &amp; attraction prices are now <strong><?= $pricesVisible ? 'VISIBLE' : 'HIDDEN' ?></strong> across the website.
  </div>
<?php endif; ?>

<?php if ($servicePriceUpdated): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:#34D399;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1.25rem;font-size:0.88rem;font-weight:600;">
    ✓ Business Setup pricing visibility updated! Service prices are now <strong><?= $servicePricesVisible ? 'VISIBLE' : 'HIDDEN' ?></strong> across the website.
  </div>
<?php endif; ?>

<!-- 1-Click Tour Price Visibility Quick-Toggle Card -->
<div class="admin-card" style="padding:1.25rem 1.5rem;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;border-left:4px solid <?= $pricesVisible ? '#10B981' : '#3B82F6' ?>;">
  <div style="display:flex;align-items:center;gap:1rem;">
    <div style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:<?= $pricesVisible ? 'rgba(16,185,129,0.15)' : 'rgba(59,130,246,0.15)' ?>;color:<?= $pricesVisible ? '#34D399' : '#60A5FA' ?>;flex-shrink:0;">
      <?php if ($pricesVisible): ?>
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      <?php else: ?>
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
      <?php endif; ?>
    </div>
    <div>
      <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
        <h2 style="font-size:1.02rem;font-weight:700;margin:0;color:var(--admin-text);">Tour &amp; Attraction Prices (AED)</h2>
        <span class="badge <?= $pricesVisible ? 'badge-converted' : 'badge-new' ?>" style="font-size:0.72rem;">
          <?= $pricesVisible ? '● VISIBLE ON WEBSITE' : '● HIDDEN ON WEBSITE' ?>
        </span>
      </div>
      <p style="margin:0.25rem 0 0;font-size:0.83rem;color:var(--admin-muted);">
        <?= $pricesVisible
            ? 'AED rates ("From AED ___ per person") are currently displayed on all tour cards and attraction pages.'
            : 'All tour and ticket prices are hidden across the website (Enquiry / WhatsApp Quote mode).' ?>
      </p>
    </div>
  </div>

  <form action="/admin/dashboard" method="POST" style="margin:0;">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="toggle_prices">
    <button type="submit" class="btn-gold" style="cursor:pointer;border:none;padding:0.65rem 1.25rem;font-weight:700;display:inline-flex;align-items:center;gap:0.45rem;<?= $pricesVisible ? 'background:#EF4444;color:#FFFFFF;' : 'background:#10B981;color:#FFFFFF;' ?>">
      <?= $pricesVisible ? 'Hide Tour Prices' : 'Show Tour Prices' ?>
    </button>
  </form>
</div>

<!-- 1-Click Business Setup Price Visibility Quick-Toggle Card -->
<div class="admin-card" style="padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;border-left:4px solid <?= $servicePricesVisible ? '#10B981' : '#F59E0B' ?>;">
  <div style="display:flex;align-items:center;gap:1rem;">
    <div style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:<?= $servicePricesVisible ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' ?>;color:<?= $servicePricesVisible ? '#34D399' : '#FBBF24' ?>;flex-shrink:0;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
    </div>
    <div>
      <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
        <h2 style="font-size:1.02rem;font-weight:700;margin:0;color:var(--admin-text);">Business Setup Service Prices (AED)</h2>
        <span class="badge <?= $servicePricesVisible ? 'badge-converted' : 'badge-new' ?>" style="font-size:0.72rem;">
          <?= $servicePricesVisible ? '● VISIBLE ON WEBSITE' : '● HIDDEN ON WEBSITE' ?>
        </span>
      </div>
      <p style="margin:0.25rem 0 0;font-size:0.83rem;color:var(--admin-muted);">
        <?= $servicePricesVisible
            ? 'Starting AED prices ("From AED ___") are currently shown on all 31 Business Setup service cards.'
            : 'All Business Setup prices are hidden across the website (Free Consultation / Custom Quote mode).' ?>
      </p>
    </div>
  </div>

  <form action="/admin/dashboard" method="POST" style="margin:0;">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="toggle_service_prices">
    <button type="submit" class="btn-gold" style="cursor:pointer;border:none;padding:0.65rem 1.25rem;font-weight:700;display:inline-flex;align-items:center;gap:0.45rem;<?= $servicePricesVisible ? 'background:#EF4444;color:#FFFFFF;' : 'background:#10B981;color:#FFFFFF;' ?>">
      <?= $servicePricesVisible ? 'Hide Business Setup Prices' : 'Show Business Setup Prices' ?>
    </button>
  </form>
</div>

<!-- Metrics Grid -->
<div class="admin-stats-grid">
  <div class="admin-stat-card">
    <div class="stat-icon" style="background:rgba(14,165,233,0.15);color:#38BDF8;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
    </div>
    <div>
      <div class="stat-val"><?= $totalTours ?></div>
      <div class="stat-lbl">Active Activities</div>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="stat-icon" style="background:rgba(168,85,247,0.15);color:#C084FC;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    </div>
    <div>
      <div class="stat-val"><?= $totalCategories ?></div>
      <div class="stat-lbl">Tour Categories</div>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:#FBBF24;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
    </div>
    <div>
      <div class="stat-val"><?= $totalServices ?></div>
      <div class="stat-lbl">Corporate Services</div>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:#34D399;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
    </div>
    <div>
      <div class="stat-val"><?= $totalArticles ?></div>
      <div class="stat-lbl">SEO Blog Articles</div>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="stat-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
    </div>
    <div>
      <div class="stat-val"><?= $totalBookings ?></div>
      <div class="stat-lbl">Total Enquiries</div>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="stat-icon" style="background:rgba(59,130,246,0.15);color:#60A5FA;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    </div>
    <div>
      <div class="stat-val"><?= $newBookings ?></div>
      <div class="stat-lbl">New / Awaiting Reply</div>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:#34D399;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    </div>
    <div>
      <div class="stat-val"><?= $confirmedCount ?></div>
      <div class="stat-lbl">Confirmed Bookings</div>
    </div>
  </div>
</div>

<!-- Recent Tour Enquiries Table -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Recent Website Tour Enquiries</h2>
    <a href="/admin/tours/bookings" class="btn-gold">View All Enquiries →</a>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref</th>
          <th>Customer</th>
          <th>Phone / WhatsApp</th>
          <th>Activity</th>
          <th>Date &amp; Guests</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentBookings)): ?>
          <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--admin-muted);">No customer enquiries recorded yet.</td></tr>
        <?php else: ?>
          <?php foreach ($recentBookings as $b):
            $fullPh = trim(($b['country_code'] ?? '+971') . ' ' . ($b['customer_phone'] ?? ''));
            $waDigits = preg_replace('/[^0-9]/', '', $fullPh);
            $waReply  = buildWhatsAppLink($waDigits, 'Hi ' . ($b['customer_name'] ?? '') . ', thank you for your enquiry with Anchor Point for ' . ($b['tour_title'] ?? '') . ' on ' . ($b['booking_date'] ?? '') . '!');
          ?>
          <tr>
            <td><code><?= e($b['booking_ref'] ?: ('#' . $b['id'])) ?></code></td>
            <td>
              <strong><?= e($b['customer_name']) ?></strong><br>
              <span style="font-size:0.78rem;color:var(--admin-muted);"><?= e($b['customer_email']) ?></span>
            </td>
            <td><?= e($fullPh) ?></td>
            <td>
              <strong><?= e($b['tour_title']) ?></strong><br>
              <span style="font-size:0.78rem;color:var(--admin-muted);"><?= e($b['category_name'] ?? '') ?></span>
            </td>
            <td>
              <?= e($b['booking_date']) ?><br>
              <span style="font-size:0.78rem;color:var(--admin-muted);"><?= (int)($b['adults_count'] ?: $b['guests_count']) ?> Ad<?= (int)($b['children_count'] ?? 0) > 0 ? ', ' . (int)$b['children_count'] . ' Ch' : '' ?></span>
            </td>
            <td><span class="badge <?= ($b['status'] === 'Confirmed') ? 'badge-converted' : 'badge-new' ?>"><?= e($b['status'] ?: 'New') ?></span></td>
            <td>
              <div style="display:flex;gap:0.4rem;">
                <a href="<?= e($waReply) ?>" target="_blank" class="btn-logout" style="padding:0.25rem 0.6rem;font-size:0.8rem;color:#34D399;border-color:rgba(16,185,129,0.35);">WhatsApp</a>
                <a href="/admin/tours/bookings" class="btn-logout" style="padding:0.25rem 0.6rem;font-size:0.8rem;">Manage</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</div><!-- .admin-content -->
</div><!-- .admin-main -->
<script src="/assets/js/admin.js"></script>
</body>
</html>
