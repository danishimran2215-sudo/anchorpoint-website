<?php
/**
 * Activity Detail Page — /<category>/<activity-name>
 * PDF Sections 3–8, Section 9 (Dynamic WhatsApp CTA), Section 10 (14-Field Form at Bottom)
 */
require_once __DIR__ . '/../config.php';

$catSlug  = sanitize($_GET['category_slug'] ?? $_GET['category'] ?? '');
$tourSlug = sanitize($_GET['slug'] ?? '');

$tour = null;
if ($catSlug !== '' && $tourSlug !== '') {
    $tour = getTourByCategoryAndSlug($catSlug, $tourSlug);
}

// Fallback: lookup by slug alone (NO REDIRECTS EVER to prevent loops)
if (!$tour && $tourSlug !== '') {
    $tour = getTourBySlug($tourSlug);
}

if (!$tour) {
    http_response_code(404);
    require ROOT_PATH . '/404.php';
    exit;
}

$showPrices         = arePricesVisible();
$activeCategorySlug = $tour['category_slug'];
$canonicalPath      = activityUrl($tour);
$canonicalUrl       = rtrim(SITE_URL, '/') . $canonicalPath;

$gallery    = json_decode($tour['gallery_json'] ?? '[]', true);
if (!is_array($gallery) || empty($gallery)) {
    $gallery = [$tour['featured_image']];
}
$inclusions = json_decode($tour['inclusions_json'] ?? '[]', true) ?: [];
$exclusions = json_decode($tour['exclusions_json'] ?? '[]', true) ?: [];
$details    = json_decode($tour['details_json'] ?? '{}', true) ?: [];
$faqs       = json_decode($tour['faq_json'] ?? '[]', true) ?: [];
$routeStops = !empty($details['route_stops']) && is_array($details['route_stops'])
    ? $details['route_stops']
    : (json_decode($tour['itinerary_json'] ?? '[]', true) ?: []);

$pageTitle = !empty($tour['seo_title']) ? $tour['seo_title'] : ($tour['title'] . ' | ' . $tour['category_name'] . ' — ' . SITE_NAME);
$pageDesc  = !empty($tour['seo_desc'])  ? $tour['seo_desc']  : ($tour['short_desc'] . ' — ' . ($tour['duration'] ?? ''));
$ogImage   = $tour['featured_image'] ?: ($gallery[0] ?? '');

$tripNode = [
    '@type'       => 'Product',
    'name'        => $tour['title'],
    'description' => $details['overview'] ?? $tour['long_desc'] ?? $tour['short_desc'],
    'image'       => array_map(fn($img) => rtrim(SITE_URL, '/') . '/' . ltrim(preg_replace('/\?.*$/', '', $img), '/'), array_slice($gallery, 0, 6)),
    'brand'       => ['@type' => 'Brand', 'name' => getSetting('site_name', SITE_NAME)],
    'aggregateRating' => [
        '@type'       => 'AggregateRating',
        'ratingValue' => (string)($tour['rating'] ?? '4.9'),
        'reviewCount' => (string)($tour['reviews_count'] ?? '115'),
    ],
];
if ($showPrices && (float)$tour['price_aed'] > 0) {
    $tripNode['offers'] = [
        '@type'         => 'Offer',
        'url'           => $canonicalUrl,
        'priceCurrency' => 'AED',
        'price'         => number_format((float)$tour['price_aed'], 2, '.', ''),
        'availability'  => 'https://schema.org/InStock',
    ];
}

$jsonLd = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => rtrim(SITE_URL, '/') . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $tour['category_name'], 'item' => rtrim(SITE_URL, '/') . '/' . $tour['category_slug']],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $tour['title'], 'item' => $canonicalUrl],
            ],
        ],
        $tripNode,
    ],
];

