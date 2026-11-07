<?php
/**
 * Category Landing Page — /desert-safari, /city-tours, /cruise-dinner, /watersports, /tickets-attractions
 * PDF Sections 1, 4–8
 */
require_once __DIR__ . '/../config.php';

$catSlug  = sanitize($_GET['category_slug'] ?? $_GET['category'] ?? '');
$category = $catSlug !== '' ? getTourCategoryBySlug($catSlug) : null;

if (!$category) {
    http_response_code(404);
    require ROOT_PATH . '/404.php';
    exit;
}

$tours              = getTours(['category_id' => (int)$category['id']]);
$showPrices         = arePricesVisible();
$activeCategorySlug = $category['slug'];
$sectionHeading     = !empty($category['section_title']) ? $category['section_title'] : $category['name'];

$pageTitle    = !empty($category['seo_title']) ? $category['seo_title'] : ($sectionHeading . ' | ' . SITE_NAME);
$pageDesc     = !empty($category['seo_desc'])  ? $category['seo_desc']  : $category['description'];
$canonicalUrl = rtrim(SITE_URL, '/') . '/' . $category['slug'];
$ogImage      = !empty($tours[0]['featured_image']) ? $tours[0]['featured_image'] : '/uploads/activities/evening-desert-safari-cover.webp';

$itemListElements = [];
foreach ($tours as $idx => $t) {
    $itemListElements[] = [
        '@type'    => 'ListItem',
        'position' => $idx + 1,
        'name'     => $t['title'],
        'url'      => rtrim(SITE_URL, '/') . activityUrl($t),
    ];
}

$jsonLd = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => rtrim(SITE_URL, '/') . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $category['name'], 'item' => $canonicalUrl],
            ],
        ],
        [
            '@type'           => 'ItemList',
            'name'            => $sectionHeading,
            'description'     => $category['description'],
            'numberOfItems'   => count($tours),
            'itemListElement' => $itemListElements,
        ],
    ],
];

$catWaMessage = 'Hi, please help me choose the best ' . $category['name'] . ' option in the UAE.';
$catWaUrl     = buildWhatsAppLink('', $catWaMessage);

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span><?= e($category['name']) ?></span>
      </nav>
      <h1><?= e($sectionHeading) ?></h1>
      <p><?= e($category['description']) ?></p>
    </div>
  </section>

  <section class="category-section" style="border-bottom:0">
    <div class="container">
      <?php if ($category['slug'] === 'tickets-attractions'): 
        $groups = getTicketGroups();
      ?>
        <div class="ticket-filter-bar" role="tablist" aria-label="Attraction Categories">
          <button type="button" class="ticket-filter-pill is-active" data-group="all" role="tab" aria-selected="true">
            All Attractions (<?= count($tours) ?>)
          </button>
          <?php foreach ($groups as $gKey => $gMeta): ?>
            <button type="button" class="ticket-filter-pill" data-group="<?= e($gKey) ?>" role="tab" aria-selected="false">
              <?= e($gMeta['icon']) ?> <?= e($gMeta['short']) ?>
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="activity-grid">
        <?php foreach ($tours as $tour): ?>
          <?php require INCLUDES_PATH . 'activity-card.php'; ?>
        <?php endforeach; ?>
      </div>

      <div class="category-cta-box">
        <p><?= e(!empty($category['cta_text']) ? $category['cta_text'] : ('Need help choosing the right ' . $category['name'] . ' experience? Message our team on WhatsApp.')) ?></p>
        <a href="<?= e($catWaUrl) ?>"
           target="_blank"
           rel="noopener"
           class="btn btn-wa"
           data-wa-location="category_cta"
           data-wa-activity="<?= e($category['name']) ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
          Chat on WhatsApp
        </a>
      </div>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
