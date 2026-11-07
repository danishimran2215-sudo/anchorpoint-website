<?php
/**
 * Fixed Top Header Partial — Anchor Point Business Setup & Travel
 * Solid Navy (#0B2545), 5 Direct Tour Links, Dropdown ONLY for Business Setup (5 Categories), Visible [WhatsApp Us]
 */
require_once __DIR__ . '/../config.php';

$activeCatSlug = $activeCategorySlug ?? '';
$isHomeHeader  = ($activePage ?? '') === 'home';
$siteName      = getSetting('site_name', SITE_NAME);

// Grouped Business Setup categories for the dropdown menu
$businessCategories = [
    [
        'slug'  => 'company-formation',
        'name'  => 'Business Setup & Licensing',
        'icon'  => 'briefcase',
        'desc'  => 'Mainland, Free Zone, Trade Licenses & Amendments',
        'links' => [
            ['title' => 'Mainland Company Setup',   'url' => '/services/new-trade-license'],
            ['title' => 'Free Zone Business Setup', 'url' => '/services/freezone-company-setup'],
            ['title' => 'Trade License Renewal',   'url' => '/services/renewal-trade-license'],
            ['title' => 'License Amendments',      'url' => '/services/company-file-update'],
            ['title' => 'Company Liquidation',     'url' => '/services/company-liquidation'],
        ]
    ],
    [
        'slug'  => 'visa-services',
        'name'  => 'Visa & Immigration',
        'icon'  => 'passport',
        'desc'  => 'Golden Visa, Investor, Employment & Family',
        'links' => [
            ['title' => 'UAE Golden Visa (10-Yr)',  'url' => '/services/golden-visa-services'],
            ['title' => 'Investor & Partner Visa', 'url' => '/services/investor-partner-visa'],
            ['title' => 'Employment Visa & MOHRE',  'url' => '/services/employment-visa'],
            ['title' => 'Family Sponsorship Visa',  'url' => '/services/family-visa'],
            ['title' => 'Freelance Visa & Permit',  'url' => '/services/freelance-visa'],
        ]
    ],
    [
        'slug'  => 'pro-services',
        'name'  => 'Government Approvals & PRO',
        'icon'  => 'shield-check',
        'desc'  => 'Corporate PRO, Municipal, SIRA, Police & MOFA',
        'links' => [
            ['title' => 'Corporate PRO Services',   'url' => '/services/pro-services'],
            ['title' => 'Local Service Agent (LSA)', 'url' => '/services/local-agent-service'],
            ['title' => 'Police Clearance',         'url' => '/services/police-clearance-certificate'],
            ['title' => 'Dubai Municipality (DM)',  'url' => '/services/dm-trade-licensing'],
            ['title' => 'SIRA Security Clearance',  'url' => '/services/sira-trade-licensing'],
        ]
    ],
    [
        'slug'  => 'vehicle-services',
        'name'  => 'Vehicle & Traffic Services',
        'icon'  => 'car',
        'desc'  => 'RTA Licenses, Salik, Darb & Mulkiya',
        'links' => [
            ['title' => 'Driving License Renewal',  'url' => '/services/driving-license-renewal'],
            ['title' => 'Dubai Salik Account & Tag', 'url' => '/services/dubai-salik'],
            ['title' => 'Abu Dhabi Darb Toll',      'url' => '/services/abu-dhabi-salik'],
            ['title' => 'Vehicle Insurance & Mulkiya', 'url' => '/services/vehicle-insurance-registration'],
        ]
    ],
    [
        'slug'  => 'banking-legal',
        'name'  => 'Corporate, Legal & Banking',
        'icon'  => 'scale',
        'desc'  => 'Bank Accounts, Corporate Tax, VAT & Contracts',
        'links' => [
            ['title' => 'Corporate Bank Account',   'url' => '/services/corporate-bank-account'],
            ['title' => 'VAT & Corporate Tax',      'url' => '/services/vat-tax-registration'],
            ['title' => 'Corporate Legal Support',  'url' => '/services/corporate-legal-support'],
            ['title' => 'Will Preparation (DIFC)',  'url' => '/services/will-preparation'],
            ['title' => 'Website & Business Email', 'url' => '/services/website-development'],
        ]
    ],
];
?>
<header class="site-header" role="banner">
  <div class="container header-inner">
    <a href="/" class="header-logo" aria-label="<?= e($siteName) ?> — Home">
      <img src="/assets/images/logo-white.svg?v=7" alt="<?= e($siteName) ?>" width="190" height="40">
    </a>

    <!-- Primary Navigation: Dropdown ONLY for Business Setup; all others are single direct links -->
    <nav class="header-nav" aria-label="Main Navigation">
      <a href="/desert-safari" class="header-nav-link<?= ($activeCatSlug === 'desert-safari') ? ' is-active' : '' ?>">Desert Safari</a>
      <a href="/city-tours" class="header-nav-link<?= ($activeCatSlug === 'city-tours') ? ' is-active' : '' ?>">City Tours</a>
      <a href="/cruise-dinner" class="header-nav-link<?= ($activeCatSlug === 'cruise-dinner') ? ' is-active' : '' ?>">Cruise Dinner</a>
      <a href="/watersports" class="header-nav-link<?= ($activeCatSlug === 'watersports') ? ' is-active' : '' ?>">Watersports</a>
      <a href="/tickets-attractions" class="header-nav-link<?= ($activeCatSlug === 'tickets-attractions') ? ' is-active' : '' ?>">Tickets &amp; Attractions</a>

      <!-- BUSINESS SETUP DROPDOWN ONLY -->
      <div class="nav-item-dropdown" id="businessSetupDropdown">
        <a href="/services"
           class="header-nav-link dropdown-toggle<?= (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/services') || ($activePage ?? '') === 'services') ? ' is-active' : '' ?>"
           id="businessSetupTrigger"
           aria-haspopup="true"
           aria-expanded="false">
          <span>Business Setup</span>
          <svg class="dropdown-chevron" viewBox="0 0 20 20" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
          </svg>
        </a>

        <!-- Structured Category Dropdown Menu -->
        <div class="nav-mega-menu" aria-labelledby="businessSetupTrigger" role="menu">
          <div class="nav-mega-grid">
            <?php foreach ($businessCategories as $bCat): ?>
              <div class="mega-column" role="none">
                <a href="/services#<?= e($bCat['slug']) ?>" class="mega-cat-heading" role="menuitem">
                  <span class="mega-cat-title"><?= e($bCat['name']) ?></span>
                  <span class="mega-cat-sub"><?= e($bCat['desc']) ?></span>
                </a>
                <ul class="mega-links-list" role="none">
                  <?php foreach ($bCat['links'] as $bLink): ?>
                    <li role="none">
                      <a href="<?= e($bLink['url']) ?>" class="mega-link" role="menuitem">
                        <?= e($bLink['title']) ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Bottom Dropdown Bar -->
          <div class="nav-mega-footer">
            <div class="mega-footer-left">
              <strong>All 31 Corporate Services Available</strong> &bull; Mainland &bull; Free Zones &bull; Golden Visas &bull; PRO Clearings
            </div>
            <div class="mega-footer-right">
              <a href="/services" class="mega-cta-btn">View All Business Setup Services &rarr;</a>
            </div>
          </div>
        </div>
      </div>

      <a href="/blog" class="header-nav-link<?= ($activePage ?? '') === 'blog' ? ' is-active' : '' ?>">Blog</a>
    </nav>

    <!-- Header Actions: WhatsApp & Mobile Menu Toggle -->
    <div class="header-actions">
      <a href="<?= e(waLinkHeader()) ?>"
         target="_blank"
         rel="noopener"
         class="btn btn-wa-header"
         data-wa-location="header"
         aria-label="Chat with us on WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.03 2C6.51 2 2.03 6.48 2.03 12c0 1.77.46 3.49 1.34 5.01L2 22l5.13-1.34A9.96 9.96 0 0 0 12.03 22c5.52 0 10-4.48 10-10S17.55 2 12.03 2zm5.82 14.12c-.24.68-1.4 1.3-1.95 1.38-.51.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.69-.63-2.98-1.29-4.93-4.29-5.08-4.49-.15-.2-1.21-1.61-1.21-3.07 0-1.46.77-2.18 1.04-2.48.27-.3.6-.37.8-.37.2 0 .4 0 .57.01.18.01.43-.07.67.51.25.6.84 2.06.91 2.21.08.15.13.32.03.52-.1.2-.15.32-.3.5-.15.17-.31.39-.45.52-.15.15-.3.31-.13.61.17.3.77 1.27 1.65 2.06 1.13 1.01 2.09 1.32 2.39 1.47.3.15.47.13.65-.08.17-.2.75-.87.95-1.17.2-.3.4-.25.67-.15.27.1 1.74.82 2.04.97.3.15.5.22.57.35.08.12.08.72-.16 1.4z"/></svg>
        <span class="wa-btn-label">WhatsApp Us</span>
      </a>

      <button type="button"
              id="mobileMenuToggle"
              class="menu-toggle"
              aria-label="Toggle navigation menu"
              aria-expanded="false"
              aria-controls="mobileNavDrawer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </div>
