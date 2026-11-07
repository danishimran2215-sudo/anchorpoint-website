<?php
/**
 * Cancellation & Refund Policy — /cancellation-policy (PDF Legal Requirements)
 * Refund rules, weather reschedule rules, attraction ticket rules, and no-show policy
 */
require_once __DIR__ . '/config.php';

$siteName     = getSetting('site_name', SITE_NAME);
$compEmail    = getCompanyEmail();
$phoneDisp    = getPrimaryPhone();
$pageTitle    = 'Cancellation & Refund Policy | ' . $siteName;
$pageDesc     = 'Clear refund, free rescheduling, sea/weather cancellation, and no-show policies for UAE tours, cruises, watersports, and attraction tickets.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/cancellation-policy';

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>Cancellation Policy</span>
      </nav>
      <h1>Cancellation &amp; Refund Policy</h1>
      <p>Clear, fair rules covering tour cancellations, weather rescheduling, attraction e-tickets, and no-shows.</p>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <article class="prose-card">
        <h2>1. Standard Desert Safaris, City Tours &amp; Dinner Cruises</h2>
        <ul>
          <li><strong>24+ hours before experience:</strong> 100% free cancellation or complimentary date rescheduling.</li>
          <li><strong>Less than 24 hours before experience:</strong> Cancellations within 24 hours of pick-up/boarding incur a 100% charge as vehicle seats, camp catering, and cruise tables are pre-allocated.</li>
          <li><strong>Private Yachts, Luxury VIP Safaris &amp; Hot Air Balloons:</strong> Free cancellation up to 48 hours prior to departure.</li>
        </ul>

        <h2>2. Weather &amp; UAE Coastguard Conditions</h2>
        <p>Guest safety always comes first. If an outdoor tour, hot air balloon flight, watersport session, or boat/yacht departure is suspended due to wind, fog, rough sea conditions, or UAE Coastguard / Civil Aviation directives, you are entitled to either:</p>
        <ul>
          <li><strong>Priority free rescheduling</strong> to the next available date/slot, OR</li>
          <li><strong>A 100% full refund</strong> of any amount paid for the affected activity.</li>
        </ul>

        <h2>3. Official Attraction &amp; Theme Park Tickets</h2>
        <p>Dated or timed-entry barcode tickets (including Burj Khalifa, Museum of the Future, Dubai Frame, Dubai Aquarium, Aquaventure, Ferrari World, Warner Bros. World, IMG Worlds, and Louvre Abu Dhabi) are <strong>non-refundable and non-changeable once the official QR ticket has been issued</strong>, in accordance with venue operator rules.</p>

        <h2>4. Late Arrivals &amp; No-Shows</h2>
        <p>Guests who miss the scheduled shared hotel pick-up window, arrive after cruise/boat departure, or miss a timed-entry landmark slot are classified as a no-show and are not eligible for a refund. Whenever possible, our WhatsApp desk (<strong><?= e($phoneDisp) ?></strong>) will assist you in arranging direct transport to meet the tour or rebooking subject to venue discretion.</p>

        <h2>5. How to Request a Change or Cancellation</h2>
        <p>Send your booking reference or full name via WhatsApp to <a href="<?= e(waLinkHeader()) ?>" target="_blank" rel="noopener"><strong><?= e($phoneDisp) ?></strong></a> or email <a href="mailto:<?= e($compEmail) ?>"><?= e($compEmail) ?></a>.</p>
      </article>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
