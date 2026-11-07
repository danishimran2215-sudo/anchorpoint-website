<?php
/**
 * Terms & Conditions — /terms (UAE Tourism Website)
 */
require_once __DIR__ . '/config.php';

$siteName     = getSetting('site_name', SITE_NAME);
$compEmail    = getCompanyEmail();
$phoneDisp    = getPrimaryPhone();
$pageTitle    = 'Terms & Conditions | ' . $siteName;
$pageDesc     = 'Booking terms, guest safety requirements, pricing in AED, and UAE governing law for ' . $siteName . '.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/terms';

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>Terms &amp; Conditions</span>
      </nav>
      <h1>Terms &amp; Conditions</h1>
      <p>Standard booking terms and operational conditions for experiences operated or ticketed by <?= e($siteName) ?>.</p>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <article class="prose-card">
        <h2>1. Bookings &amp; Confirmations</h2>
        <p>Submitting an online enquiry or WhatsApp message initiates a reservation request. Your booking is confirmed once our reservations team verifies slot availability, confirms your pick-up window or timed-entry slot, and issues your booking confirmation or QR e-ticket.</p>

        <h2>2. Pricing &amp; Currency</h2>
        <p>All rates published on our website are quoted in United Arab Emirates Dirhams (AED) unless otherwise stated. Seasonal peak dates (such as UAE National Day, Christmas, and New Year’s Eve) may carry official venue surcharges, which are always disclosed transparently prior to confirmation.</p>

        <h2>3. Pick-up Timings &amp; Punctuality</h2>
        <p>For shared safari and city tours, hotel pick-up windows span 30–45 minutes depending on traffic and hotel location. Guests must be ready in the hotel lobby at the start of the confirmed window. For cruises, watersports, and timed-entry landmarks, guests must arrive at the pier or venue 20–30 minutes prior to departure/slot time.</p>

        <h2>4. Health, Age, Weight &amp; Dress Requirements</h2>
        <ul>
          <li>Off-road dune bashing, quad biking, dune buggies, jet skiing, and speedboats are not suitable for pregnant guests, infants, or individuals with serious back, neck, or heart conditions.</li>
          <li>UAE Coastguard regulations require a valid original Passport or Emirates ID for yacht charters, fishing trips, jet skiing, and scuba diving.</li>
          <li>Respectful modest attire is mandatory when visiting mosques (including Sheikh Zayed Grand Mosque) and cultural heritage sites.</li>
        </ul>

        <h2>5. Governing Law</h2>
        <p>These Terms &amp; Conditions are governed by the federal laws of the United Arab Emirates and the local regulations of the Emirate of Dubai. For assistance, contact <a href="mailto:<?= e($compEmail) ?>"><?= e($compEmail) ?></a> or WhatsApp <strong><?= e($phoneDisp) ?></strong>.</p>
      </article>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
