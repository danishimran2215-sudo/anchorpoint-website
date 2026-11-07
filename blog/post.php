<?php
/**
 * Single Blog Article — /blog/<slug>
 * Semantic H1-H3 structure, Schema.org BlogPosting JSON-LD, Internal Links, and CTA
 */
require_once __DIR__ . '/../config.php';

$slug = sanitize($_GET['slug'] ?? '');
$post = getBlogPostBySlug($slug);

if (!$post) {
    http_response_code(404);
    require ROOT_PATH . '/404.php';
    exit;
}

$pageTitle    = !empty($post['seo_title']) ? $post['seo_title'] : ($post['title'] . ' | Anchor Point UAE');
$pageDesc     = !empty($post['seo_desc']) ? $post['seo_desc'] : $post['excerpt'];
$canonicalUrl = rtrim(SITE_URL, '/') . '/blog/' . $post['slug'];
$ogImage      = $post['featured_image'];

$relatedPosts = getRecentBlogPosts(3, (int)$post['id']);

$articleSchema = [
    '@context'         => 'https://schema.org',
    '@type'            => 'BlogPosting',
    'headline'         => $post['title'],
    'description'      => $post['excerpt'],
    'image'            => str_starts_with($post['featured_image'], 'http') ? $post['featured_image'] : (rtrim(SITE_URL, '/') . $post['featured_image']),
    'author'           => [
        '@type' => 'Person',
        'name'  => $post['author'],
    ],
    'publisher'        => [
        '@type' => 'Organization',
        'name'  => getSetting('site_name', SITE_NAME),
        'logo'  => [
            '@type' => 'ImageObject',
            'url'   => rtrim(SITE_URL, '/') . '/assets/images/logo.svg',
        ],
    ],
    'datePublished'    => $post['published_at'],
    'dateModified'     => $post['created_at'],
    'mainEntityOfPage' => $canonicalUrl,
];

$jsonLd = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => rtrim(SITE_URL, '/') . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => rtrim(SITE_URL, '/') . '/blog'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => $canonicalUrl],
            ],
        ],
        $articleSchema,
    ],
];

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <article class="blog-article">
    <header class="page-hero" style="padding:3.5rem 0 2.5rem">
      <div class="container" style="max-width:880px">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="/">Home</a>
          <span aria-hidden="true">/</span>
          <a href="/blog">Blog</a>
          <span aria-hidden="true">/</span>
          <span><?= e($post['category']) ?></span>
        </nav>
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;font-size:0.88rem;color:rgba(255,255,255,0.75)">
          <span style="background:rgba(255,255,255,0.15);padding:3px 10px;border-radius:12px;color:#FBBF24;font-weight:700"><?= e($post['category']) ?></span>
          <span>&bull;</span>
          <span><?= e($post['read_time']) ?></span>
          <span>&bull;</span>
          <span>By <?= e($post['author']) ?></span>
        </div>
        <h1 style="font-size:2.2rem;line-height:1.25"><?= e($post['title']) ?></h1>
      </div>
    </header>

    <div class="container" style="max-width:880px;padding:3rem 1.25rem">
      <div style="border-radius:16px;overflow:hidden;margin-bottom:2.5rem;box-shadow:0 10px 30px rgba(11,37,69,0.08)">
        <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" style="width:100%;height:auto;display:block">
      </div>

      <!-- Article Body with Rich Content & Internal Links -->
      <div class="article-content" style="font-size:1.06rem;line-height:1.8;color:var(--slate-800)">
        <?= $post['content'] ?>
      </div>

      <!-- Contextual Call to Action -->
      <div style="background:var(--slate-50);border:1px solid var(--slate-200);border-left:5px solid #1FA855;border-radius:12px;padding:1.75rem 2rem;margin:3rem 0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.25rem">
        <div>
          <h3 style="margin:0 0 0.35rem;font-size:1.25rem;color:var(--navy-900)">Have Questions or Need Immediate Assistance?</h3>
          <p style="margin:0;font-size:0.95rem;color:var(--slate-600)">Connect with our Dubai specialists on WhatsApp for fast answers and instant bookings.</p>
        </div>
        <a href="<?= e(waLinkHeader()) ?>" target="_blank" rel="noopener nofollow" class="btn btn-wa" style="padding:0.75rem 1.4rem">
          Chat on WhatsApp
        </a>
      </div>

      <!-- Related Guides -->
      <?php if (!empty($relatedPosts)): ?>
        <div style="margin-top:4rem;padding-top:2.5rem;border-top:1px solid var(--slate-200)">
          <h2 style="font-size:1.5rem;color:var(--navy-900);margin-bottom:1.5rem">Recommended Guides</h2>
          <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:1.25rem">
            <?php foreach ($relatedPosts as $rel): ?>
              <a href="/blog/<?= e($rel['slug']) ?>" style="text-decoration:none;color:inherit;background:#FFFFFF;border:1px solid var(--slate-200);border-radius:12px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 2px 6px rgba(0,0,0,0.04)">
                <img src="<?= e($rel['featured_image']) ?>" alt="<?= e($rel['title']) ?>" style="width:100%;aspect-ratio:16/9;object-fit:cover">
                <div style="padding:1rem;flex:1;display:flex;flex-direction:column">
                  <span style="font-size:0.75rem;font-weight:700;color:var(--blue-600);margin-bottom:0.35rem"><?= e($rel['category']) ?></span>
                  <h4 style="font-size:0.98rem;margin:0 0 0.5rem;color:var(--navy-900);line-height:1.4"><?= e($rel['title']) ?></h4>
                  <span style="margin-top:auto;font-size:0.85rem;color:var(--blue-600);font-weight:700">Read &rarr;</span>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </article>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
