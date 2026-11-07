<?php
/**
 * Thank-You & Conversion Page — /thank-you (PDF Section 1, 9, 10)
 * Confirms submission, provides pre-filled WhatsApp confirmation buttons,
 * and fires GA4 + Meta Pixel conversion events.
 */
require_once __DIR__ . '/config.php';

$ref  = sanitize($_GET['ref'] ?? '');
$last = $_SESSION['last_booking'] ?? null;

if ($ref !== '' && (empty($last) || ($last['booking_ref'] ?? '') !== $ref)) {
    try {
        $db = getDB();
        $st = $db->prepare('SELECT * FROM tour_bookings WHERE booking_ref = ? LIMIT 1');
        $st->execute([$ref]);
        $row = $st->fetch();
        if ($row) {
            $last = [
                'booking_ref'    => $row['booking_ref'],
                'tour_title'     => $row['tour_title'],
                'category_name'  => $row['category_name'],
                'customer_name'  => $row['customer_name'],
                'booking_date'   => $row['booking_date'],
                'adults_count'   => (int)$row['adults_count'],
                'children_count' => (int)$row['children_count'],
                'total_price'    => (float)$row['total_price_aed'],
            ];
        }
    } catch (Exception $e) {}
}

$bookingRef   = $last['booking_ref'] ?? $ref;
$activityName = $last['tour_title'] ?? 'your selected UAE tour';
$customerName = $last['customer_name'] ?? 'Guest';
$bookingDate  = $last['booking_date'] ?? '';
$adultsCount  = (int)($last['adults_count'] ?? 2);
$childCount   = (int)($last['children_count'] ?? 0);
$totalValue   = (float)($last['total_price'] ?? 0);

$pageTitle    = 'Thank You — Enquiry Received | ' . SITE_NAME;
$pageDesc     = 'We received your booking enquiry and will reply within 30 minutes on WhatsApp.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/thank-you';
$noIndex      = true;

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="detail-section" style="padding:4rem 0">
    <div class="container">
      <div class="prose-card" style="max-width:680px;text-align:center">
        <div style="width:68px;height:68px;border-radius:50%;background:#DCFCE7;color:#15803D;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;margin-bottom:1.15rem" aria-hidden="true">✓</div>

        <h1 style="font-size:clamp(1.7rem,3vw,2.25rem);color:var(--navy-900);margin-bottom:0.6rem">
          Thank You, <?= e($customerName) ?>!
        </h1>
        <p style="font-size:1.1rem;font-weight:600;color:var(--slate-800);margin-bottom:1.25rem">
          We received your request and will reply within 30 minutes on WhatsApp.
        </p>

        <?php if ($bookingRef !== '' || $bookingDate !== ''): ?>
        <div style="background:var(--slate-50);border:1px solid var(--slate-200);border-radius:12px;padding:1.15rem 1.35rem;text-align:left;margin-bottom:1.65rem;font-size:0.95rem">
          <?php if ($bookingRef !== ''): ?>
            <div style="margin-bottom:0.35rem"><strong>Reference:</strong> <?= e($bookingRef) ?></div>
          <?php endif; ?>
          <div style="margin-bottom:0.35rem"><strong>Experience:</strong> <?= e($activityName) ?></div>
          <?php if ($bookingDate !== ''): ?>
            <div style="margin-bottom:0.35rem"><strong>Preferred Date:</strong> <?= e($bookingDate) ?></div>
          <?php endif; ?>
          <div><strong>Guests:</strong> <?= $adultsCount ?> Adult<?= $adultsCount > 1 ? 's' : '' ?><?= $childCount > 0 ? (', ' . $childCount . ' Child' . ($childCount > 1 ? 'ren' : '')) : '' ?></div>
        </div>
        <?php endif; ?>

        <p style="color:var(--slate-600);font-size:0.96rem;margin-bottom:1.25rem">
          Want to finalize your pick-up time or receive your tickets immediately? Tap below to open WhatsApp with your details pre-filled:
        </p>

        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:0.85rem;margin-bottom:1.5rem">
          <a href="<?= e(waLinkCheckout($activityName, $customerName)) ?>"
             target="_blank"
             rel="noopener"
             class="btn btn-wa"
             data-wa-location="checkout_complete"
             data-wa-activity="<?= e($activityName) ?>">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
            Complete booking on WhatsApp
          </a>

          <a href="<?= e(waLinkThankYouConfirm()) ?>"
             target="_blank"
             rel="noopener"
             class="btn btn-outline"
             data-wa-location="thank_you_confirm"
             data-wa-activity="<?= e($activityName) ?>">
            Chat with us now
          </a>
        </div>

        <a href="/" style="font-size:0.93rem;font-weight:700">&larr; Back to All UAE Experiences</a>
      </div>
    </div>
  </section>
</main>

<!-- Conversion Events for GA4 & Meta Pixel (PDF Section 10 #5) -->
<script>
  window.addEventListener('DOMContentLoaded', function () {
    var convPayload = {
      currency: 'AED',
      value: <?= json_encode(round($totalValue, 2)) ?>,
      item_name: <?= json_encode($activityName) ?>,
      transaction_id: <?= json_encode($bookingRef) ?>
    };
    if (typeof window.gtag === 'function') {
      window.gtag('event', 'generate_lead', convPayload);
    }
    if (typeof window.fbq === 'function') {
      window.fbq('track', 'Lead', {
        content_name: <?= json_encode($activityName) ?>,
        currency: 'AED',
        value: <?= json_encode(round($totalValue, 2)) ?>
      });
    }
  });
</script>

<?php require INCLUDES_PATH . 'footer.php'; ?>
