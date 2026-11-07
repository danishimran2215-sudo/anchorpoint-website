<?php
/**
 * Privacy Policy — /privacy (UAE Federal Decree-Law No. 45 of 2021 — PDPL Compliant)
 */
require_once __DIR__ . '/config.php';

$siteName     = getSetting('site_name', SITE_NAME);
$compEmail    = getCompanyEmail();
$phoneDisp    = getPrimaryPhone();
$addressStr   = getSetting('office_address', OFFICE_ADDRESS);
$pageTitle    = 'Privacy Policy (UAE PDPL) | ' . $siteName;
$pageDesc     = 'Privacy Policy for ' . $siteName . ' in accordance with UAE Federal Decree-Law No. 45 of 2021 Regarding the Protection of Personal Data.';
$canonicalUrl = rtrim(SITE_URL, '/') . '/privacy';

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="page-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span aria-hidden="true">/</span>
        <span>Privacy Policy</span>
      </nav>
      <h1>Privacy Policy</h1>
      <p>Compliant with UAE Federal Decree-Law No. 45 of 2021 on the Protection of Personal Data (PDPL).</p>
    </div>
  </section>

  <section class="detail-section">
    <div class="container">
      <article class="prose-card">
        <h2>1. Who We Are</h2>
        <p><?= e($siteName) ?> (“we”, “our”, or “us”) operates from <?= e($addressStr) ?> and provides guided tours, desert safaris, dinner cruises, watersports, and attraction tickets across the United Arab Emirates.</p>

        <h2>2. Personal Data We Collect</h2>
        <p>When you submit an enquiry form or contact us via WhatsApp, phone, or email, we collect only the personal data required to arrange and confirm your booking:</p>
        <ul>
          <li>Full name, email address, and phone / WhatsApp number (with country code).</li>
          <li>Selected tour or attraction, preferred date/time, guest counts (adults &amp; children ages), hotel pick-up location, nationality, and special dietary/accessibility requests.</li>
          <li>Technical attribution data such as source page URL, campaign UTM parameters, and IP address for fraud/spam prevention.</li>
        </ul>

        <h2>3. Lawful Basis &amp; Purpose of Processing</h2>
        <p>Under the UAE Personal Data Protection Law (Federal Decree-Law No. 45 of 2021), we process your data strictly with your explicit consent and to fulfill your requested booking—including sending booking confirmations, e-tickets, driver pick-up coordinates, and customer support messages via WhatsApp, phone, or email.</p>

        <h2>4. Data Sharing &amp; Retention</h2>
        <p>We never sell or rent your personal information. Minimal operational details (such as guest name, hotel room/pick-up point, or passport/ID details where required by UAE Coastguard or attraction venues) are shared solely with licensed transport captains, marinas, or official UAE venues executing your booking. Enquiry records are stored securely and retained only as long as necessary for booking fulfillment, customer service, and UAE accounting/regulatory compliance.</p>

        <h2>5. Cookies &amp; Analytics</h2>
        <p>Our website uses essential session cookies for security (CSRF protection) and optional analytics tags (Google Analytics 4 / Meta Pixel) to measure website performance and WhatsApp booking clicks.</p>

        <h2>6. Your Rights Under UAE PDPL</h2>
        <p>You may request access to, correction of, or deletion of your personal data, or withdraw your marketing/contact consent at any time by contacting us at <a href="mailto:<?= e($compEmail) ?>"><?= e($compEmail) ?></a> or via WhatsApp at <strong><?= e($phoneDisp) ?></strong>.</p>
      </article>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
