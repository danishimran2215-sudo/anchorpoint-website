<?php
/**
 * Single Service Detail Page — /services/<slug>
 * Rich Schema.org Service JSON-LD, Deliverables, Requirements, FAQs, and WhatsApp CTA
 */
require_once __DIR__ . '/../config.php';

$slug = sanitize($_GET['slug'] ?? '');
$service = getServiceBySlug($slug);

if (!$service) {
    http_response_code(404);
    require ROOT_PATH . '/404.php';
    exit;
}

$pageTitle    = !empty($service['seo_title']) ? $service['seo_title'] : ($service['title'] . ' | Anchor Point Dubai Business Setup');
$pageDesc     = !empty($service['seo_desc']) ? $service['seo_desc'] : $service['short_desc'];
$canonicalUrl = rtrim(SITE_URL, '/') . '/services/' . $service['slug'];
$ogImage      = $service['featured_image'];

$deliverables = json_decode($service['deliverables_json'] ?? '[]', true) ?: [];
$requirements = json_decode($service['requirements_json'] ?? '[]', true) ?: [];
$faqs         = json_decode($service['faq_json'] ?? '[]', true) ?: [];

$serviceSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'name'        => $service['title'],
    'description' => $service['short_desc'],
    'provider'    => [
        '@type'   => 'LocalBusiness',
        'name'    => getSetting('site_name', SITE_NAME),
        'address' => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => getSetting('office_address', OFFICE_ADDRESS),
            'addressLocality' => 'Dubai',
            'addressCountry'  => 'AE',
        ],
        'telephone' => getPrimaryPhone(),
    ],
    'areaServed' => 'United Arab Emirates',
];

if (areServicePricesVisible() && (float)$service['price_starting_aed'] > 0) {
    $serviceSchema['offers'] = [
        '@type'         => 'Offer',
        'priceCurrency' => 'AED',
        'price'         => number_format((float)$service['price_starting_aed'], 2, '.', ''),
    ];
}

$jsonLd = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => rtrim(SITE_URL, '/') . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Business Setup Services', 'item' => rtrim(SITE_URL, '/') . '/services'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $service['title'], 'item' => $canonicalUrl],
            ],
        ],
        $serviceSchema,
    ],
];

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <a href="/services">Business Setup</a>
        <span aria-hidden="true">/</span>
        <span><?= e($service['title']) ?></span>
      </nav>
      <?php if (!empty($service['badge'])): ?>
        <span class="service-badge" style="margin-bottom:0.75rem"><?= e($service['badge']) ?></span>
      <?php endif; ?>
      <h1><?= e($service['title']) ?></h1>
      <p><?= e($service['short_desc']) ?></p>
    </div>
  </section>

  <section class="detail-section" style="padding:3rem 0">
    <div class="container">
      <div class="detail-grid">
        <div class="detail-main">
          <!-- Featured Image -->
          <div style="border-radius:14px;overflow:hidden;margin-bottom:2rem;box-shadow:0 8px 24px rgba(11,37,69,0.08)">
            <img src="<?= e(serviceImageUrl($service['featured_image'], $service['category_slug'] ?? '')) ?>" alt="<?= e($service['title']) ?>" style="width:100%;height:auto;display:block" onerror="this.onerror=null;this.src='/uploads/services/new-trade-license.jpg';">
          </div>

          <!-- Main Content -->
          <div class="info-block" style="font-size:1.02rem;line-height:1.75">
            <?= $service['content'] ?>
          </div>

          <!-- Deliverables Checklist -->
          <?php if (!empty($deliverables)): ?>
          <div class="info-block">
            <h2>Key Inclusions &amp; Deliverables</h2>
            <ul style="display:grid;gap:0.75rem;padding-left:1.25rem;color:var(--slate-700)">
              <?php foreach ($deliverables as $del): ?>
                <li><strong><?= e($del) ?></strong></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <!-- Requirements Checklist -->
          <?php if (!empty($requirements)): ?>
          <div class="info-block">
            <h2>Required Documents</h2>
            <ul style="display:grid;gap:0.75rem;padding-left:1.25rem;color:var(--slate-700)">
              <?php foreach ($requirements as $req): ?>
                <li><?= e($req) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <!-- FAQs -->
          <?php if (!empty($faqs)): ?>
          <div class="info-block">
            <h2>Frequently Asked Questions</h2>
            <div style="display:grid;gap:1.25rem;margin-top:1rem">
              <?php foreach ($faqs as $faq): ?>
                <div style="border-bottom:1px solid var(--slate-200);padding-bottom:1rem">
                  <h3 style="font-size:1.1rem;color:var(--navy-900);margin:0 0 0.4rem"><?= e($faq['q']) ?></h3>
                  <p style="margin:0;color:var(--slate-600)"><?= e($faq['a']) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Sidebar / Booking Card -->
        <aside class="detail-sidebar">
          <div class="pricing-card" style="position:sticky;top:calc(var(--header-h) + 20px)">
            <div style="padding-bottom:1rem;border-bottom:1px solid var(--slate-100);margin-bottom:1.25rem">
              <?php $showServicePrices = areServicePricesVisible(); ?>
              <?php if ($showServicePrices && (float)$service['price_starting_aed'] > 0): ?>
                <span style="font-size:0.85rem;color:var(--slate-500);text-transform:uppercase;font-weight:700">Starting Investment</span>
                <div style="font-size:1.9rem;font-weight:800;color:var(--navy-900)">
                  AED <?= number_format((float)$service['price_starting_aed']) ?>
                </div>
              <?php else: ?>
                <span style="font-size:0.85rem;color:var(--slate-500);text-transform:uppercase;font-weight:700">Advisory &amp; Quotation</span>
                <div style="font-size:1.45rem;font-weight:800;color:var(--navy-900)">
                  Free Initial Consultation
                </div>
              <?php endif; ?>
              <span style="font-size:0.85rem;color:var(--slate-500)">Timeline: <strong><?= e($service['timeline']) ?></strong></span>
            </div>

            <p style="font-size:0.9rem;color:var(--slate-600);margin-bottom:1.5rem">
              Get an instant quotation and personalized advisory directly with our senior corporate setup consultants.
            </p>

            <a href="https://wa.me/<?= e(getWhatsAppNumber()) ?>?text=<?= urlencode('Hello Anchor Point, I would like to consult about ' . $service['title'] . '.') ?>"
               target="_blank"
               rel="noopener nofollow"
               class="btn btn-wa"
               style="width:100%;margin-bottom:0.75rem;padding:0.85rem">
              Chat with Corporate Desk
            </a>

            <a href="/contact" class="btn btn-outline" style="width:100%;padding:0.75rem">
              Submit Enquiry Form
            </a>

            <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--slate-100);font-size:0.82rem;color:var(--slate-500);display:grid;gap:0.4rem">
              <div>✓ 100% Confidential Consultation</div>
              <div>✓ Official Government Typing &amp; PRO</div>
              <div>✓ Fast Direct Ministry Processing</div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
