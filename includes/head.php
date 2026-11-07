<?php
/**
 * HTML <head> Partial — Anchor Point Travel & Tourism
 * Unique SEO titles, meta descriptions, OpenGraph, Schema.org JSON-LD, GA4/GTM/Meta Pixel
 */
$siteName     = getSetting('site_name', SITE_NAME);
$metaTitle    = !empty($pageTitle) ? $pageTitle : ($siteName . ' — Desert Safari, City Tours, Cruises & Attractions in UAE');
$metaDesc     = !empty($pageDesc)  ? $pageDesc  : 'Book Dubai Desert Safari, UAE City Tours, Marina & Creek Dinner Cruises, Watersports, and Attraction Tickets with instant WhatsApp confirmation.';
$reqPath      = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonical    = !empty($canonicalUrl) ? $canonicalUrl : (rtrim(SITE_URL, '/') . ($reqPath === '/' ? '/' : rtrim($reqPath, '/')));
$ogImgPath    = !empty($ogImage) ? $ogImage : '/uploads/activities/evening-desert-safari-cover.webp';
$ogImgFull    = str_starts_with($ogImgPath, 'http') ? $ogImgPath : (rtrim(SITE_URL, '/') . '/' . ltrim(preg_replace('/\?.*$/', '', $ogImgPath), '/'));
$ogTypeVal    = !empty($ogType) ? $ogType : 'website';
$robotsMeta   = !empty($noIndex) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large';

$gaId         = trim(getSetting('ga_id', GA_ID));
$gtmId        = trim(getSetting('gtm_id', GTM_ID));
$metaPixelId  = trim(getSetting('meta_pixel_id', META_PIXEL));
$turnstileKey = trim(getSetting('turnstile_site_key', ''));

$orgSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'TravelAgency',
    'name'        => $siteName,
    'url'         => rtrim(SITE_URL, '/') . '/',
    'logo'        => rtrim(SITE_URL, '/') . '/assets/images/logo.svg',
    'image'       => $ogImgFull,
    'telephone'   => getPrimaryPhone(),
    'email'       => getCompanyEmail(),
    'priceRange'  => 'AED 55 - AED 2200',
    'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => getSetting('office_address', OFFICE_ADDRESS),
        'addressLocality' => 'Dubai',
        'addressCountry'  => 'AE',
    ],
    'contactPoint' => [
        '@type'             => 'ContactPoint',
        'telephone'         => getPrimaryPhone(),
        'contactType'       => 'customer service',
        'areaServed'        => 'AE',
        'availableLanguage' => ['English', 'Arabic', 'Hindi', 'Urdu'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($metaTitle) ?></title>
  <meta name="description" content="<?= e($metaDesc) ?>">
  <meta name="robots" content="<?= e($robotsMeta) ?>">
  <link rel="canonical" href="<?= e($canonical) ?>">

  <!-- OpenGraph / Social Sharing -->
  <meta property="og:type" content="<?= e($ogTypeVal) ?>">
  <meta property="og:site_name" content="<?= e($siteName) ?>">
  <meta property="og:title" content="<?= e($metaTitle) ?>">
  <meta property="og:description" content="<?= e($metaDesc) ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:image" content="<?= e($ogImgFull) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($metaTitle) ?>">
  <meta name="twitter:description" content="<?= e($metaDesc) ?>">
  <meta name="twitter:image" content="<?= e($ogImgFull) ?>">

  <link rel="icon" type="image/svg+xml" href="/assets/images/logo-icon.svg?v=6">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/tourism.css?v=9">

  <script type="application/ld+json"><?= json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <?php if (!empty($jsonLd)): ?>
  <script type="application/ld+json"><?= is_string($jsonLd) ? $jsonLd : json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <?php endif; ?>

  <?php if ($gtmId !== ''): ?>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= e($gtmId) ?>');</script>
  <?php endif; ?>

  <?php if ($gaId !== ''): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($gaId) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= e($gaId) ?>');
  </script>
  <?php endif; ?>

  <?php if ($metaPixelId !== ''): ?>
  <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= e($metaPixelId) ?>');
    fbq('track', 'PageView');
  </script>
  <?php endif; ?>

  <?php if ($turnstileKey !== ''): ?>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <?php endif; ?>
</head>
<body>
<?php if ($gtmId !== ''): ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($gtmId) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>
