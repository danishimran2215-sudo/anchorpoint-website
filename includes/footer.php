<?php
/**
 * Site Footer Partial — Anchor Point Business Setup & Travel
 * Clean, modern, professional 4-column luxury layout.
 * Office Address, Phone & WhatsApp, Service Links.
 */
require_once __DIR__ . '/../config.php';

$siteName   = getSetting('site_name', SITE_NAME);
$phoneDisp  = getPrimaryPhone();
$phoneRaw   = getPrimaryPhoneRaw();
$compEmail  = getCompanyEmail();
$addressStr = getSetting('office_address', 'Office #08, Sheikh Rashid Rd, near Financial Centre Metro, Al Jafiliya, Dubai, UAE');
$hoursStr   = getSetting('office_hours', 'Daily: 9:00 AM – 6:00 PM (GST)');
$igUrl      = trim(getSetting('instagram_url', ''));
$fbUrl      = trim(getSetting('facebook_url', ''));
$ttUrl      = trim(getSetting('tiktok_url', ''));
?>
<footer class="site-footer" role="contentinfo">
  <div class="container footer-container">
    <div class="footer-main-grid">
      
      <!-- Col 1: Brand & UAE Licences -->
      <div class="footer-col footer-col-brand">
        <a href="/" class="footer-brand-logo" aria-label="<?= e($siteName) ?> Home">
          <img src="/assets/images/logo-white.svg?v=7" alt="<?= e($siteName) ?>" width="210" height="44" loading="lazy">
        </a>
        <p class="footer-brand-text">
          Premier corporate services and travel partner in Dubai, UAE. We curate signature Dubai desert safaris, dhow &amp; yacht dinner cruises, city sightseeing, official attraction tickets, and end-to-end business setup across Mainland &amp; Free Zones.
        </p>
      </div>

      <!-- Col 2: Tourism Experiences & Attraction Tickets -->
      <div class="footer-col">
        <h3 class="footer-col-title">Tourism Experiences</h3>
        <ul class="footer-nav-list">
          <li><a href="/desert-safari">Desert Safari Adventures</a></li>
          <li><a href="/city-tours">Dubai &amp; Abu Dhabi City Tours</a></li>
          <li><a href="/cruise-dinner">Marina &amp; Creek Dinner Cruises</a></li>
          <li><a href="/watersports">Watersports &amp; Sea Adventures</a></li>
          <li><a href="/tickets-attractions">Attraction Tickets (36+ Sites)</a></li>
          <li><a href="/book">Send Custom Booking Request</a></li>
        </ul>
      </div>

      <!-- Col 3: Business Setup & Corporate Services -->
      <div class="footer-col">
        <h3 class="footer-col-title">Business Setup &amp; PRO</h3>
        <ul class="footer-nav-list">
          <li><a href="/services/new-trade-license">Mainland Company Setup</a></li>
          <li><a href="/services/freezone-company-setup">Free Zone Business Formation</a></li>
          <li><a href="/services/golden-visa-services">UAE Golden Visa (10-Year)</a></li>
          <li><a href="/services/investor-partner-visa">Investor &amp; Partner Visas</a></li>
          <li><a href="/services/corporate-bank-account">Corporate Bank Account Opening</a></li>
          <li><a href="/services/pro-services">Corporate PRO &amp; Document Clearing</a></li>
          <li><a href="/services/vat-tax-registration">VAT &amp; Corporate Tax Registration</a></li>
          <li><a href="/services"><strong>View All 31 Business Services &rarr;</strong></a></li>
          <li><a href="/blog">UAE Business &amp; Travel Blog</a></li>
        </ul>
      </div>

      <!-- Col 4: 24/7 Booking & Contact Desk -->
      <div class="footer-col footer-col-contact">
        <h3 class="footer-col-title">24/7 Booking &amp; Support</h3>
        <div class="footer-contact-items">
          <div class="footer-contact-item">
            <span class="contact-label">Direct Phone:</span>
            <a href="tel:<?= e($phoneRaw) ?>" class="contact-value-phone"><?= e($phoneDisp) ?></a>
          </div>

          <div class="footer-contact-item">
            <span class="contact-label">WhatsApp (Instant Desk):</span>
            <a href="<?= e(waLinkFooter()) ?>" target="_blank" rel="noopener nofollow" class="contact-value-wa" data-wa-location="footer">
              <?= e($phoneDisp) ?> <span>(Chat Now)</span>
            </a>
          </div>

          <div class="footer-contact-item">
            <span class="contact-label">Email Inquiries:</span>
            <a href="mailto:<?= e($compEmail) ?>" class="contact-value-email"><?= e($compEmail) ?></a>
          </div>

          <div class="footer-contact-item">
            <span class="contact-label">Dubai Office Address:</span>
            <address class="contact-address"><?= e($addressStr) ?></address>
          </div>

          <div class="footer-contact-item">
            <span class="contact-label">Office Hours:</span>
            <span class="contact-hours"><?= e($hoursStr) ?></span>
          </div>
        </div>

        <?php if ($igUrl || $fbUrl || $ttUrl): ?>
          <div class="footer-social-links">
            <?php if ($igUrl): ?><a href="<?= e($igUrl) ?>" target="_blank" rel="noopener nofollow" aria-label="Instagram">Instagram</a><?php endif; ?>
            <?php if ($fbUrl): ?><a href="<?= e($fbUrl) ?>" target="_blank" rel="noopener nofollow" aria-label="Facebook">Facebook</a><?php endif; ?>
            <?php if ($ttUrl): ?><a href="<?= e($ttUrl) ?>" target="_blank" rel="noopener nofollow" aria-label="TikTok">TikTok</a><?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Bottom Copyright & Legal Links -->
    <div class="footer-bottom-bar">
      <div class="footer-copyright">
        &copy; <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved. Registered in Dubai, UAE.
      </div>
      <div class="footer-legal-links">
        <a href="/terms">Terms &amp; Conditions</a>
        <span class="sep">&bull;</span>
        <a href="/privacy">Privacy Policy (UAE PDPL)</a>
        <span class="sep">&bull;</span>
        <a href="/cancellation-policy">Cancellation &amp; Refund</a>
        <span class="sep">&bull;</span>
        <a href="/sitemap.xml">XML Sitemap</a>
      </div>
    </div>
  </div>
</footer>
<?php require __DIR__ . '/floating-buttons.php'; ?>
<script src="/assets/js/nav-interactions.js?v=4" defer></script>
</body>
</html>
