<?php
/**
 * Blog & Knowledge Base Hub — /blog
 * Lists all SEO guides on Dubai business setup, Golden Visas, desert safaris, and UAE sightseeing.
 */
require_once __DIR__ . '/../config.php';

$pageTitle    = 'UAE Business Setup & Travel Guides | Anchor Point Blog';
$pageDesc     = 'Expert guides and practical advice on starting a business in Dubai, UAE Golden Visas, opening corporate bank accounts, and top desert safaris.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/blog';
$ogImage      = '/uploads/services/new-trade-license.jpg';

$posts = getBlogPosts(true);

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>Blog &amp; Knowledge Base</span>
      </nav>
      <h1>UAE Business &amp; Travel Knowledge Base</h1>
      <p>Practical guides, regulatory updates, and expert recommendations curated by our Dubai corporate advisors and licensed tourism specialists.</p>
    </div>
  </section>

  <section class="detail-section" style="padding:3.5rem 0;background:#FFFFFF">
    <div class="container">
      <div class="blog-grid" style="margin-top:0">
        <?php foreach ($posts as $post): ?>
          <article class="blog-card">
            <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-media" aria-label="<?= e($post['title']) ?>">
              <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='/uploads/activities/dubai-city-tour-half-day-cover.webp'">
            </a>
            <div class="blog-card-body">
              <div class="blog-card-meta">
                <span class="blog-card-tag"><?= e($post['category']) ?></span>
                <span>&bull;</span>
                <span><?= e($post['read_time']) ?></span>
              </div>
              <h2 class="blog-card-title" style="font-size:1.15rem">
                <a href="/blog/<?= e($post['slug']) ?>" style="color:inherit;text-decoration:none"><?= e($post['title']) ?></a>
              </h2>
              <p class="blog-card-excerpt"><?= e(truncate($post['excerpt'], 140)) ?></p>
              <div>
                <a href="/blog/<?= e($post['slug']) ?>" style="color:var(--blue-600);font-weight:700;text-decoration:none;font-size:0.92rem">
                  Read Full Guide &rarr;
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
