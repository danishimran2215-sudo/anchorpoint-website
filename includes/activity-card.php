<?php
/**
 * Reusable Activity Card Partial — PDF Section 3
 * Expects $tour (array). Respects global Show/Hide Prices setting.
 */
if (empty($tour)) return;
$cardUrl     = activityUrl($tour);
$cardImg     = tourImageUrl($tour['featured_image'] ?? '');
$cardTitle   = $tour['title'] ?? '';
$cardMeta    = !empty($tour['pickup_line']) ? $tour['pickup_line'] : ($tour['duration'] ?? '');
$cardDesc    = $tour['short_desc'] ?? '';
$cardBadge   = trim((string)($tour['badge'] ?? ''));
$pricesShown = isset($showPrices) ? (bool)$showPrices : arePricesVisible();
$ticketGroup = ticketGroupOf($tour);
?>
<article class="activity-card ticket-product-card" data-ticket-group="<?= e($ticketGroup) ?>">
  <a href="<?= e($cardUrl) ?>" class="activity-card-media" aria-label="<?= e($cardTitle) ?>">
    <img src="<?= e($cardImg) ?>"
         alt="<?= e($cardTitle . ' — ' . ($tour['category_name'] ?? 'UAE Experience')) ?>"
         width="800"
         height="600"
         loading="lazy"
         decoding="async"
         onerror="this.onerror=null;this.src='/assets/images/activity-placeholder.svg';">
    <?php if ($pricesShown && (float)($tour['price_aed'] ?? 0) > 0): ?>
      <span class="activity-card-pricetag">AED <?= number_format((float)$tour['price_aed'], 0) ?></span>
    <?php endif; ?>
    <?php if ($cardBadge !== ''): ?>
      <span class="activity-card-badge"><?= e($cardBadge) ?></span>
    <?php endif; ?>
  </a>

  <div class="activity-card-body">
    <?php if ($cardMeta !== ''): ?>
      <div class="activity-card-meta">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 15"/></svg>
        <span><?= e($cardMeta) ?></span>
      </div>
    <?php endif; ?>

    <h3 class="activity-card-title">
      <a href="<?= e($cardUrl) ?>"><?= e($cardTitle) ?></a>
    </h3>

    <p class="activity-card-desc"><?= e($cardDesc) ?></p>

    <?php if ($pricesShown && (float)($tour['price_aed'] ?? 0) > 0): ?>
      <div class="activity-card-price">
        From <strong>AED <?= number_format((float)$tour['price_aed'], 0) ?></strong> per person
      </div>
    <?php endif; ?>

    <div class="activity-card-actions">
      <a href="<?= e(waLinkCard($cardTitle)) ?>"
         target="_blank"
         rel="noopener"
         class="btn btn-wa"
         data-wa-location="activity_card"
         data-wa-activity="<?= e($cardTitle) ?>">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
        Book on WhatsApp
      </a>
      <a href="<?= e($cardUrl) ?>" class="btn btn-outline">View details</a>
    </div>
  </div>
</article>
