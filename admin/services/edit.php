<?php
/**
 * Anchor Point — Admin Service Edit / Create
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$service = null;

if ($id > 0) {
    $st = $db->prepare('SELECT * FROM services WHERE id = ?');
    $st->execute([$id]);
    $service = $st->fetch();
}

$isNew = !$service;
$pageTitle = $isNew ? 'Add New Corporate Service' : ('Edit Service: ' . $service['title']);

$categories = getServiceCategories();
$error = '';
$success = '';

if (isPost() && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $catId    = (int)($_POST['category_id'] ?? 1);
    $title    = sanitize($_POST['title'] ?? '');
    $slug     = slugify($_POST['slug'] ?? $title);
    $badge    = sanitize($_POST['badge'] ?? '');
    $shortDesc= sanitize($_POST['short_desc'] ?? '');
    $content  = $_POST['content'] ?? '';
    $image    = sanitize($_POST['featured_image'] ?? '/uploads/services/new-trade-license.jpg');
    $price    = (float)($_POST['price_starting_aed'] ?? 0);
    $timeline = sanitize($_POST['timeline'] ?? '');
    $sort     = (int)($_POST['sort_order'] ?? 0);
    $isActive = !empty($_POST['is_active']) ? 1 : 0;
    $isFeat   = !empty($_POST['is_featured']) ? 1 : 0;
    $seoTitle = sanitize($_POST['seo_title'] ?? '');
    $seoDesc  = sanitize($_POST['seo_desc'] ?? '');

    if ($title === '') {
        $error = 'Service title is required.';
    } else {
        if ($isNew) {
            $ins = $db->prepare('
                INSERT INTO services (category_id, title, slug, badge, short_desc, content, featured_image, price_starting_aed, timeline, sort_order, is_featured, is_active, seo_title, seo_desc)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $ins->execute([$catId, $title, $slug, $badge, $shortDesc, $content, $image, $price, $timeline, $sort, $isFeat, $isActive, $seoTitle, $seoDesc]);
            $newId = (int)$db->lastInsertId();
            logActivity('Created Service', 'services', $newId);
            redirect('/admin/services');
        } else {
            $upd = $db->prepare('
                UPDATE services SET
                    category_id = ?, title = ?, slug = ?, badge = ?, short_desc = ?, content = ?,
                    featured_image = ?, price_starting_aed = ?, timeline = ?, sort_order = ?,
                    is_featured = ?, is_active = ?, seo_title = ?, seo_desc = ?
                WHERE id = ?
            ');
            $upd->execute([$catId, $title, $slug, $badge, $shortDesc, $content, $image, $price, $timeline, $sort, $isFeat, $isActive, $seoTitle, $seoDesc, $id]);
            logActivity('Updated Service', 'services', $id);
            $success = 'Service updated successfully!';
            // Refresh
            $st = $db->prepare('SELECT * FROM services WHERE id = ?');
            $st->execute([$id]);
            $service = $st->fetch();
        }
    }
}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;">
  <a href="/admin/services" style="color:var(--admin-muted);font-size:0.88rem;text-decoration:none;">&larr; Back to Services</a>
  <?php if (!$isNew): ?>
    <a href="/services/<?= e($service['slug']) ?>" target="_blank" class="btn btn-outline" style="font-size:0.82rem;padding:0.4rem 0.8rem;">View on Website ↗</a>
  <?php endif; ?>
</div>

<?php if ($error): ?>
  <div class="admin-alert" style="background:rgba(220,38,38,0.15);color:#F87171;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);color:#34D399;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;"><?= e($success) ?></div>
<?php endif; ?>

<form method="POST" action="">
  <?= csrfField() ?>
  <div class="admin-card" style="padding:1.75rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1.1rem;margin:0 0 1.25rem;">Core Service Details</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label class="form-label">Service Title *</label>
        <input type="text" name="title" class="form-control" value="<?= e($service['title'] ?? '') ?>" required>
      </div>
      <div>
        <label class="form-label">URL Slug</label>
        <input type="text" name="slug" class="form-control" value="<?= e($service['slug'] ?? '') ?>" placeholder="leave blank to auto-generate">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label class="form-label">Category</label>
        <select name="category_id" class="form-control">
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= ((int)($service['category_id'] ?? 1) === (int)$cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label">Highlight Badge</label>
        <input type="text" name="badge" class="form-control" value="<?= e($service['badge'] ?? '') ?>" placeholder="e.g. 100% Foreign Ownership">
      </div>
      <div>
        <label class="form-label">Starting Price (AED)</label>
        <input type="number" step="0.01" name="price_starting_aed" class="form-control" value="<?= e($service['price_starting_aed'] ?? '0') ?>">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label class="form-label">Timeline</label>
        <input type="text" name="timeline" class="form-control" value="<?= e($service['timeline'] ?? '2–4 business days') ?>">
      </div>
      <div>
        <label class="form-label">Image Path</label>
        <input type="text" name="featured_image" class="form-control" value="<?= e($service['featured_image'] ?? '/uploads/services/new-trade-license.jpg') ?>">
      </div>
      <div>
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)($service['sort_order'] ?? 0) ?>">
      </div>
    </div>

    <div style="margin-bottom:1.25rem;">
      <label class="form-label">Short Description (Summary)</label>
      <textarea name="short_desc" class="form-control" rows="2"><?= e($service['short_desc'] ?? '') ?></textarea>
    </div>

    <div style="margin-bottom:1.25rem;">
      <label class="form-label">Full Service Content (HTML)</label>
      <textarea name="content" class="form-control" rows="10"><?= htmlspecialchars((string)($service['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div style="display:flex;gap:2rem;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--admin-border);">
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1" <?= (!isset($service['is_active']) || $service['is_active']) ? 'checked' : '' ?>>
        <strong>Active on Website</strong>
      </label>
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
        <input type="checkbox" name="is_featured" value="1" <?= (!empty($service['is_featured'])) ? 'checked' : '' ?>>
        <strong>Showcase on Homepage</strong>
      </label>
    </div>
  </div>

  <div class="admin-card" style="padding:1.75rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1.1rem;margin:0 0 1.25rem;">SEO Meta Configuration</h3>
    <div style="margin-bottom:1.25rem;">
      <label class="form-label">SEO Title (&lt; 60 chars)</label>
      <input type="text" name="seo_title" class="form-control" value="<?= e($service['seo_title'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label">SEO Meta Description (&lt; 155 chars)</label>
      <textarea name="seo_desc" class="form-control" rows="2"><?= e($service['seo_desc'] ?? '') ?></textarea>
    </div>
  </div>

  <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;font-size:0.95rem;">Save Service</button>
</form>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
