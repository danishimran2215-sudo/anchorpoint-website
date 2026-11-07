<?php
/**
 * 404 Page Not Found — Anchor Point Travel & Tourism
 */
require_once __DIR__ . '/config.php';
http_response_code(404);

$pageTitle = 'Page Not Found (404) | ' . SITE_NAME;
$pageDesc  = 'The page you requested could not be found. Browse our UAE Desert Safaris, City Tours, Dinner Cruises, Watersports, and Attraction Tickets.';
$noIndex   = true;

require INCLUDES_PATH . 'head.php';
require INCLUDES_PATH . 'header.php';
?>

<main id="mainContent">
  <section class="detail-section" style="padding:4.5rem 0">
    <div class="container">
      <div class="prose-card" style="max-width:640px;text-align:center">
        <div style="font-size:3rem;font-weight:800;color:var(--blue-600);line-height:1;margin-bottom:0.5rem">404</div>
        <h1 style="font-size:1.75rem;color:var(--navy-900);margin-bottom:0.6rem">Page Not Found</h1>
        <p style="color:var(--slate-600);margin-bottom:1.5rem">
          The page you are looking for may have moved. Explore our 5 main UAE experience categories below or message us on WhatsApp.
        </p>
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:0.6rem;margin-bottom:1.5rem">
          <a href="/desert-safari" class="btn btn-outline">Desert Safari</a>
          <a href="/city-tours" class="btn btn-outline">City Tours</a>
          <a href="/cruise-dinner" class="btn btn-outline">Cruise Dinner</a>
          <a href="/watersports" class="btn btn-outline">Watersports</a>
          <a href="/tickets-attractions" class="btn btn-outline">Tickets &amp; Attractions</a>
        </div>
        <div style="display:flex;justify-content:center;gap:0.75rem;flex-wrap:wrap">
          <a href="/" class="btn btn-primary">Go to Homepage</a>
          <a href="<?= e(waLinkHeader()) ?>" target="_blank" rel="noopener" class="btn btn-wa" data-wa-location="404_page">Chat on WhatsApp</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require INCLUDES_PATH . 'footer.php'; ?>
