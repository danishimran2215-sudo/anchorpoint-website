<?php
/**
 * Shared Helper Functions — Anchor Point UAE Tourism Website
 */

/** HTML-encode output */
function e(?string $text): string {
    return htmlspecialchars((string)$text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Redirect and exit */
function redirect(string $url, int $code = 302): never {
    header("Location: $url", true, $code);
    exit;
}

/** Clean user input */
function sanitize(mixed $input): string {
    return trim(strip_tags((string)$input));
}

/** Is current request a POST? */
function isPost(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** Generate a URL-safe slug from text */
function slugify(string $text): string {
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/** Get real client IP */
function getClientIP(): string {
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Extract & persist UTM parameters across the visitor session */
function getUTM(): array {
    $params = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    $incoming = [];
    foreach ($params as $p) {
        if (isset($_GET[$p]) && $_GET[$p] !== '') {
            $incoming[$p] = sanitize($_GET[$p]);
        }
    }
    if (!empty($incoming)) {
        $_SESSION['utm'] = array_merge($_SESSION['utm'] ?? [], $incoming);
    }
    $result = [];
    foreach ($params as $p) {
        $result[$p] = sanitize($_SESSION['utm'][$p] ?? '');
    }
    return $result;
}

/** Simple pagination helper */
function paginate(int $total, int $perPage, int $currentPage, string $baseUrl): array {
    $totalPages = max(1, (int)ceil($total / max(1, $perPage)));
    $currentPage = max(1, min($currentPage, $totalPages));
    $pages = [];
    for ($i = 1; $i <= $totalPages; $i++) {
        $pages[] = [
            'num'     => $i,
            'url'     => $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . $i,
            'current' => $i === $currentPage,
        ];
    }
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'pages'       => $pages,
        'prev'        => $currentPage > 1 ? $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . ($currentPage - 1) : null,
        'next'        => $currentPage < $totalPages ? $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . ($currentPage + 1) : null,
        'offset'      => ($currentPage - 1) * $perPage,
    ];
}

/** Human-readable time ago */
function timeAgo(string $datetime): string {
    $ts = strtotime($datetime);
    if (!$ts) return $datetime;
    $diff = time() - $ts;
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return (int)($diff / 60) . ' min ago';
    if ($diff < 86400)  return (int)($diff / 3600) . ' hr ago';
    if ($diff < 604800) return (int)($diff / 86400) . ' days ago';
    return date('d M Y', $ts);
}

/** Truncate text to word boundary */
function truncate(string $text, int $length = 160): string {
    if (mb_strlen($text) <= $length) return $text;
    $sub = mb_substr($text, 0, $length);
    $sp  = mb_strrpos($sub, ' ');
    return ($sp !== false ? mb_substr($sub, 0, $sp) : $sub) . '…';
}

/** Settings cache & getters/setters */
function &getSettingsCache(): array {
    static $cache = [];
    return $cache;
}

function getSetting(string $key, string $default = ''): string {
    $cache = &getSettingsCache();
    if (!array_key_exists($key, $cache)) {
        try {
            $db = getDB();
            $st = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
            $st->execute([$key]);
            $row = $st->fetch();
            $cache[$key] = ($row && $row['setting_value'] !== null && $row['setting_value'] !== '')
                ? (string)$row['setting_value']
                : null;
        } catch (Exception $e) {
            return $default;
        }
    }
    return $cache[$key] !== null ? (string)$cache[$key] : $default;
}

function setSetting(string $key, string $value): void {
    $db = getDB();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $st = $db->prepare('INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)');
        $st->execute([$key, $value]);
    } else {
        $st = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $st->execute([$key, $value]);
    }
    $cache = &getSettingsCache();
    $cache[$key] = $value;
}

/** Whether AED prices are displayed on the website ('1' = visible by default per PDF, '0' = hidden) */
function arePricesVisible(): bool {
    return getSetting('show_prices', '1') === '1';
}

/** Whether AED prices are displayed for Business Setup services ('1' = visible, '0' = hidden) */
function areServicePricesVisible(): bool {
    return getSetting('show_service_prices', '1') === '1';
}

/** Active WhatsApp number in international digits-only format (no + or spaces) */
function getWhatsAppNumber(): string {
    $raw = getSetting('whatsapp_number', WHATSAPP_NUMBER);
    $digits = preg_replace('/[^0-9]/', '', $raw);
    return $digits !== '' ? $digits : WHATSAPP_NUMBER;
}

/** Human-formatted primary phone */
function getPrimaryPhone(): string {
    return getSetting('phone_display', PHONE_1);
}

/** Raw tel: link phone */
function getPrimaryPhoneRaw(): string {
    $p = preg_replace('/[^0-9+]/', '', getPrimaryPhone());
    return $p !== '' ? $p : PHONE_1_RAW;
}

/** Editable main company email */
function getCompanyEmail(): string {
    return getSetting('company_email', ADMIN_EMAIL);
}

/** Build WhatsApp wa.me link (Section 9 exact format: https://wa.me/971XXXXXXXXX?text=...) */
function buildWhatsAppLink(string $number = '', string $message = ''): string {
    $num = preg_replace('/[^0-9]/', '', $number ?: getWhatsAppNumber());
    $url = 'https://wa.me/' . $num;
    if ($message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }
    return $url;
}

/** Exact Section 9 WhatsApp links by location */
function waLinkHeader(): string {
    return buildWhatsAppLink('', 'Hi, I would like to know more about your tours.');
}

function waLinkFloating(): string {
    return buildWhatsAppLink('', 'Hi, I have a question about your tours.');
}

function waLinkHero(): string {
    return buildWhatsAppLink('', 'Hi, please help me plan my trip.');
}

function waLinkCard(string $activityName): string {
    return buildWhatsAppLink('', 'Hi, I want to book: ' . $activityName . '.');
}

function waLinkDetail(string $activityName, string $date = '', int $guests = 2): string {
    $d = $date !== '' ? $date : date('Y-m-d', strtotime('+1 day'));
    $g = max(1, $guests);
    return buildWhatsAppLink('', 'Hi, I want to book ' . $activityName . ' on ' . $d . ' for ' . $g . ' guests.');
}

function waLinkCheckout(string $activityName = 'a tour', string $customerName = ''): string {
    $act = trim($activityName) !== '' ? trim($activityName) : 'a UAE tour';
    $nm  = trim($customerName) !== '' ? trim($customerName) : 'Guest';
    return buildWhatsAppLink('', 'Hi, I just sent an enquiry for ' . $act . '. My name is ' . $nm . '.');
}

function waLinkThankYouConfirm(): string {
    return buildWhatsAppLink('', 'Hi, I submitted the form and would like to confirm.');
}

function waLinkFooter(): string {
    return buildWhatsAppLink('', 'Hi');
}

/** Build canonical activity URL: /category/activity-name */
function activityUrl(array $tour): string {
    $catSlug = $tour['category_slug'] ?? 'desert-safari';
    return '/' . trim($catSlug, '/') . '/' . trim($tour['slug'], '/');
}

/** Resolve local image URL with cache-busting */
function tourImageUrl(?string $url): string {
    $u = trim((string)$url);
    if ($u === '') return '/assets/images/logo.svg';
    // New attraction-ticket photos: fall back to a shared ticket gallery image if the file is not uploaded yet
    if (str_starts_with($u, '/uploads/attractions/') && !str_contains($u, '?')) {
        $root = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
        if (!is_file($root . $u)) {
            // Closest-matching shared ticket photo for the attraction's group until its own photo is uploaded
            $pools = [
                'landmarks' => [1, 2], 'theme-parks' => [5, 6, 7], 'water-parks' => [8], 'aquariums-wildlife' => [4],
                'museums-culture' => [3, 9], 'gardens-events' => [2, 1], 'indoor-adventure' => [6, 7], 'combo-passes' => [1, 5],
            ];
            $grp  = ticketGroupOf(['slug' => pathinfo($u, PATHINFO_FILENAME)]);
            $pool = $pools[$grp] ?? [1];
            return '/uploads/activities/gallery-tickets-' . $pool[abs(crc32($u)) % count($pool)] . '.webp?v=4';
        }
        return $u . '?v=1';
    }
    if ((str_starts_with($u, '/uploads/activities/') || str_starts_with($u, '/uploads/tours/')) && !str_contains($u, '?')) {
        return $u . '?v=4';
    }
    return $u;
}

/** Resolve a Business Setup service photo with automatic fallback and cache-busting */
function serviceImageUrl(?string $path, string $categorySlug = ''): string {
    $p = trim((string)$path);
    if ($p === '') {
        $fallbacks = [
            'company-formation' => '/uploads/services/new-trade-license.jpg',
            'visa-services'     => '/uploads/services/pro-services.jpg',
            'pro-services'      => '/uploads/services/pro-services.jpg',
            'vehicle-services'  => '/uploads/services/driving-license-renewal.jpg',
            'banking-legal'     => '/uploads/services/corporate-legal-support.jpg',
        ];
        $p = $fallbacks[$categorySlug] ?? '/uploads/services/new-trade-license.jpg';
    }
    return $p . (!str_contains($p, '?') ? '?v=3' : '');
}

/** Attraction-ticket groups (category-wise navigation inside "Tickets & Attractions") */
function getTicketGroups(): array {
    return [
        'landmarks'         => ['label' => 'Landmarks & Observation Decks', 'short' => 'Landmarks',       'icon' => '🏙️', 'desc' => 'Skyline views, observation decks and iconic Dubai landmarks.'],
        'theme-parks'       => ['label' => 'Theme Parks',                   'short' => 'Theme Parks',     'icon' => '🎢', 'desc' => 'Dubai and Abu Dhabi theme parks with skip-the-line entry.'],
        'water-parks'       => ['label' => 'Water Parks',                   'short' => 'Water Parks',     'icon' => '🌊', 'desc' => 'Slides, wave pools and lazy rivers for all ages.'],
        'aquariums-wildlife'=> ['label' => 'Aquariums & Wildlife',          'short' => 'Aquariums & Wildlife', 'icon' => '🐠', 'desc' => 'Underwater tunnels, safari parks and rainforest domes.'],
        'museums-culture'   => ['label' => 'Museums & Culture',             'short' => 'Museums & Culture','icon' => '🏛️', 'desc' => 'Museums, palaces and immersive art experiences.'],
        'gardens-events'    => ['label' => 'Gardens & Events',              'short' => 'Gardens & Events','icon' => '🌸', 'desc' => 'Flower gardens, night markets and seasonal events.'],
        'indoor-adventure'  => ['label' => 'Indoor Fun & Adventure',        'short' => 'Indoor Adventure','icon' => '⛷️', 'desc' => 'Snow parks, indoor skydiving, kids cities and adventure zones.'],
        'combo-passes'      => ['label' => 'Combo Tickets & Multi-Park Passes', 'short' => 'Combo Passes','icon' => '🎟️', 'desc' => 'Save more with bundled tickets and multi-park passes.'],
    ];
}

/** Which ticket group an attraction belongs to */
function ticketGroupOf(array $tour): string {
    static $map = [
        'burj-khalifa-at-the-top' => 'landmarks', 'dubai-frame' => 'landmarks', 'the-view-at-the-palm' => 'landmarks', 'sky-views-dubai' => 'landmarks',
        'ferrari-world-abu-dhabi' => 'theme-parks', 'warner-bros-world-abu-dhabi' => 'theme-parks', 'img-worlds-of-adventure' => 'theme-parks',
        'motiongate-dubai' => 'theme-parks', 'legoland-dubai' => 'theme-parks', 'bollywood-parks-dubai' => 'theme-parks', 'seaworld-abu-dhabi' => 'theme-parks',
        'aquaventure-waterpark' => 'water-parks', 'wild-wadi-waterpark' => 'water-parks', 'yas-waterworld' => 'water-parks', 'legoland-water-park' => 'water-parks',
        'dubai-aquarium-and-underwater-zoo' => 'aquariums-wildlife', 'lost-chambers-aquarium' => 'aquariums-wildlife', 'dubai-safari-park' => 'aquariums-wildlife', 'green-planet-dubai' => 'aquariums-wildlife',
        'louvre-abu-dhabi' => 'museums-culture', 'museum-of-the-future' => 'museums-culture', 'etihad-museum' => 'museums-culture',
        'teamlab-phenomena-abu-dhabi' => 'museums-culture', 'qasr-al-watan' => 'museums-culture',
        'dubai-miracle-garden' => 'gardens-events', 'global-village-dubai' => 'gardens-events',
        'ski-dubai' => 'indoor-adventure', 'ifly-dubai' => 'indoor-adventure', 'skydive-dubai' => 'indoor-adventure', 'kidzania-dubai' => 'indoor-adventure',
        'play-dxb' => 'indoor-adventure', 'snow-abu-dhabi' => 'indoor-adventure', 'clymb-abu-dhabi' => 'indoor-adventure',
        'burj-khalifa-and-dubai-aquarium-combo' => 'combo-passes', 'yas-island-multi-park-pass' => 'combo-passes', 'dubai-parks-and-resorts-multi-park-pass' => 'combo-passes',
    ];
    $det = json_decode((string)($tour['details_json'] ?? '{}'), true);
    if (is_array($det) && !empty($det['ticket_group']) && isset(getTicketGroups()[$det['ticket_group']])) {
        return $det['ticket_group'];
    }
    $slug = (string)($tour['slug'] ?? '');
    if (isset($map[$slug])) return $map[$slug];
    $type = strtolower((string)($tour['badge'] ?? ''));
    if (str_contains($type, 'museum')) return 'museums-culture';
    if (str_contains($type, 'water'))  return 'water-parks';
    if (str_contains($type, 'theme'))  return 'theme-parks';
    if (str_contains($type, 'combo') || str_contains($type, 'pass')) return 'combo-passes';
    return 'landmarks';
}

/** Business Setup categories, each with its sub-services (for menus, hub page and footer) */
function getServicesGrouped(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cats = getServiceCategories();
    $all  = getServices(true);
    $by   = [];
    foreach ($all as $s) {
        $by[(int)$s['category_id']][] = $s;
    }
    foreach ($cats as &$c) {
        $c['services'] = $by[(int)$c['id']] ?? [];
    }
    unset($c);
    return $cache = $cats;
}

/** Generate cryptographically secure random token */
function generateToken(int $length = 32): string {
    return bin2hex(random_bytes($length));
}

/** Convert an uploaded image to WebP when GD WebP is available */
function convertToWebP(string $source, string $dest, int $quality = 84): bool {
    if (!function_exists('imagewebp')) return false;
    $info = @getimagesize($source);
    if (!$info) return false;
    $image = match ($info['mime']) {
        'image/jpeg' => @imagecreatefromjpeg($source),
        'image/png'  => @imagecreatefrompng($source),
        'image/gif'  => @imagecreatefromgif($source),
        'image/webp' => @imagecreatefromwebp($source),
        default      => false,
    };
    if (!$image) return false;
    imagepalettetotruecolor($image);
    $ok = @imagewebp($image, $dest, $quality);
    imagedestroy($image);
    return (bool)$ok;
}

/** Secure file upload — auto-converts raster uploads to .webp when supported */
function uploadFile(array $file, string $destDir, array $allowedMimes = [], int $maxSize = 0): string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload error code: ' . ($file['error'] ?? -1));
    }
    $maxSize = $maxSize ?: UPLOAD_MAX_SIZE;
    $allowed = $allowedMimes ?: UPLOAD_ALLOWED_TYPES;
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('File too large. Max ' . round($maxSize / 1048576, 1) . ' MB.');
    }
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('File type not allowed: ' . $mime);
    }
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
    $baseName = generateToken(12);
    $rawPath  = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $rawPath)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }
    // Convert JPEG/PNG to WebP for Core Web Vitals speed
    if (in_array($mime, ['image/jpeg', 'image/png'], true) && function_exists('imagewebp')) {
        $webpPath = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $baseName . '.webp';
        if (convertToWebP($rawPath, $webpPath, 84) && file_exists($webpPath)) {
            @unlink($rawPath);
            return $baseName . '.webp';
        }
    }
    return $baseName . '.' . $ext;
}

