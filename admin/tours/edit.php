<?php
/**
 * Anchor Point Travel & Tourism — Activity & Price Editor (/admin/tours/edit)
 * Allows staff to edit activity details, AED prices, pick-up toggle, inclusions, and WebP photos without touching code.
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$db        = getDB();
$tourId    = (int)($_GET['id'] ?? 0);
$pageTitle = $tourId ? 'Edit Activity & Pricing' : 'Add New Activity';

$tour = [
    'id'              => 0,
    'category_id'     => 1,
    'title'           => '',
    'slug'            => '',
    'badge'           => '',
    'short_desc'      => '',
    'long_desc'       => '',
    'duration'        => '6 hours',
    'pickup_line'     => '6 hours, hotel pick-up',
    'has_pickup'      => 1,
    'price_aed'       => 150,
    'child_price_aed' => 120,
    'age_policy'      => 'Adults 12+ yrs | Children 3–11 yrs',
    'rating'          => 4.9,
    'reviews_count'   => 120,
    'featured_image'  => '',
    'gallery_json'    => '[]',
    'inclusions_json' => '[]',
    'exclusions_json' => '[]',
    'details_json'    => '{}',
    'pickup_details'  => '',
    'sort_order'      => 1,
    'is_featured'     => 0,
    'is_active'       => 1,
    'seo_title'       => '',
    'seo_desc'        => '',
];

if ($tourId > 0) {
    $st = $db->prepare('SELECT * FROM tours WHERE id = ? LIMIT 1');
    $st->execute([$tourId]);
    $fetched = $st->fetch();
    if ($fetched) $tour = array_merge($tour, $fetched);
}

$categories = getTourCategories();
$error      = '';
$saved      = !empty($_GET['saved']);

if (isPost()) {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        $title         = sanitize($_POST['title'] ?? '');
        $slug          = slugify($_POST['slug'] ?? $title) ?: slugify($title);
        $categoryId    = max(1, (int)($_POST['category_id'] ?? 1));
        $badge         = sanitize($_POST['badge'] ?? '');
        $shortDesc     = sanitize($_POST['short_desc'] ?? '');
        $longDesc      = trim(strip_tags((string)($_POST['long_desc'] ?? '')));
        $duration      = sanitize($_POST['duration'] ?? '');
        $pickupLine    = sanitize($_POST['pickup_line'] ?? '');
        $hasPickup     = !empty($_POST['has_pickup']) ? 1 : 0;
        $priceAed      = max(0, (float)($_POST['price_aed'] ?? 0));
        $childPriceAed = max(0, (float)($_POST['child_price_aed'] ?? 0));
        $agePolicy     = sanitize($_POST['age_policy'] ?? '');
        $featuredImg   = sanitize($_POST['featured_image'] ?? '');
        $sortOrder     = (int)($_POST['sort_order'] ?? 1);
        $isFeatured    = !empty($_POST['is_featured']) ? 1 : 0;
        $isActive      = !empty($_POST['is_active']) ? 1 : 0;
        $seoTitle      = sanitize($_POST['seo_title'] ?? '');
        $seoDesc       = sanitize($_POST['seo_desc'] ?? '');

        $incLines = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", '', $_POST['inclusions_text'] ?? '')))));
        $excLines = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", '', $_POST['exclusions_text'] ?? '')))));
        $galLines = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", '', $_POST['gallery_text'] ?? '')))));

        if ($featuredImg !== '' && !in_array($featuredImg, $galLines, true)) {
            array_unshift($galLines, $featuredImg);
        }

        $existingDetails = json_decode($tour['details_json'] ?? '{}', true);
        if (!is_array($existingDetails)) $existingDetails = [];
        $existingDetails['overview'] = $longDesc;

        if ($title === '') {
            $error = 'Activity title is required.';
        } else {
            if ($tourId > 0) {
                $upd = $db->prepare('
                    UPDATE tours SET
                        category_id = ?, title = ?, slug = ?, badge = ?, short_desc = ?, long_desc = ?,
                        duration = ?, pickup_line = ?, has_pickup = ?, price_aed = ?, child_price_aed = ?,
                        age_policy = ?, featured_image = ?, gallery_json = ?, inclusions_json = ?, exclusions_json = ?,
                        details_json = ?, sort_order = ?, is_featured = ?, is_active = ?, seo_title = ?, seo_desc = ?,
                        updated_at = ?
                    WHERE id = ?
                ');
                $upd->execute([
                    $categoryId, $title, $slug, $badge, $shortDesc, $longDesc,
                    $duration, $pickupLine, $hasPickup, $priceAed, $childPriceAed,
                    $agePolicy, $featuredImg, json_encode($galLines, JSON_UNESCAPED_SLASHES),
                    json_encode($incLines, JSON_UNESCAPED_UNICODE), json_encode($excLines, JSON_UNESCAPED_UNICODE),
                    json_encode($existingDetails, JSON_UNESCAPED_UNICODE),
                    $sortOrder, $isFeatured, $isActive, $seoTitle, $seoDesc,
                    date('Y-m-d H:i:s'), $tourId
                ]);
                logActivity('Updated Activity', 'tours', $tourId, "Updated $title (AED $priceAed)");
                redirect('/admin/tours/edit?id=' . $tourId . '&saved=1');
            } else {
                $ins = $db->prepare('
                    INSERT INTO tours (
                        category_id, title, slug, badge, short_desc, long_desc,
                        duration, pickup_line, has_pickup, price_aed, child_price_aed,
                        age_policy, featured_image, gallery_json, inclusions_json, exclusions_json,
                        details_json, sort_order, is_featured, is_active, seo_title, seo_desc
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ');
                $ins->execute([
                    $categoryId, $title, $slug, $badge, $shortDesc, $longDesc,
                    $duration, $pickupLine, $hasPickup, $priceAed, $childPriceAed,
                    $agePolicy, $featuredImg, json_encode($galLines, JSON_UNESCAPED_SLASHES),
                    json_encode($incLines, JSON_UNESCAPED_UNICODE), json_encode($excLines, JSON_UNESCAPED_UNICODE),
                    json_encode($existingDetails, JSON_UNESCAPED_UNICODE),
                    $sortOrder, $isFeatured, $isActive, $seoTitle, $seoDesc
                ]);
                $newId = (int)$db->lastInsertId();
                logActivity('Created Activity', 'tours', $newId, "Created $title");
                redirect('/admin/tours/edit?id=' . $newId . '&saved=1');
            }
        }
    }
}

$incArr  = json_decode($tour['inclusions_json'] ?? '[]', true) ?: [];
$excArr  = json_decode($tour['exclusions_json'] ?? '[]', true) ?: [];
$galArr  = json_decode($tour['gallery_json'] ?? '[]', true) ?: [];
$detArr  = json_decode($tour['details_json'] ?? '{}', true) ?: [];
$longVal = $detArr['overview'] ?? $tour['long_desc'] ?? '';

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
  <a href="/admin/tours" class="btn-logout">← Back to All Activities</a>
  <?php if ($tourId > 0):
    $catSlugForPreview = 'desert-safari';
    foreach ($categories as $c) {
        if ((int)$c['id'] === (int)$tour['category_id']) $catSlugForPreview = $c['slug'];
    }
  ?>
    <a href="/<?= e($catSlugForPreview . '/' . $tour['slug']) ?>" target="_blank" class="btn-gold">View Live Page ↗</a>
  <?php endif; ?>
</div>

<?php if ($saved): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:#34D399;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1.25rem;font-weight:600;">
    ✓ Activity &amp; pricing changes saved immediately.
  </div>
<?php endif; ?>

<?php if ($error !== ''): ?>
  <div class="admin-alert" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.35);color:#FCA5A5;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1.25rem;font-weight:600;">
    <?= e($error) ?>
  </div>
<?php endif; ?>

<div class="admin-card" style="padding:2rem;max-width:960px;">
  <form action="" method="POST">
    <?= csrfField() ?>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Activity Title (Keep short, max 6 words recommended)</label>
        <input type="text" name="title" class="form-control" required value="<?= e($tour['title']) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Category</label>
        <select name="category_id" class="form-control">
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= (int)$tour['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">URL Slug</label>
        <input type="text" name="slug" class="form-control" value="<?= e($tour['slug']) ?>" placeholder="auto-generated-from-title">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Adult Price (AED)</label>
        <input type="number" step="0.01" min="0" name="price_aed" class="form-control" value="<?= e((string)$tour['price_aed']) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Child Price (AED, 0 if N/A)</label>
        <input type="number" step="0.01" min="0" name="child_price_aed" class="form-control" value="<?= e((string)($tour['child_price_aed'] ?? 0)) ?>">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Duration</label>
        <input type="text" name="duration" class="form-control" value="<?= e($tour['duration']) ?>" placeholder="e.g. 6 hours">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Card 1-Line Duration &amp; Pick-up</label>
        <input type="text" name="pickup_line" class="form-control" value="<?= e($tour['pickup_line'] ?? '') ?>" placeholder="e.g. 6 hours, hotel pick-up">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Optional Badge (e.g. Landmark)</label>
        <input type="text" name="badge" class="form-control" value="<?= e($tour['badge']) ?>">
      </div>
    </div>

    <div style="margin-bottom:1.25rem;">
      <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Age Policy</label>
      <input type="text" name="age_policy" class="form-control" value="<?= e($tour['age_policy'] ?? '') ?>">
    </div>

    <div style="margin-bottom:1.25rem;">
      <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Short Card Summary (1–2 lines)</label>
      <input type="text" name="short_desc" class="form-control" value="<?= e($tour['short_desc']) ?>">
    </div>

    <div style="margin-bottom:1.25rem;">
      <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Full Experience Overview</label>
      <textarea name="long_desc" rows="4" class="form-control"><?= e($longVal) ?></textarea>
    </div>

    <!-- Cover Photo & Gallery Uploader -->
    <div style="margin-bottom:1.25rem;padding:1rem;border:1px solid var(--admin-border);border-radius:8px;">
      <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Featured 4:3 Cover Image (WebP auto-converted on upload)</label>
      <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <input type="text" id="featured_image_input" name="featured_image" class="form-control" style="flex:1;min-width:240px;" value="<?= e($tour['featured_image']) ?>">
        <input type="file" id="cover_upload_file" accept="image/*" style="max-width:230px;">
      </div>
      <div style="margin-top:0.75rem;">
        <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:0.3rem;">Gallery Image URLs (One per line, 6–10 photos recommended)</label>
        <textarea id="gallery_textarea" name="gallery_text" rows="4" class="form-control"><?= e(implode("\n", $galArr)) ?></textarea>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Included Items (One per line)</label>
        <textarea name="inclusions_text" rows="5" class="form-control"><?= e(implode("\n", $incArr)) ?></textarea>
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Not Included Items (One per line)</label>
        <textarea name="exclusions_text" rows="5" class="form-control"><?= e(implode("\n", $excArr)) ?></textarea>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;margin-bottom:1.5rem;align-items:center;">
      <div>
        <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:0.3rem;">Display Order</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)$tour['sort_order'] ?>">
      </div>
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;margin-top:1.2rem;">
        <input type="checkbox" name="has_pickup" value="1" <?= (int)($tour['has_pickup'] ?? 1) === 1 ? 'checked' : '' ?>>
        <span>Includes Hotel Pick-up</span>
      </label>
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;margin-top:1.2rem;">
        <input type="checkbox" name="is_featured" value="1" <?= !empty($tour['is_featured']) ? 'checked' : '' ?>>
        <span>Show on Homepage</span>
      </label>
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;margin-top:1.2rem;">
        <input type="checkbox" name="is_active" value="1" <?= !empty($tour['is_active']) ? 'checked' : '' ?>>
        <span>Published / Active</span>
      </label>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">SEO Title</label>
        <input type="text" name="seo_title" class="form-control" value="<?= e($tour['seo_title']) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">SEO Meta Description</label>
        <input type="text" name="seo_desc" class="form-control" value="<?= e($tour['seo_desc']) ?>">
      </div>
    </div>

    <button type="submit" class="btn-gold" style="padding:0.75rem 2rem;border:none;cursor:pointer;font-weight:700;">Save Activity &amp; Pricing</button>
  </form>
</div>

<script>
document.getElementById('cover_upload_file')?.addEventListener('change', async function () {
  if (!this.files || !this.files.length) return;
  const fd = new FormData();
  fd.append('image', this.files[0]);
  try {
    const res = await fetch('/api/upload-tour-image.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success && data.url) {
      document.getElementById('featured_image_input').value = data.url;
      const gt = document.getElementById('gallery_textarea');
      if (gt && !gt.value.includes(data.url)) {
        gt.value = data.url + (gt.value.trim() ? '\n' + gt.value.trim() : '');
      }
    } else {
      alert(data.error || 'Upload failed');
    }
  } catch (err) {
    alert('Upload error');
  }
});
</script>

</div><!-- .admin-content -->
</div><!-- .admin-main -->
<script src="/assets/js/admin.js"></script>
</body>
</html>
