<?php
/**
 * Homepage — Anchor Point Business Setup & Travel
 */
require_once __DIR__ . '/config.php';

$categories        = getTourCategories();
$showPrices        = arePricesVisible();
$showServicePrices = areServicePricesVisible();
$featuredServices  = getFeaturedServices(8);
$recentBlogPosts   = getRecentBlogPosts(3);

$activePage   = 'home';
$pageTitle    = 'Anchor Point | Dubai Tours & Business Setup';
$pageDesc     = 'Book top Dubai desert safaris, city tours, dhow cruises & attraction tickets, plus UAE company setup and visas with instant WhatsApp support.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/';
$ogImage      = '/assets/images/dubai-hero.jpg';

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <!-- Luxury Dubai Hero Section with Skyline Background -->
  <section class="hero-short">
    <div class="container">
      <div class="hero-badge">
        <span class="badge-dot"></span>
        Dubai's Trusted Tourism &amp; Corporate Services Partner
      </div>
      <h1>Dubai Tours, Desert Safaris &amp; <span class="text-highlight">Business Setup Services</span></h1>
      <p>Book premium Dubai tours, desert safaris, luxury dinner cruises, official attraction tickets, and complete Dubai business setup services with instant WhatsApp assistance.</p>
      
      <div class="hero-cta-group">
        <a href="<?= e(waLinkHero()) ?>"
           target="_blank"
           rel="noopener nofollow"
           class="btn btn-wa"
           data-wa-location="hero">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
          Chat on WhatsApp
        </a>
        <a href="#desert-safari" class="btn btn-hero-explore">
          Explore Tours &darr;
        </a>
        <a href="#business-setup" class="btn btn-outline" style="background:rgba(255,255,255,0.12);color:#fff;border-color:rgba(255,255,255,0.35)">
          Business Setup Services &rarr;
        </a>
      </div>

      <div class="hero-trust-bar">
        <div class="trust-item">
          <span class="trust-icon">🚐</span>
          <span>Hotel Pick-up Included</span>
        </div>
        <div class="trust-item">
          <span class="trust-icon">⚡</span>
          <span>Instant WhatsApp Confirmation</span>
        </div>
        <div class="trust-item">
          <span class="trust-icon">🛡️</span>
          <span>Best Price Guarantee</span>
        </div>
        <div class="trust-item">
          <span class="trust-icon">🏢</span>
          <span>Dedicated 24/7 Concierge</span>
        </div>
        <div class="trust-item">
          <span class="trust-icon">★</span>
          <span>4.9/5 Rating (1,200+ Reviews)</span>
        </div>
      </div>
    </div>
  </section>

  <!-- 5 Tour Category Sections -->
  <?php foreach ($categories as $cat):
    $catTours = getTours([
        'category_id' => (int)$cat['id'],
        'limit'       => 4,
    ]);
    $secTitle = !empty($cat['section_title']) ? $cat['section_title'] : $cat['name'];
    $catUrl   = '/' . $cat['slug'];
  ?>
  <section id="<?= e($cat['slug']) ?>" class="category-section">
    <div class="container">
      <div class="section-header">
        <div class="section-header-text">
          <h2><?= e($secTitle) ?></h2>
          <p><?= e($cat['description']) ?></p>
        </div>
        <a href="<?= e($catUrl) ?>" class="btn btn-outline">View all <?= e($cat['name']) ?> &rarr;</a>
      </div>

      <div class="activity-grid">
        <?php foreach ($catTours as $tour): ?>
          <?php require INCLUDES_PATH . 'activity-card.php'; ?>
        <?php endforeach; ?>
      </div>

      <div class="section-footer-action">
        <a href="<?= e($catUrl) ?>" class="btn btn-primary">View all <?= e($cat['name']) ?></a>
      </div>
    </div>
  </section>
  <?php endforeach; ?>

  <!-- Business Setup & Corporate Services Section -->
  <section id="business-setup" class="services-section">
    <div class="container">
      <div class="section-header">
        <div class="section-header-text">
          <span style="display:inline-block;font-size:0.82rem;font-weight:800;color:var(--amber-500);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.35rem">Corporate &amp; Legal Services</span>
          <h2>Dubai Business Setup &amp; Company Formation</h2>
          <p>Expert assistance for Mainland &amp; Free Zone Trade Licenses, UAE Golden Visas, Corporate Bank Accounts, and Government PRO clearings.</p>
        </div>
        <a href="/services" class="btn btn-outline">Explore All Services &rarr;</a>
      </div>

      <div class="service-grid">
        <?php foreach ($featuredServices as $serv): ?>
          <article class="service-card">
            <div class="service-card-media">
              <img src="<?= e(serviceImageUrl($serv['featured_image'], $serv['category_slug'] ?? '')) ?>" alt="<?= e($serv['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='/uploads/services/new-trade-license.jpg';">
            </div>
            <div class="service-card-body">
              <?php if (!empty($serv['badge'])): ?>
                <span class="service-badge"><?= e($serv['badge']) ?></span>
              <?php endif; ?>
              <h3 class="service-title">
                <a href="/services/<?= e($serv['slug']) ?>" style="color:inherit;text-decoration:none"><?= e($serv['title']) ?></a>
              </h3>
              <p class="service-desc"><?= e($serv['short_desc']) ?></p>
              
              <div class="service-meta">
                <?php if ($showServicePrices && (float)$serv['price_starting_aed'] > 0): ?>
                  <span class="service-price">From AED <?= number_format((float)$serv['price_starting_aed']) ?></span>
                <?php else: ?>
                  <span class="service-price" style="color:#0F172A;font-weight:700">Free Consultation</span>
                <?php endif; ?>
                <span class="service-timeline"><?= e($serv['timeline']) ?></span>
              </div>

              <div class="service-actions">
                <a href="https://wa.me/<?= e(getWhatsAppNumber()) ?>?text=<?= urlencode('Hello Anchor Point, I would like consultation regarding ' . $serv['title'] . '.') ?>"
                   target="_blank"
                   rel="noopener nofollow"
                   class="btn btn-wa"
                   style="font-size:0.84rem;padding:0.55rem">
                  WhatsApp Us
                </a>
                <a href="/services/<?= e($serv['slug']) ?>" class="btn btn-outline" style="font-size:0.84rem;padding:0.55rem">
                  View Details
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="section-footer-action" style="margin-top:2.5rem">
        <a href="/services" class="btn btn-primary">View All Business Setup Services &rarr;</a>
      </div>
    </div>
  </section>

  <!-- Latest UAE Travel & Business Blog Section -->
  <?php if (!empty($recentBlogPosts)): ?>
  <section id="blog" class="blog-section">
    <div class="container">
      <div class="section-header">
        <div class="section-header-text">
          <span style="display:inline-block;font-size:0.82rem;font-weight:800;color:var(--blue-600);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.35rem">Knowledge Base &amp; Travel Guides</span>
          <h2>Latest Insights &amp; Dubai Guides</h2>
          <p>Expert articles on company setup, 10-year Golden Visas, desert safaris, and luxury dinner cruises.</p>
        </div>
        <a href="/blog" class="btn btn-outline">Read All Articles &rarr;</a>
      </div>

      <div class="blog-grid">
        <?php foreach ($recentBlogPosts as $post): ?>
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
              <h3 class="blog-card-title">
                <a href="/blog/<?= e($post['slug']) ?>" style="color:inherit;text-decoration:none"><?= e($post['title']) ?></a>
              </h3>
              <p class="blog-card-excerpt"><?= e(truncate($post['excerpt'], 130)) ?></p>
              <div>
                <a href="/blog/<?= e($post['slug']) ?>" style="color:var(--blue-600);font-weight:700;text-decoration:none;font-size:0.9rem">
                  Read Full Guide &rarr;
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