/** Get current full URL */
function currentUrl(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri    = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
}

/** JSON response helper */
function jsonResponse(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** The 5 official UAE Tourism Category Slugs */
function getOfficialCategorySlugs(): array {
    return [
        'desert-safari',
        'city-tours',
        'cruise-dinner',
        'watersports',
        'tickets-attractions',
    ];
}

/** Get all active tour categories in exact order */
function getTourCategories(): array {
    try {
        $db = getDB();
        $st = $db->query('SELECT * FROM tour_categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/** Get single tour category by slug */
function getTourCategoryBySlug(string $slug): ?array {
    try {
        $db = getDB();
        $st = $db->prepare('SELECT * FROM tour_categories WHERE slug = ? AND is_active = 1 LIMIT 1');
        $st->execute([$slug]);
        $row = $st->fetch();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/** Get activities with optional filters */
function getTours(array $filters = []): array {
    try {
        $db = getDB();
        $sql = '
            SELECT t.*, c.name AS category_name, c.section_title AS category_section_title, c.slug AS category_slug
            FROM tours t
            JOIN tour_categories c ON t.category_id = c.id
            WHERE t.is_active = 1 AND c.is_active = 1
        ';
        $params = [];

        if (!empty($filters['category_slug'])) {
            $sql .= ' AND c.slug = ?';
            $params[] = $filters['category_slug'];
        }
        if (!empty($filters['category_id'])) {
            $sql .= ' AND t.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $sql .= ' AND t.is_featured = ?';
            $params[] = (int)$filters['is_featured'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (t.title LIKE ? OR t.short_desc LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params[] = $q;
            $params[] = $q;
        }

        $sql .= ' ORDER BY c.sort_order ASC, t.sort_order ASC, t.id ASC';

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ' . (int)$filters['limit'];
        }

        $st = $db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/** Get single activity by category_slug + activity slug */
function getTourByCategoryAndSlug(string $categorySlug, string $slug): ?array {
    try {
        $db = getDB();
        $st = $db->prepare('
            SELECT t.*, c.name AS category_name, c.section_title AS category_section_title,
                   c.slug AS category_slug, c.cta_text AS category_cta_text
            FROM tours t
            JOIN tour_categories c ON t.category_id = c.id
            WHERE c.slug = ? AND t.slug = ? AND t.is_active = 1
            LIMIT 1
        ');
        $st->execute([$categorySlug, $slug]);
        $row = $st->fetch();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/** Get single activity by slug alone (for legacy redirects or quick lookup) */
function getTourBySlug(string $slug): ?array {
    try {
        $db = getDB();
        $st = $db->prepare('
            SELECT t.*, c.name AS category_name, c.section_title AS category_section_title,
                   c.slug AS category_slug, c.cta_text AS category_cta_text
            FROM tours t
            JOIN tour_categories c ON t.category_id = c.id
            WHERE t.slug = ? AND t.is_active = 1
            LIMIT 1
        ');
        $st->execute([$slug]);
        $row = $st->fetch();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/** Get related activities in same category */
function getRelatedTours(int $categoryId, int $excludeId, int $limit = 4): array {
    try {
        $db = getDB();
        $st = $db->prepare('
            SELECT t.*, c.name AS category_name, c.slug AS category_slug
            FROM tours t
            JOIN tour_categories c ON t.category_id = c.id
            WHERE t.category_id = ? AND t.id != ? AND t.is_active = 1
            ORDER BY t.sort_order ASC, t.id ASC
            LIMIT ' . (int)$limit
        );
        $st->execute([$categoryId, $excludeId]);
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/** Country dial codes for Phone / WhatsApp country picker (Default: UAE +971) */
function getCountryDialCodes(): array {
    return [
        ['code' => '+971', 'label' => '🇦🇪 UAE (+971)'],
        ['code' => '+966', 'label' => '🇸🇦 Saudi Arabia (+966)'],
        ['code' => '+974', 'label' => '🇶🇦 Qatar (+974)'],
        ['code' => '+965', 'label' => '🇰🇼 Kuwait (+965)'],
        ['code' => '+973', 'label' => '🇧🇭 Bahrain (+973)'],
        ['code' => '+968', 'label' => '🇴🇲 Oman (+968)'],
        ['code' => '+44',  'label' => '🇬🇧 UK (+44)'],
        ['code' => '+1',   'label' => '🇺🇸 USA / Canada (+1)'],
        ['code' => '+91',  'label' => '🇮🇳 India (+91)'],
        ['code' => '+92',  'label' => '🇵🇰 Pakistan (+92)'],
        ['code' => '+49',  'label' => '🇩🇪 Germany (+49)'],
        ['code' => '+33',  'label' => '🇫🇷 France (+33)'],
        ['code' => '+39',  'label' => '🇮🇹 Italy (+39)'],
        ['code' => '+34',  'label' => '🇪🇸 Spain (+34)'],
        ['code' => '+31',  'label' => '🇳🇱 Netherlands (+31)'],
        ['code' => '+41',  'label' => '🇨🇭 Switzerland (+41)'],
        ['code' => '+7',   'label' => '🇷🇺 Russia / KZ (+7)'],
        ['code' => '+86',  'label' => '🇨🇳 China (+86)'],
        ['code' => '+61',  'label' => '🇦🇺 Australia (+61)'],
        ['code' => '+63',  'label' => '🇵🇭 Philippines (+63)'],
        ['code' => '+20',  'label' => '🇪🇬 Egypt (+20)'],
        ['code' => '+27',  'label' => '🇿🇦 South Africa (+27)'],
    ];
}

// ── Corporate & Business Setup Service Helpers ─────────────
function getServices(bool $activeOnly = true, ?int $categoryId = null, ?int $limit = null): array {
    try {
        $db = getDB();
        $sql = 'SELECT s.*, c.name as category_name, c.slug as category_slug FROM services s LEFT JOIN service_categories c ON s.category_id = c.id WHERE 1=1';
        $params = [];
        if ($activeOnly) {
            $sql .= ' AND s.is_active = 1';
        }
        if ($categoryId !== null) {
            $sql .= ' AND s.category_id = ?';
            $params[] = $categoryId;
        }
        $sql .= ' ORDER BY s.sort_order ASC, s.id ASC';
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        $st = $db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getFeaturedServices(int $limit = 8): array {
    try {
        $db = getDB();
        $st = $db->prepare('SELECT s.*, c.name as category_name, c.slug as category_slug FROM services s LEFT JOIN service_categories c ON s.category_id = c.id WHERE s.is_active = 1 AND s.is_featured = 1 ORDER BY s.sort_order ASC LIMIT ' . (int)$limit);
        $st->execute();
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getServiceBySlug(string $slug): ?array {
    try {
        $db = getDB();
        $st = $db->prepare('SELECT s.*, c.name as category_name, c.slug as category_slug FROM services s LEFT JOIN service_categories c ON s.category_id = c.id WHERE s.slug = ? LIMIT 1');
        $st->execute([$slug]);
        $row = $st->fetch();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getServiceCategories(): array {
    try {
        $db = getDB();
        return $db->query('SELECT * FROM service_categories WHERE is_active = 1 ORDER BY sort_order ASC')->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// ── SEO Blog & Knowledge Base Helpers ──────────────────────
function getBlogPosts(bool $publishedOnly = true, ?int $limit = null, int $offset = 0): array {
    try {
        $db = getDB();
        $sql = 'SELECT * FROM blog_posts WHERE 1=1';
        if ($publishedOnly) {
            $sql .= ' AND is_published = 1';
        }
        $sql .= ' ORDER BY published_at DESC, id DESC';
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        }
        return $db->query($sql)->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getBlogPostBySlug(string $slug): ?array {
    try {
        $db = getDB();
        $st = $db->prepare('SELECT * FROM blog_posts WHERE slug = ? LIMIT 1');
        $st->execute([$slug]);
        $row = $st->fetch();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getRecentBlogPosts(int $limit = 3, ?int $excludeId = null): array {
    try {
        $db = getDB();
        $sql = 'SELECT * FROM blog_posts WHERE is_published = 1';
        $params = [];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY published_at DESC LIMIT ' . (int)$limit;
        $st = $db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Automatically persist incoming UTM parameters even when loaded from an older config.php
if (session_status() === PHP_SESSION_ACTIVE) {
    getUTM();
}