$tomorrowDate = date('Y-m-d', strtotime('+1 day'));
$todayDate    = date('Y-m-d');
$relatedTours = getRelatedTours((int)$tour['category_id'], (int)$tour['id'], 4);

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <a href="/<?= e($tour['category_slug']) ?>"><?= e($tour['category_name']) ?></a>
        <span aria-hidden="true">/</span>
        <span><?= e($tour['title']) ?></span>
      </nav>

      <h1><?= e($tour['title']) ?></h1>
      <p><?= e($tour['short_desc']) ?></p>

      <div class="detail-pills">
        <?php if (!empty($tour['duration'])): ?>
          <span class="detail-pill">⏱ Duration: <?= e($tour['duration']) ?></span>
        <?php endif; ?>
        <span class="detail-pill">
          <?= (int)($tour['has_pickup'] ?? 1) === 1 ? '🚐 Hotel Pick-up Available' : '📍 Direct Venue / Marina Access' ?>
        </span>
        <?php if (!empty($details['attraction_type'])): ?>
          <span class="detail-pill">🎟 <?= e($details['attraction_type']) ?></span>
        <?php endif; ?>
        <span class="detail-pill">★ <?= number_format((float)($tour['rating'] ?? 4.9), 1) ?> (<?= (int)($tour['reviews_count'] ?? 110) ?>+ verified guests)</span>
      </div>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <!-- 6–8 Photo Gallery + Thumbnail Strip -->
      <?php $gridPhotos = array_slice($gallery, 0, 5); ?>
      <div class="detail-gallery" aria-label="Activity Photo Gallery">
        <?php foreach ($gridPhotos as $gIdx => $gImg):
          $imgUrl = tourImageUrl($gImg);
          $extraCount = count($gallery) - 5;
        ?>
          <button type="button"
                  class="gallery-item"
                  data-lightbox-src="<?= e($imgUrl) ?>"
                  aria-label="Open photo <?= $gIdx + 1 ?> of <?= count($gallery) ?>">
            <img src="<?= e($imgUrl) ?>"
                 alt="<?= e($tour['title'] . ' — Photo ' . ($gIdx + 1)) ?>"
                 width="800"
                 height="600"
                 <?= $gIdx === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
            <?php if ($gIdx === 4 && $extraCount > 0): ?>
              <span class="gallery-more-badge">+<?= $extraCount ?> More Photos</span>
            <?php endif; ?>
          </button>
        <?php endforeach; ?>
      </div>

      <?php if (count($gallery) > 1): ?>
      <div class="detail-thumbs-strip" aria-label="All Gallery Photos">
        <?php foreach ($gallery as $tIdx => $tImg):
          $tUrl = tourImageUrl($tImg);
        ?>
          <button type="button"
                  class="detail-thumb-btn"
                  data-lightbox-src="<?= e($tUrl) ?>"
                  aria-label="View photo <?= $tIdx + 1 ?>">
            <img src="<?= e($tUrl) ?>"
                 alt="<?= e($tour['title'] . ' thumbnail ' . ($tIdx + 1)) ?>"
                 width="160"
                 height="120"
                 loading="lazy">
          </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="detail-columns">
        <!-- Left Main Details Column -->
        <div>
          <div class="info-block">
            <h2>Experience Overview</h2>
            <p style="margin-bottom:0;font-size:1.02rem;line-height:1.7">
              <?= nl2br(e($details['overview'] ?? $tour['long_desc'] ?? $tour['short_desc'])) ?>
            </p>
          </div>

          <?php if (!empty($inclusions) || !empty($exclusions)): ?>
          <div class="info-block">
            <h2>What’s Included &amp; Not Included</h2>
            <div class="inc-exc-grid">
              <?php if (!empty($inclusions)): ?>
              <div>
                <h3 style="font-size:1.02rem;color:var(--success);margin-bottom:0.65rem">Included</h3>
                <ul class="check-list">
                  <?php foreach ($inclusions as $inc): ?>
                    <li><span class="icon-ok" aria-hidden="true">✓</span><span><?= e($inc) ?></span></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php endif; ?>

              <?php if (!empty($exclusions)): ?>
              <div>
                <h3 style="font-size:1.02rem;color:var(--danger);margin-bottom:0.65rem">Not Included</h3>
                <ul class="cross-list">
                  <?php foreach ($exclusions as $exc): ?>
                    <li><span class="icon-no" aria-hidden="true">✕</span><span><?= e($exc) ?></span></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($routeStops)): ?>
          <div class="info-block">
            <h2>Route &amp; Key Stops</h2>
            <ul class="stops-list">
              <?php foreach ($routeStops as $stop): ?>
                <li><span><?= e(is_array($stop) ? ($stop['title'] ?? implode(' — ', $stop)) : (string)$stop) ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <?php if ($showPrices && !empty($details['ticket_types']) && is_array($details['ticket_types'])): ?>
          <div class="info-block">
            <h2>Ticket Options &amp; Official Rates</h2>
            <div style="overflow-x:auto">
              <table class="ticket-table">
                <thead>
                  <tr>
                    <th>Ticket Type</th>
                    <th>Adult Rate</th>
                    <th>Child Rate</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($details['ticket_types'] as $tt): ?>
                  <tr>
                    <td><strong><?= e($tt['name'] ?? '') ?></strong></td>
                    <td><?= e($tt['adult'] ?? '') ?></td>
                    <td><?= e($tt['child'] ?? '') ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endif; ?>

          <!-- Category-Specific Operational & Guest Details (PDF Pages 4–9) -->
          <?php
            $structuredSpecs = [
                'timings_pickup'      => 'Pick-up & Drop-off Timings',
                'boarding_point'      => 'Boarding Point & Arrival Time',
                'menu_highlights'     => 'Buffet Menu & Refreshments',
                'seating_options'     => 'Deck & Seating Options',
                'entertainment'       => 'Live Onboard Entertainment',
                'start_location'      => 'Start Location / Marina Point',
                'safety_requirements' => 'Age, Weight & Safety Requirements',
                'what_to_bring'       => 'What to Bring',
                'dress_code'          => 'Dress Code Guidelines',
                'opening_hours'       => 'Opening Hours',
                'date_time_note'      => 'Date & Time Slot Selection',
                'delivery_method'     => 'Ticket Delivery (WhatsApp & Email)',
                'package_discounts'   => 'Combo & Group Savings',
            ];
            $presentSpecs = [];
            foreach ($structuredSpecs as $sKey => $sLabel) {
                if (!empty($details[$sKey])) {
                    $presentSpecs[$sLabel] = $details[$sKey];
                }
            }
          ?>
          <?php if (!empty($presentSpecs)): ?>
          <div class="info-block">
            <h2>Essential Information</h2>
            <div style="display:grid;gap:1rem">
              <?php foreach ($presentSpecs as $sLabel => $sVal): ?>
                <div style="padding-bottom:0.85rem;border-bottom:1px solid var(--slate-100)">
                  <strong style="display:block;color:var(--navy-900);margin-bottom:0.2rem"><?= e($sLabel) ?></strong>
                  <span style="color:var(--slate-700);font-size:0.96rem"><?= e((string)$sVal) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($details['important_notes'])): ?>
          <div class="info-block">
            <h2>Important Health &amp; Safety Notes</h2>
            <div class="note-callout"><?= e($details['important_notes']) ?></div>
          </div>
          <?php endif; ?>

          <?php
            $policyLine = $details['cancellation'] ?? $details['weather_policy'] ?? $details['terms_rules'] ?? '';
            if ($policyLine !== ''):
          ?>
          <div class="info-block">
            <h2>Cancellation, Weather &amp; Ticket Policy</h2>
            <p style="margin-bottom:0.5rem"><?= e($policyLine) ?></p>
            <a href="/cancellation-policy" style="font-size:0.9rem;font-weight:700">Read full Cancellation &amp; Refund Policy &rarr;</a>
          </div>
          <?php endif; ?>

          <?php if (!empty($details['reviews']) && is_array($details['reviews'])): ?>
          <div class="info-block">
            <h2>Guest Reviews</h2>
            <div class="reviews-grid">
              <?php foreach ($details['reviews'] as $rev): ?>
                <div class="review-card">
                  <div class="review-stars" aria-label="5 out of 5 stars"><?= str_repeat('★', max(1, (int)($rev['rating'] ?? 5))) ?></div>
                  <p style="font-size:0.94rem;color:var(--slate-800);margin-bottom:0.55rem">“<?= e($rev['comment'] ?? '') ?>”</p>
                  <strong style="font-size:0.86rem;color:var(--slate-600)">— <?= e($rev['author'] ?? 'Verified Guest') ?></strong>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($faqs)): ?>
          <div class="info-block">
            <h2>Frequently Asked Questions</h2>
            <div style="display:grid;gap:0.9rem">
              <?php foreach ($faqs as $faq): ?>
                <div style="padding-bottom:0.8rem;border-bottom:1px solid var(--slate-100)">
                  <strong style="display:block;color:var(--navy-900);margin-bottom:0.25rem"><?= e($faq['q'] ?? '') ?></strong>
                  <p style="margin:0;color:var(--slate-600);font-size:0.95rem"><?= e($faq['a'] ?? '') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Right Sticky Booking Sidebar -->
        <aside class="booking-sidebar-card" aria-label="Instant Booking Box">
          <?php if ($showPrices && (float)$tour['price_aed'] > 0): ?>
            <div class="sidebar-price-row">
              <div>
                <div style="font-size:0.82rem;font-weight:700;text-transform:uppercase;color:var(--slate-500)">Adult Price</div>
                <div class="sidebar-price-main">AED <?= number_format((float)$tour['price_aed'], 0) ?></div>
                <div style="font-size:0.84rem;color:var(--slate-500)">per person</div>
              </div>
              <?php if ((float)($tour['child_price_aed'] ?? 0) > 0): ?>
              <div style="text-align:right">
                <div style="font-size:0.8rem;font-weight:700;color:var(--slate-500)">Child Rate</div>
                <div style="font-size:1.15rem;font-weight:800;color:var(--navy-900)">AED <?= number_format((float)$tour['child_price_aed'], 0) ?></div>
              </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div style="padding-bottom:0.9rem;margin-bottom:0.9rem;border-bottom:1px solid var(--slate-200)">
              <strong style="font-size:1.2rem;color:var(--navy-900)">Instant Availability Check</strong>
              <p style="margin:0.25rem 0 0;font-size:0.9rem;color:var(--slate-600)">Message us on WhatsApp or submit the enquiry form below for instant rates &amp; confirmation.</p>
            </div>
          <?php endif; ?>

          <?php if (!empty($tour['age_policy'])): ?>
            <p style="font-size:0.85rem;color:var(--slate-600);background:var(--slate-50);padding:0.6rem 0.75rem;border-radius:8px;margin-bottom:1rem">
              <strong>Age Policy:</strong> <?= e($tour['age_policy']) ?>
            </p>
          <?php endif; ?>

          <div class="sidebar-quick-fields">
            <div>
              <label for="quickBookingDate">Preferred Date</label>
              <input type="date"
                     id="quickBookingDate"
                     class="form-control"
                     min="<?= e($todayDate) ?>"
                     value="<?= e($tomorrowDate) ?>">
            </div>
            <div>
              <label for="quickBookingGuests">Number of Guests</label>
              <select id="quickBookingGuests" class="form-control">
                <?php for ($g = 1; $g <= 15; $g++): ?>
                  <option value="<?= $g ?>"<?= $g === 2 ? ' selected' : '' ?>><?= $g ?> <?= $g === 1 ? 'Guest' : 'Guests' ?></option>
                <?php endfor; ?>
                <option value="16+">16+ Guests (Group Rate)</option>
              </select>
            </div>
          </div>

          <div style="display:grid;gap:0.65rem">
            <a id="detailBookWhatsAppBtn"
               href="<?= e(waLinkDetail($tour['title'], $tomorrowDate, 2)) ?>"
               target="_blank"
               rel="noopener"
               class="btn btn-wa"
               style="width:100%"
               data-wa-location="activity_detail"
               data-wa-activity="<?= e($tour['title']) ?>"
               data-wa-num="<?= e(getWhatsAppNumber()) ?>">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
              Book on WhatsApp
            </a>
            <a href="#enquiryFormSection" class="btn btn-outline" style="width:100%">
              Book via Online Form
            </a>
          </div>

          <p style="margin:0.9rem 0 0;font-size:0.82rem;color:var(--slate-500);text-align:center">
            Direct UAE Booking Desk: <strong><?= e(getPrimaryPhone()) ?></strong>
          </p>
        </aside>
      </div>

      <!-- Full 14-Field Customer Information Form at Bottom of Every Activity Page (PDF Section 10) -->
      <div style="margin-top:3rem">
        <?php
          $selectedTour   = $tour;
          $formHeading    = 'Book ' . $tour['title'];
          $formSubheading = 'Complete the enquiry form below and our team will confirm your booking on WhatsApp within 30 minutes.';
          require INCLUDES_PATH . 'enquiry-form.php';
        ?>
      </div>

      <?php if (!empty($relatedTours)): ?>
      <div style="margin-top:3.5rem">
        <div class="section-header">
          <div class="section-header-text">
            <h2>More in <?= e($tour['category_name']) ?></h2>
          </div>
          <a href="/<?= e($tour['category_slug']) ?>" class="btn btn-outline">View all <?= e($tour['category_name']) ?> &rarr;</a>
        </div>
        <div class="activity-grid">
          <?php foreach ($relatedTours as $relTour):
            $tour = $relTour;
            require INCLUDES_PATH . 'activity-card.php';
          endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<!-- Fullscreen Gallery Lightbox Modal -->
<div id="galleryLightbox" class="lightbox-modal" role="dialog" aria-modal="true" aria-label="Photo Viewer">
  <button type="button" id="lightboxClose" class="lightbox-btn lightbox-close" aria-label="Close gallery">&times;</button>
  <button type="button" id="lightboxPrev" class="lightbox-btn lightbox-prev" aria-label="Previous photo">&#8249;</button>
  <img id="lightboxImage" class="lightbox-img" src="" alt="Expanded tour photo">
  <button type="button" id="lightboxNext" class="lightbox-btn lightbox-next" aria-label="Next photo">&#8250;</button>
</div>

<?php require INCLUDES_PATH . 'footer.php'; ?>
