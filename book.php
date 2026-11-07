<?php
/**
 * Dedicated Booking / Enquiry Checkout Page — /book (PDF Section 1, 9, 10)
 */
require_once __DIR__ . '/config.php';

$pageTitle    = 'Book Your UAE Experience | Enquiry & Instant WhatsApp Checkout — ' . SITE_NAME;
$pageDesc     = 'Submit your UAE tour or attraction booking request online or chat directly on WhatsApp (+971 52 537 6838) for rapid confirmation within 30 minutes.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/book';

$preTour = null;
if (!empty($_GET['tour_id'])) {
    $db = getDB();
    $st = $db->prepare('SELECT t.*, c.name AS category_name, c.slug AS category_slug FROM tours t JOIN tour_categories c ON t.category_id = c.id WHERE t.id = ? LIMIT 1');
    $st->execute([(int)$_GET['tour_id']]);
    $preTour = $st->fetch() ?: null;
}

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>Book &amp; Enquire</span>
      </nav>
      <h1>Book Your UAE Tour or Attraction</h1>
      <p>Fill out our quick booking enquiry form below, or message our UAE booking desk directly on WhatsApp for immediate confirmation.</p>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <div class="detail-columns">
        <div>
          <?php
            $selectedTour   = $preTour;
            $formHeading    = 'Customer Information & Booking Form';
            $formSubheading = 'Complete the fields below. Our team receives your request instantly and replies within 30 minutes on WhatsApp.';
            require INCLUDES_PATH . 'enquiry-form.php';
          ?>
        </div>

        <aside class="booking-sidebar-card" aria-label="Direct WhatsApp Checkout">
          <h2 style="font-size:1.25rem;color:var(--navy-900);margin-bottom:0.5rem">Prefer Instant WhatsApp Booking?</h2>
          <p style="font-size:0.94rem;color:var(--slate-600);margin-bottom:1.15rem">
            Skip the wait and chat live with our local Dubai reservations desk for live slot checks, group discounts, and instant ticket delivery.
          </p>

          <a href="<?= e(waLinkCheckout($preTour['title'] ?? 'a UAE tour', 'Guest')) ?>"
             target="_blank"
             rel="noopener"
             class="btn btn-wa"
             style="width:100%;margin-bottom:1rem"
             data-wa-location="checkout_page"
             data-wa-activity="<?= e($preTour['title'] ?? 'General Booking') ?>">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
            Complete booking on WhatsApp
          </a>

          <ul class="check-list" style="font-size:0.9rem;color:var(--slate-700)">
            <li><span class="icon-ok">✓</span><span>Reply within 30 minutes on WhatsApp</span></li>
            <li><span class="icon-ok">✓</span><span>Trusted UAE Tour &amp; Corporate Partner</span></li>
            <li><span class="icon-ok">✓</span><span>Transparent AED pricing, no hidden charges</span></li>
            <li><span class="icon-ok">✓</span><span>24/7 WhatsApp helpline: <strong><?= e(getPrimaryPhone()) ?></strong></span></li>
          </ul>
        </aside>
      </div>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
