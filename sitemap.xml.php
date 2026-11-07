<?php
/**
 * Anchor Point Travel & Tourism — Dynamic XML Sitemap (Section 1 & Section 13)
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=UTF-8');

$db = getDB();
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? '';
$siteUrl = ($host !== '' && !str_starts_with($host, 'localhost') && !str_starts_with($host, '127.0.0.1'))
    ? ($scheme . '://' . $host)
    : rtrim(SITE_URL, '/');

$pages = [
    ['loc' => $siteUrl . '/',                    'priority' => '1.0',  'changefreq' => 'daily'],
    ['loc' => $siteUrl . '/services',            'priority' => '0.95', 'changefreq' => 'weekly'],
    ['loc' => $siteUrl . '/blog',                'priority' => '0.90', 'changefreq' => 'weekly'],
    ['loc' => $siteUrl . '/book',                'priority' => '0.85', 'changefreq' => 'weekly'],
    ['loc' => $siteUrl . '/contact',             'priority' => '0.80', 'changefreq' => 'monthly'],
    ['loc' => $siteUrl . '/privacy',             'priority' => '0.40', 'changefreq' => 'yearly'],
    ['loc' => $siteUrl . '/terms',               'priority' => '0.40', 'changefreq' => 'yearly'],
    ['loc' => $siteUrl . '/cancellation-policy', 'priority' => '0.40', 'changefreq' => 'yearly'],
];

// 5 Category Landing Pages (/desert-safari, /city-tours, /cruise-dinner, /watersports, /tickets-attractions)
foreach (getTourCategories() as $cat) {
    $pages[] = [
        'loc'        => $siteUrl . '/' . $cat['slug'],
        'priority'   => '0.95',
        'changefreq' => 'weekly',
    ];
}

// 35 Individual Activity Pages (/<category>/<activity-name>)
foreach (getTours() as $tour) {
    $pages[] = [
        'loc'        => $siteUrl . activityUrl($tour),
        'priority'   => '0.90',
        'changefreq' => 'weekly',
        'lastmod'    => !empty($tour['updated_at']) ? date('Y-m-d', strtotime($tour['updated_at'])) : date('Y-m-d'),
    ];
}

// Corporate Business Setup Service Pages (/services/<slug>)
foreach (getServices(true) as $serv) {
    $pages[] = [
        'loc'        => $siteUrl . '/services/' . $serv['slug'],
        'priority'   => '0.88',
        'changefreq' => 'weekly',
        'lastmod'    => date('Y-m-d'),
    ];
}

// SEO Blog Article Pages (/blog/<slug>)
foreach (getBlogPosts(true) as $post) {
    $pages[] = [
        'loc'        => $siteUrl . '/blog/' . $post['slug'],
        'priority'   => '0.85',
        'changefreq' => 'weekly',
        'lastmod'    => !empty($post['published_at']) ? date('Y-m-d', strtotime($post['published_at'])) : date('Y-m-d'),
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
  <url>
    <loc><?= htmlspecialchars($p['loc'], ENT_XML1, 'UTF-8') ?></loc>
    <lastmod><?= $p['lastmod'] ?? date('Y-m-d') ?></lastmod>
    <changefreq><?= $p['changefreq'] ?></changefreq>
    <priority><?= $p['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
