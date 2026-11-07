<?php
/**
 * Anchor Point — Admin Blog Edit / Create
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$post = null;

if ($id > 0) {
    $st = $db->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $st->execute([$id]);
    $post = $st->fetch();
}

$isNew = !$post;
$pageTitle = $isNew ? 'Write New SEO Article' : ('Edit Article: ' . $post['title']);

$error = '';
$success = '';

if (isPost() && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    $title    = sanitize($_POST['title'] ?? '');
    $slug     = slugify($_POST['slug'] ?? $title);
    $category = sanitize($_POST['category'] ?? 'Business Setup');
    $excerpt  = sanitize($_POST['excerpt'] ?? '');
    $content  = $_POST['content'] ?? '';
    $image    = sanitize($_POST['featured_image'] ?? '/uploads/services/new-trade-license.jpg');
    $author   = sanitize($_POST['author'] ?? 'Anchor Point Corporate Desk');
    $readTime = sanitize($_POST['read_time'] ?? '5 min read');
    $isPub    = !empty($_POST['is_published']) ? 1 : 0;
    $seoTitle = sanitize($_POST['seo_title'] ?? '');
    $seoDesc  = sanitize($_POST['seo_desc'] ?? '');

    if ($title === '') {
        $error = 'Article title is required.';
    } else {
        if ($isNew) {
            $ins = $db->prepare('
                INSERT INTO blog_posts (title, slug, category, excerpt, content, featured_image, author, read_time, is_published, seo_title, seo_desc)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $ins->execute([$title, $slug, $category, $excerpt, $content, $image, $author, $readTime, $isPub, $seoTitle, $seoDesc]);
            $newId = (int)$db->lastInsertId();
            logActivity('Created Blog Post', 'blog_posts', $newId);
            redirect('/admin/blog');
        } else {
            $upd = $db->prepare('
                UPDATE blog_posts SET
                    title = ?, slug = ?, category = ?, excerpt = ?, content = ?,
                    featured_image = ?, author = ?, read_time = ?, is_published = ?,
                    seo_title = ?, seo_desc = ?
                WHERE id = ?
            ');
            $upd->execute([$title, $slug, $category, $excerpt, $content, $image, $author, $readTime, $isPub, $seoTitle, $seoDesc, $id]);
            logActivity('Updated Blog Post', 'blog_posts', $id);
            $success = 'Article updated successfully!';
            // Refresh
            $st = $db->prepare('SELECT * FROM blog_posts WHERE id = ?');
            $st->execute([$id]);
            $post = $st->fetch();
        }
    }
}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;">
  <a href="/admin/blog" style="color:var(--admin-muted);font-size:0.88rem;text-decoration:none;">&larr; Back to Articles</a>
  <?php if (!$isNew): ?>
    <a href="/blog/<?= e($post['slug']) ?>" target="_blank" class="btn btn-outline" style="font-size:0.82rem;padding:0.4rem 0.8rem;">View on Website ↗</a>
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
    <h3 style="font-size:1.1rem;margin:0 0 1.25rem;">Article Details</h3>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label class="form-label">Article Title *</label>
        <input type="text" name="title" class="form-control" value="<?= e($post['title'] ?? '') ?>" required>
      </div>
      <div>
        <label class="form-label">URL Slug</label>
        <input type="text" name="slug" class="form-control" value="<?= e($post['slug'] ?? '') ?>" placeholder="leave blank to auto-generate">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="<?= e($post['category'] ?? 'Business Setup') ?>">
      </div>
      <div>
        <label class="form-label">Author Name</label>
        <input type="text" name="author" class="form-control" value="<?= e($post['author'] ?? 'Anchor Point Corporate Desk') ?>">
      </div>
      <div>
        <label class="form-label">Read Time</label>
        <input type="text" name="read_time" class="form-control" value="<?= e($post['read_time'] ?? '5 min read') ?>">
      </div>
    </div>

    <div style="margin-bottom:1.25rem;">
      <label class="form-label">Featured Image Path</label>
      <input type="text" name="featured_image" class="form-control" value="<?= e($post['featured_image'] ?? '/uploads/services/new-trade-license.jpg') ?>">
    </div>

    <div style="margin-bottom:1.25rem;">
      <label class="form-label">Excerpt (Summary)</label>
      <textarea name="excerpt" class="form-control" rows="2"><?= e($post['excerpt'] ?? '') ?></textarea>
    </div>

    <div style="margin-bottom:1.25rem;">
      <label class="form-label">Full Article Content (HTML with H2, H3, Links)</label>
      <textarea name="content" class="form-control" rows="14"><?= htmlspecialchars((string)($post['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div style="padding-top:1.25rem;border-top:1px solid var(--admin-border);">
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
        <input type="checkbox" name="is_published" value="1" <?= (!isset($post['is_published']) || $post['is_published']) ? 'checked' : '' ?>>
        <strong>Publish Immediately</strong>
      </label>
    </div>
  </div>

  <div class="admin-card" style="padding:1.75rem;margin-bottom:1.5rem;">
    <h3 style="font-size:1.1rem;margin:0 0 1.25rem;">SEO Meta Optimization</h3>
    <div style="margin-bottom:1.25rem;">
      <label class="form-label">SEO Title (&lt; 60 chars)</label>
      <input type="text" name="seo_title" class="form-control" value="<?= e($post['seo_title'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label">SEO Meta Description (&lt; 155 chars)</label>
      <textarea name="seo_desc" class="form-control" rows="2"><?= e($post['seo_desc'] ?? '') ?></textarea>
    </div>
  </div>

  <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;font-size:0.95rem;">Save Article</button>
</form>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
