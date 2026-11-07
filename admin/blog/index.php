<?php
/**
 * Anchor Point — Admin Blog Articles Manager
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Blog & SEO Articles Manager';
$db = getDB();

if (isPost() && isset($_POST['action']) && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    if ($_POST['action'] === 'toggle') {
        $blogId = (int)$_POST['id'];
        $db->prepare('UPDATE blog_posts SET is_published = (1 - is_published) WHERE id = ?')->execute([$blogId]);
        logActivity('Toggled Blog Status', 'blog_posts', $blogId);
    } elseif ($_POST['action'] === 'delete') {
        $blogId = (int)$_POST['id'];
        $db->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([$blogId]);
        logActivity('Deleted Blog Post', 'blog_posts', $blogId);
    }
    redirect('/admin/blog');
}

$posts = getBlogPosts(false);

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
  <div>
    <h2 style="font-size:1.15rem;font-weight:700;margin:0 0 0.25rem;">SEO Blog Articles (<?= count($posts) ?>)</h2>
    <p style="margin:0;font-size:0.85rem;color:var(--admin-muted);">Manage high-ranking informational articles for internal linking and organic traffic.</p>
  </div>
  <a href="/admin/blog/edit" class="btn btn-primary" style="padding:0.6rem 1.25rem;font-size:0.88rem;">+ Write New Article</a>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:50px;">Image</th>
          <th>Article Title</th>
          <th>Category</th>
          <th>Author</th>
          <th>Read Time</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td>
              <img src="<?= e($p['featured_image']) ?>" alt="" style="width:48px;height:36px;object-fit:cover;border-radius:6px;border:1px solid var(--admin-border)">
            </td>
            <td>
              <strong><a href="/blog/<?= e($p['slug']) ?>" target="_blank" style="color:var(--admin-text);text-decoration:none;"><?= e($p['title']) ?></a></strong>
            </td>
            <td><span style="font-size:0.85rem;color:var(--admin-muted);"><?= e($p['category']) ?></span></td>
            <td><span style="font-size:0.85rem;"><?= e($p['author']) ?></span></td>
            <td><span style="font-size:0.85rem;color:var(--admin-muted);"><?= e($p['read_time']) ?></span></td>
            <td>
              <form method="POST" action="/admin/blog" style="display:inline;">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button type="submit" class="badge-status <?= $p['is_published'] ? 'badge-confirmed' : 'badge-cancelled' ?>" style="cursor:pointer;border:none;">
                  <?= $p['is_published'] ? 'Published' : 'Draft' ?>
                </button>
              </form>
            </td>
            <td style="text-align:right;">
              <a href="/admin/blog/edit?id=<?= (int)$p['id'] ?>" class="btn-action" title="Edit Article">Edit</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
