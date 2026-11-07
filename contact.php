<?php
/**
 * About & Contact Page — /contact & /about (PDF Section 1 & Legal Requirements)
 * Office address, phone, WhatsApp, email, Google Map, and UAE Trade & Tourism licence numbers
 */
require_once __DIR__ . '/config.php';

$siteName   = getSetting('site_name', SITE_NAME);
$phoneDisp  = getPrimaryPhone();
$phoneRaw   = getPrimaryPhoneRaw();
$compEmail  = getCompanyEmail();
$addressStr = getSetting('office_address', OFFICE_ADDRESS);
$hoursStr   = getSetting('office_hours', OFFICE_HOURS);

$pageTitle    = 'About & Contact Us | ' . $siteName . ' Dubai, UAE';
$pageDesc     = 'Contact Anchor Point Travel & Tourism in Dubai, UAE. Call or WhatsApp ' . $phoneDisp . ', email ' . $compEmail . ', or visit our Dubai office.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/contact';

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>About &amp; Contact</span>
      </nav>
      <h1>About &amp; Contact <?= e($siteName) ?></h1>
      <p>Your trusted local specialist for Desert Safaris, UAE City Tours, Marina &amp; Creek Dinner Cruises, Watersports, and Official Attraction Tickets.</p>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <div class="contact-grid">
        <div class="info-block" style="margin-bottom:0">
          <h2>Reach Our Dubai Team</h2>
          <p style="color:var(--slate-600)">
            Whether you are planning a family holiday, a private desert dinner, a romantic yacht charter, or skip-the-line attraction tickets, our local Dubai desk is available 7 days a week.
          </p>

          <div style="display:grid;gap:1.1rem;margin:1.5rem 0">
            <div>
              <strong style="display:block;color:var(--navy-900)">Phone &amp; 24/7 WhatsApp Priority Line</strong>
              <a href="tel:<?= e($phoneRaw) ?>" style="font-size:1.15rem;font-weight:800"><?= e($phoneDisp) ?></a>
            </div>
            <div>
              <strong style="display:block;color:var(--navy-900)">Reservations Email</strong>
              <a href="mailto:<?= e($compEmail) ?>" style="font-weight:700"><?= e($compEmail) ?></a>
            </div>
            <div>
              <strong style="display:block;color:var(--navy-900)">Dubai Office Address</strong>
              <span style="color:var(--slate-700)"><?= e($addressStr) ?></span>
            </div>
            <div>
              <strong style="display:block;color:var(--navy-900)">Operating Hours</strong>
              <span style="color:var(--slate-700)"><?= e($hoursStr) ?></span>
            </div>
            <div style="background:var(--slate-50);border:1px solid var(--slate-200);border-radius:10px;padding:1rem">
              <strong style="display:block;color:var(--navy-900);margin-bottom:0.35rem">Customer Guarantee</strong>
              <div style="font-size:0.92rem;color:var(--slate-700)">
                Direct local Dubai concierge support, verified corporate PRO assistance, and instant WhatsApp response 7 days a week.
              </div>
            </div>
          </div>

          <div style="display:flex;flex-wrap:wrap;gap:0.75rem">
            <a href="<?= e(waLinkHeader()) ?>"
               target="_blank"
               rel="noopener"
               class="btn btn-wa"
               data-wa-location="contact_page">
              Chat on WhatsApp (<?= e($phoneDisp) ?>)
            </a>
            <a href="/book" class="btn btn-primary">Open Booking Form</a>
          </div>
        </div>

        <div class="info-block" style="margin-bottom:0;padding:0;overflow:hidden;min-height:420px">
          <iframe
            title="Anchor Point Travel & Tourism Office Map — Dubai, UAE"
            src="https://www.google.com/maps?q=Office%2008%20Sheikh%20Rashid%20Rd%20near%20Financial%20Centre%20Metro%20Al%20Jafiliya%20Dubai&output=embed"
            width="100%"
            height="100%"
            style="border:0;min-height:440px;display:block"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
