<?php
/**
 * Anchor Point Travel & Tourism — Activities & Prices Manager
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Activities & Prices Manager';
$db = getDB();

if (isPost() && isset($_POST['action']) && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    if ($_POST['action'] === 'toggle_prices') {
        $nextVal = arePricesVisible() ? '0' : '1';
        setSetting('show_prices', $nextVal);
        logActivity($nextVal === '1' ? 'Enabled Website Prices' : 'Hidden Website Prices', 'settings', 0);
        redirect('/admin/tours');
    } elseif ($_POST['action'] === 'toggle') {
        $tourId = (int)$_POST['id'];
        $db->prepare('UPDATE tours SET is_active = (1 - is_active) WHERE id = ?')->execute([$tourId]);
        logActivity('Toggled Activity Status', 'tours', $tourId);
    } elseif ($_POST['action'] === 'delete') {
        $tourId = (int)$_POST['id'];
        $db->prepare('DELETE FROM tours WHERE id = ?')->execute([$tourId]);
        logActivity('Deleted Activity', 'tours', $tourId);
    }
    redirect('/admin/tours');
}

$pricesVisible = arePricesVisible();
$categories    = getTourCategories();
$selectedCat   = (int)($_GET['category_id'] ?? 0);

$query = '
    SELECT t.*, c.name AS category_name, c.slug AS category_slug
    FROM tours t
    JOIN tour_categories c ON t.category_id = c.id
';
$params = [];
if ($selectedCat > 0) {
    $query .= ' WHERE t.category_id = ?';
    $params[] = $selectedCat;
}
$query .= ' ORDER BY c.sort_order ASC, t.sort_order ASC, t.id ASC';

$tours = [];
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $tours = $stmt->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
  <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
    <input type="text" id="table-search" class="form-control" placeholder="Search activities..." style="max-width:260px;">

    <select class="form-control" style="max-width:250px;" onchange="location.href = this.value ? '/admin/tours?category_id=' + this.value : '/admin/tours';">
      <option value="">All 5 Categories (<?= count($tours) ?>)</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= (int)$cat['id'] ?>" <?= $selectedCat === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
    <form action="/admin/tours" method="POST" style="margin:0;">
      <?= csrfField() ?>
      <input type="hidden" name="action" value="toggle_prices">
      <button type="submit" class="btn-logout" style="cursor:pointer;border-color:<?= $pricesVisible ? '#EF4444' : '#10B981' ?>;color:<?= $pricesVisible ? '#F87171' : '#34D399' ?>;font-weight:700;">
        <?= $pricesVisible ? 'Hide Website Prices' : 'Unhide Website Prices' ?>
      </button>
    </form>
    <a href="/admin/tours/bookings" class="btn-logout" style="border-color:var(--admin-gold);color:var(--admin-gold);">Customer Enquiries</a>
    <a href="/admin/tours/edit" class="btn-gold">+ Add New Activity</a>
  </div>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:50px;">#</th>
          <th style="width:72px;">Cover</th>
          <th>Activity &amp; Clean URL</th>
          <th>Category</th>
          <th>Duration &amp; Pick-up</th>
          <th>Price (AED)</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($tours)): ?>
        <tr>
          <td colspan="8" style="text-align:center;padding:2rem;color:var(--admin-muted);">No activities found.</td>
        </tr>
        <?php endif; ?>
        <?php foreach ($tours as $t):
          $galleryCount = count(json_decode($t['gallery_json'] ?? '[]', true) ?: []);
          $coverImg     = !empty($t['featured_image']) ? $t['featured_image'] : '/assets/images/logo-icon.svg';
          $liveUrl      = activityUrl($t);
        ?>
        <tr>
          <td><span style="color:var(--admin-muted);font-weight:600;"><?= (int)$t['sort_order'] ?></span></td>
          <td>
            <div style="width:56px;height:42px;border-radius:6px;overflow:hidden;background:#0A1628;border:1px solid var(--admin-border);">
              <img src="<?= e($coverImg) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
            </div>
          </td>
          <td>
            <div style="font-weight:700;color:var(--admin-text);margin-bottom:0.2rem;">
              <?= e($t['title']) ?>
              <?php if (!empty($t['is_featured'])): ?>
                <span style="font-size:0.68rem;padding:0.1rem 0.45rem;background:rgba(16,185,129,0.16);color:#34D399;border-radius:999px;margin-left:0.4rem;">Homepage Card</span>
              <?php endif; ?>
            </div>
            <div style="font-size:0.78rem;color:var(--admin-muted);">
              <code><?= e($liveUrl) ?></code> &bull; <?= $galleryCount ?> photos
            </div>
          </td>
          <td><span style="color:var(--admin-gold);font-weight:600;font-size:0.85rem;"><?= e($t['category_name']) ?></span></td>
          <td style="font-size:0.82rem;color:var(--admin-muted);">
            <?= e($t['pickup_line'] ?: $t['duration']) ?><br>
            <span>Pick-up field: <?= (int)($t['has_pickup'] ?? 1) === 1 ? 'Shown' : 'Hidden' ?></span>
          </td>
          <td>
            <strong style="color:var(--admin-text);">AED <?= number_format((float)$t['price_aed'], 0) ?></strong>
            <?php if ((float)($t['child_price_aed'] ?? 0) > 0): ?>
              <div style="font-size:0.76rem;color:var(--admin-muted);">Child: AED <?= number_format((float)$t['child_price_aed'], 0) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <form action="/admin/tours" method="POST" style="display:inline;">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
              <button type="submit" class="badge <?= $t['is_active'] ? 'badge-converted' : 'badge-lost' ?>" style="border:none;cursor:pointer;">
                <?= $t['is_active'] ? 'Active' : 'Hidden' ?>
              </button>
            </form>
          </td>
          <td style="text-align:right;">
            <div style="display:inline-flex;gap:0.4rem;">
              <a href="<?= e($liveUrl) ?>" target="_blank" class="btn-logout" style="padding:0.25rem 0.6rem;font-size:0.8rem;">Live ↗</a>
              <a href="/admin/tours/edit?id=<?= (int)$t['id'] ?>" class="btn-logout" style="padding:0.25rem 0.6rem;font-size:0.8rem;border-color:var(--admin-gold);color:var(--admin-gold);">Edit</a>
              <form action="/admin/tours" method="POST" style="display:inline;" onsubmit="return confirm('Delete this activity?');">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button type="submit" class="btn-logout" style="padding:0.25rem 0.6rem;font-size:0.8rem;border-color:#EF4444;color:#EF4444;cursor:pointer;">Del</button>
              </form>
            </div>
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
