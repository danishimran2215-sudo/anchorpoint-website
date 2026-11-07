<?php
/**
 * Anchor Point Business Setup & Travel — Built-in PHP Web Server Router
 * Usage: php -S localhost:8000 router.php
 */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $uri;

// Block sensitive files & internal directories
if (preg_match('#^/(\.git|\.env|config(\.local|\.example)?\.php|database\.sqlite|database\.sql|includes/|logs/|uploads/.*\.php$)#i', $uri)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

// Strip trailing slash on non-root non-directory URLs
if ($uri !== '/' && str_ends_with($uri, '/') && !is_dir($file)) {
    $clean = rtrim($uri, '/');
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . $clean . $qs, true, 302);
    exit;
}

// Serve existing static assets directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'png'   => 'image/png',
        'gif'   => 'image/gif',
        'ico'   => 'image/x-icon',
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'woff2' => 'font/woff2',
        'txt'   => 'text/plain; charset=UTF-8',
    ];
    if ($ext !== 'php' && isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        readfile($file);
        exit;
    }
    return false;
}

// On-demand image fallbacks
if (preg_match('#^/uploads/activities/([a-z0-9-]+\.webp)$#', $uri, $m)) {
    $_GET['file'] = $m[1];
    require __DIR__ . '/api/activity-image.php';
    exit;
}
if (preg_match('#^/uploads/(services|attractions)/([a-z0-9-]+\.(jpg|jpeg|png|webp))$#i', $uri, $m)) {
    $_GET['file'] = $m[2];
    require __DIR__ . '/api/service-image.php';
    exit;
}

// Root homepage
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
    exit;
}

// Tours slug fallback
if ($uri === '/tours') {
    require __DIR__ . '/index.php';
    exit;
}
if (preg_match('#^/tours/([a-z0-9-]+)$#i', $uri, $m)) {
    $_GET['slug'] = strtolower($m[1]);
    require __DIR__ . '/tours/view.php';
    exit;
}

// Business Setup & Services detail route
if (preg_match('#^/services/([a-z0-9-]+)$#i', $uri, $m)) {
    $_GET['slug'] = strtolower($m[1]);
    require __DIR__ . '/services/view.php';
    exit;
}

// Blog detail route
if (preg_match('#^/blog/([a-z0-9-]+)$#i', $uri, $m)) {
    $_GET['slug'] = strtolower($m[1]);
    require __DIR__ . '/blog/post.php';
    exit;
}

// Exact static routes
$routes = [
    '/services'             => '/services/index.php',
    '/business-setup'       => '/services/index.php',
    '/blog'                 => '/blog/index.php',
    '/book'                 => '/book.php',
    '/thank-you'            => '/thank-you.php',
    '/contact'              => '/contact.php',
    '/about'                => '/contact.php',
    '/privacy'              => '/privacy-policy.php',
    '/privacy-policy'       => '/privacy-policy.php',
    '/terms'                => '/terms.php',
    '/cancellation-policy'  => '/cancellation-policy.php',
    '/sitemap.xml'          => '/sitemap.xml.php',
    '/admin'                => '/admin/index.php',
    '/admin/login'          => '/admin/login.php',
    '/admin/logout'         => '/admin/logout.php',
    '/admin/dashboard'      => '/admin/dashboard.php',
    '/admin/tours'          => '/admin/tours/index.php',
    '/admin/tours/edit'     => '/admin/tours/edit.php',
    '/admin/tours/bookings' => '/admin/tours/bookings.php',
    '/admin/services'       => '/admin/services/index.php',
    '/admin/services/edit'  => '/admin/services/edit.php',
    '/admin/blog'           => '/admin/blog/index.php',
    '/admin/blog/edit'      => '/admin/blog/edit.php',
    '/admin/settings'       => '/admin/settings/index.php',
    '/admin/users'          => '/admin/users/index.php',
];

if (isset($routes[$uri])) {
    require __DIR__ . $routes[$uri];
    exit;
}

// Official 5 Category Landing Pages: /<category>
if (preg_match('#^/(desert-safari|city-tours|cruise-dinner|watersports|tickets-attractions)/?$#i', $uri, $m)) {
    $_GET['category_slug'] = strtolower($m[1]);
    $_GET['category']      = strtolower($m[1]);
    require __DIR__ . '/tours/category.php';
    exit;
}

// Official Activity Detail Pages: /<category>/<activity-name>
if (preg_match('#^/(desert-safari|city-tours|cruise-dinner|watersports|tickets-attractions)/([a-z0-9-]+)/?$#i', $uri, $m)) {
    $_GET['category_slug'] = strtolower($m[1]);
    $_GET['category']      = strtolower($m[1]);
    $_GET['slug']          = strtolower($m[2]);
    require __DIR__ . '/tours/view.php';
    exit;
}

// API endpoints
if (str_starts_with($uri, '/api/')) {
    $apiFile = __DIR__ . $uri;
    if (file_exists($apiFile) && !is_dir($apiFile)) {
        require $apiFile;
        exit;
    }
    if (file_exists($apiFile . '.php')) {
        require $apiFile . '.php';
        exit;
    }
}

// 404 Not Found
http_response_code(404);
require __DIR__ . '/404.php';
exit;
