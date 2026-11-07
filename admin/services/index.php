<?php
/**
 * Anchor Point — Admin Services Manager
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Business Setup Services Manager';
$db = getDB();

if (isPost() && isset($_POST['action']) && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    if ($_POST['action'] === 'toggle') {
        $servId = (int)$_POST['id'];
        $db->prepare('UPDATE services SET is_active = (1 - is_active) WHERE id = ?')->execute([$servId]);
        logActivity('Toggled Service Status', 'services', $servId);
    } elseif ($_POST['action'] === 'toggle_featured') {
        $servId = (int)$_POST['id'];
        $db->prepare('UPDATE services SET is_featured = (1 - is_featured) WHERE id = ?')->execute([$servId]);
        logActivity('Toggled Service Featured', 'services', $servId);
    } elseif ($_POST['action'] === 'delete') {
        $servId = (int)$_POST['id'];
        $db->prepare('DELETE FROM services WHERE id = ?')->execute([$servId]);
        logActivity('Deleted Service', 'services', $servId);
    }
    redirect('/admin/services');
}

$services = getServices(false);
$categories = getServiceCategories();

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
  <div>
    <h2 style="font-size:1.15rem;font-weight:700;margin:0 0 0.25rem;">Business Setup &amp; Corporate Services (<?= count($services) ?>)</h2>
    <p style="margin:0;font-size:0.85rem;color:var(--admin-muted);">Manage mainland, free zone, visa, PRO, and banking service cards.</p>
  </div>
  <a href="/admin/services/edit" class="btn btn-primary" style="padding:0.6rem 1.25rem;font-size:0.88rem;">+ Add New Service</a>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:50px;">Image</th>
          <th>Service Title</th>
          <th>Category</th>
          <th>Starting Price</th>
          <th>Timeline</th>
          <th>Featured</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td>
              <img src="<?= e($s['featured_image']) ?>" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:6px;border:1px solid var(--admin-border)">
            </td>
            <td>
              <strong><a href="/services/<?= e($s['slug']) ?>" target="_blank" style="color:var(--admin-text);text-decoration:none;"><?= e($s['title']) ?></a></strong>
              <?php if (!empty($s['badge'])): ?>
                <span style="font-size:0.72rem;background:rgba(245,158,11,0.15);color:#FBBF24;padding:2px 6px;border-radius:4px;margin-left:4px;"><?= e($s['badge']) ?></span>
              <?php endif; ?>
            </td>
            <td><span style="font-size:0.85rem;color:var(--admin-muted);"><?= e($s['category_name']) ?></span></td>
            <td><strong>AED <?= number_format((float)$s['price_starting_aed']) ?></strong></td>
            <td><span style="font-size:0.85rem;color:var(--admin-muted);"><?= e($s['timeline']) ?></span></td>
            <td>
              <form method="POST" action="/admin/services" style="display:inline;">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="toggle_featured">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button type="submit" class="badge-status <?= $s['is_featured'] ? 'badge-confirmed' : 'badge-cancelled' ?>" style="cursor:pointer;border:none;">
                  <?= $s['is_featured'] ? '★ Featured' : 'Standard' ?>
                </button>
              </form>
            </td>
            <td>
              <form method="POST" action="/admin/services" style="display:inline;">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button type="submit" class="badge-status <?= $s['is_active'] ? 'badge-confirmed' : 'badge-cancelled' ?>" style="cursor:pointer;border:none;">
                  <?= $s['is_active'] ? 'Active' : 'Hidden' ?>
                </button>
              </form>
            </td>
            <td style="text-align:right;">
              <a href="/admin/services/edit?id=<?= (int)$s['id'] ?>" class="btn-action" title="Edit Service">Edit</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
