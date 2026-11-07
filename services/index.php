<?php
/**
 * Business Setup & Corporate Services Hub — /services & /business-setup
 * Organized category-wise into 5 official categories and all 31 sub-services.
 * Full metadata, JSON-LD Schema.org, quick jump tabs, and instant WhatsApp booking.
 */
require_once __DIR__ . '/../config.php';

$pageTitle    = 'Dubai Business Setup & Company Formation Services | Anchor Point';
$pageDesc     = 'Start your business in Dubai Mainland & Free Zones with 100% foreign ownership. Fast trade licenses, Golden Visas, corporate bank accounts & PRO services.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/services';
$ogImage      = '/uploads/services/new-trade-license.jpg';

$groupedCategories = getServicesGrouped();
$showServicePrices = areServicePricesVisible();
$totalServices = 0;
foreach ($groupedCategories as $c) {
    $totalServices += count($c['services'] ?? []);
}

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <!-- Page Hero -->
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>Business Setup Services</span>
      </nav>
      <h1>Dubai Business Setup &amp; Company Formation</h1>
      <p>Empowering international entrepreneurs, corporate investors, and SMEs to incorporate, license, and thrive across Dubai Mainland and UAE Free Zones with 100% foreign ownership and zero red tape.</p>
    </div>
  </section>

  <section class="detail-section" style="padding:2.5rem 0 5rem;background:#F8FAFC">
    <div class="container">

      <!-- Advisory Alert Banner -->
      <div style="background:#0B2545;color:#FFFFFF;border-radius:18px;padding:2.25rem 2.5rem;margin-bottom:3rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem;box-shadow:0 12px 30px rgba(11,37,69,0.15)">
        <div style="max-width:700px">
          <span style="display:inline-block;font-size:0.8rem;font-weight:800;color:#FBBF24;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.45rem">Official Corporate Advisory</span>
          <h2 style="font-size:1.65rem;margin:0 0 0.6rem;color:#FFFFFF">Need expert advice on Mainland vs Free Zone?</h2>
          <p style="margin:0;color:rgba(255,255,255,0.85);font-size:0.96rem;line-height:1.6">Speak with our senior Dubai company formation specialists. We analyze your commercial business activities, visa requirements, office needs, and banking objectives to deliver the most cost-effective solution.</p>
        </div>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
          <a href="<?= e(waLinkHeader()) ?>" target="_blank" rel="noopener nofollow" class="btn btn-wa" style="padding:0.75rem 1.4rem">
            Chat on WhatsApp
          </a>
          <a href="/contact" class="btn btn-outline-light" style="padding:0.75rem 1.4rem;background:rgba(255,255,255,0.12);border:1.5px solid rgba(255,255,255,0.7);color:#FFFFFF;font-weight:700">
            Book Consultation
          </a>
        </div>
      </div>

      <!-- Quick Category Jump Navigation -->
      <div style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:3.5rem;box-shadow:0 4px 16px rgba(0,0,0,0.04)">
        <div style="font-size:0.84rem;font-weight:800;color:#64748B;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.85rem">
          Jump to Service Category (<?= $totalServices ?> Total Services)
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:0.6rem">
          <?php foreach ($groupedCategories as $idx => $cat): 
            $count = count($cat['services'] ?? []);
          ?>
            <a href="#<?= e($cat['slug']) ?>" class="btn btn-outline" style="min-height:38px;padding:0.45rem 1rem;font-size:0.86rem;border-radius:9999px;background:#F8FAFC;border-color:#CBD5E1;color:#1E293B">
              <strong><?= ($idx + 1) ?>. <?= e($cat['name']) ?></strong> <span style="color:#64748B;font-size:0.78rem;margin-left:4px">(<?= $count ?>)</span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Category-Wise Services Sections -->
      <?php foreach ($groupedCategories as $idx => $cat): 
        $catServices = $cat['services'] ?? [];
        if (empty($catServices)) continue;
      ?>
        <section id="<?= e($cat['slug']) ?>" style="margin-bottom:4.5rem;scroll-margin-top:100px">
          <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem;padding-bottom:1rem;border-bottom:2px solid #E2E8F0">
            <div>
              <span style="font-size:0.82rem;font-weight:800;color:#2563EB;text-transform:uppercase;letter-spacing:0.05em">Category <?= ($idx + 1) ?> of 5</span>
              <h2 style="font-size:1.75rem;font-weight:850;color:#0F172A;margin:0.25rem 0 0.4rem"><?= e($cat['name']) ?></h2>
              <p style="color:#64748B;font-size:0.96rem;margin:0;max-width:800px;line-height:1.55"><?= e($cat['description']) ?></p>
            </div>
            <a href="https://wa.me/<?= e(getWhatsAppNumber()) ?>?text=<?= urlencode('Hello Anchor Point, I need assistance with ' . $cat['name'] . '.') ?>"
               target="_blank"
               rel="noopener nofollow"
               class="btn btn-wa"
               style="font-size:0.86rem;padding:0.5rem 1rem">
              Enquire About <?= e($cat['name']) ?>
            </a>
          </div>

          <div class="service-grid" style="margin-top:0">
            <?php foreach ($catServices as $serv): 
              $servImg = serviceImageUrl($serv['featured_image'] ?? '', $cat['slug']);
              $servPrice = (float)($serv['price_starting_aed'] ?? 0);
              $waMsg = 'Hello Anchor Point, I would like consultation and pricing for ' . $serv['title'] . '.';
            ?>
              <article class="service-card">
                <div class="service-card-media">
                  <img src="<?= e($servImg) ?>" alt="<?= e($serv['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='/uploads/services/new-trade-license.jpg';">
                </div>
                <div class="service-card-body">
                  <?php if (!empty($serv['badge'])): ?>
                    <span class="service-badge"><?= e($serv['badge']) ?></span>
                  <?php endif; ?>
                  
                  <h3 class="service-title">
                    <a href="/services/<?= e($serv['slug']) ?>"><?= e($serv['title']) ?></a>
                  </h3>
                  
                  <p class="service-desc"><?= e($serv['short_desc']) ?></p>

                  <div class="service-meta">
                    <?php if ($showServicePrices && $servPrice > 0): ?>
                      <span class="service-price">
                        From AED <?= number_format($servPrice, 0) ?>
                      </span>
                    <?php else: ?>
                      <span class="service-price" style="color:#0F172A;font-weight:700">
                        Free Consultation
                      </span>
                    <?php endif; ?>
                    <span class="service-timeline"><?= e($serv['timeline'] ?? '1–3 business days') ?></span>
                  </div>

                  <div class="service-actions">
                    <a href="https://wa.me/<?= e(getWhatsAppNumber()) ?>?text=<?= urlencode($waMsg) ?>"
                       target="_blank"
                       rel="noopener nofollow"
                       class="btn btn-wa"
                       data-wa-location="service_card">
                      WhatsApp Us
                    </a>
                    <a href="/services/<?= e($serv['slug']) ?>" class="btn btn-outline">
                      View Details
                    </a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <!-- Bottom Advisory Block -->
      <div style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:18px;padding:2.5rem;text-align:center;max-width:850px;margin:2rem auto 0;box-shadow:0 8px 24px rgba(0,0,0,0.05)">
        <h3 style="font-size:1.5rem;color:#0F172A;margin:0 0 0.75rem">Can't Find the Exact Service You Need?</h3>
        <p style="color:#64748B;font-size:0.96rem;line-height:1.6;margin:0 auto 1.5rem;max-width:640px">
          Our government relations team handles over 100+ bespoke PRO tasks, court notarizations, custom industrial permits, and specialised licensing cases. Message our Dubai desk on WhatsApp for immediate guidance.
        </p>
        <div style="display:flex;justify-content:center;gap:0.85rem;flex-wrap:wrap">
          <a href="<?= e(waLinkHeader()) ?>" target="_blank" rel="noopener nofollow" class="btn btn-wa" style="padding:0.75rem 1.6rem">
            Chat with a PRO Specialist on WhatsApp
          </a>
          <a href="/contact" class="btn btn-primary" style="padding:0.75rem 1.6rem">
            Visit Our Al Jafiliya Office
          </a>
        </div>
      </div>

    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