</header>

<!-- Mobile Navigation Backdrop & Drawer -->
<div id="mobileNavBackdrop" class="mobile-nav-backdrop" aria-hidden="true"></div>
<div id="mobileNavDrawer" class="mobile-nav-drawer" aria-label="Mobile Navigation Drawer">
  <div class="mobile-nav-header">
    <img src="/assets/images/logo-white.svg?v=7" alt="<?= e($siteName) ?>" width="160" height="34">
    <button type="button" id="mobileMenuClose" class="mobile-close-btn" aria-label="Close menu">&times;</button>
  </div>

  <ul class="mobile-nav-list">
    <li><a href="/desert-safari"><span>Desert Safari</span><span aria-hidden="true">&rsaquo;</span></a></li>
    <li><a href="/city-tours"><span>City Tours</span><span aria-hidden="true">&rsaquo;</span></a></li>
    <li><a href="/cruise-dinner"><span>Cruise Dinner</span><span aria-hidden="true">&rsaquo;</span></a></li>
    <li><a href="/watersports"><span>Watersports</span><span aria-hidden="true">&rsaquo;</span></a></li>
    <li><a href="/tickets-attractions"><span>Tickets &amp; Attractions (36+)</span><span aria-hidden="true">&rsaquo;</span></a></li>

    <!-- Accordion item ONLY for Business Setup -->
    <li class="mobile-accordion-item">
      <button type="button" class="mobile-accordion-btn" id="mobileBusinessAccordionBtn" aria-expanded="false">
        <span>Business Setup (All Categories)</span>
        <svg class="accordion-chevron" viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" /></svg>
      </button>
      <div class="mobile-accordion-content" id="mobileBusinessAccordionPanel" hidden>
        <?php foreach ($businessCategories as $bCat): ?>
          <div class="mobile-subcat-group">
            <a href="/services#<?= e($bCat['slug']) ?>" class="mobile-subcat-title"><?= e($bCat['name']) ?></a>
            <ul class="mobile-subcat-links">
              <?php foreach (array_slice($bCat['links'], 0, 4) as $subLink): ?>
                <li><a href="<?= e($subLink['url']) ?>"><?= e($subLink['title']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
        <a href="/services" class="mobile-view-all-services">View All 31 Business Setup Services &rarr;</a>
      </div>
    </li>

    <li><a href="/blog"><span>UAE Travel &amp; Business Blog</span><span aria-hidden="true">&rsaquo;</span></a></li>
    <li><a href="/book"><span>Book / Send Enquiry</span><span aria-hidden="true">&rsaquo;</span></a></li>
    <li><a href="/contact"><span>About &amp; Contact Us</span><span aria-hidden="true">&rsaquo;</span></a></li>
  </ul>

  <div class="mobile-nav-footer">
    <a href="<?= e(waLinkHeader()) ?>" target="_blank" rel="noopener" class="btn btn-wa" style="width:100%">
      Chat on WhatsApp (+971 52 537 6838)
    </a>
  </div>
</div>
