<?php
/**
 * PDO Database Singleton — Anchor Point UAE Tourism Website
 * Connects to MySQL in production/cPanel, with seamless SQLite fallback for local development.
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    // Try MySQL first when configured
    if (defined('DB_NAME') && DB_NAME !== 'YOUR_DB_NAME' && DB_NAME !== '') {
        try {
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, $charset);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            ensureTourismSchema($pdo);
            return $pdo;
        } catch (PDOException $e) {
            if (APP_ENV === 'production' && defined('FORCE_MYSQL') && FORCE_MYSQL) {
                die('Database connection failed. Please verify config.php credentials.');
            }
        }
    }

    // Local / Portable SQLite Database
    $sqlitePath = __DIR__ . '/../database.sqlite';
    try {
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        ensureTourismSchema($pdo);
        return $pdo;
    } catch (Exception $e) {
        die('Database initialization error: ' . $e->getMessage());
    }
}

/**
 * Ensure all UAE Tourism tables & columns exist across both SQLite and MySQL
 */
function ensureTourismSchema(PDO $db): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $isSqlite = ($driver === 'sqlite');

    if ($isSqlite) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT UNIQUE,
                setting_value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                email TEXT UNIQUE,
                password_hash TEXT,
                role TEXT DEFAULT 'editor',
                is_active INTEGER DEFAULT 1,
                last_login DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip TEXT,
                attempted_at DATETIME
            );
            CREATE TABLE IF NOT EXISTS activity_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action TEXT,
                entity_type TEXT,
                entity_id INTEGER DEFAULT 0,
                details TEXT,
                ip TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS tour_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                section_title TEXT,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                cta_text TEXT,
                seo_keywords TEXT,
                icon TEXT DEFAULT 'compass',
                image TEXT,
                sort_order INTEGER DEFAULT 0,
                seo_title TEXT,
                seo_desc TEXT,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS tours (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                badge TEXT,
                short_desc TEXT,
                long_desc TEXT,
                duration TEXT,
                pickup_line TEXT,
                has_pickup INTEGER DEFAULT 1,
                price_aed REAL NOT NULL DEFAULT 0,
                child_price_aed REAL DEFAULT 0,
                original_price_aed REAL,
                age_policy TEXT,
                rating REAL DEFAULT 4.9,
                reviews_count INTEGER DEFAULT 120,
                featured_image TEXT,
                gallery_json TEXT,
                itinerary_json TEXT,
                inclusions_json TEXT,
                exclusions_json TEXT,
                details_json TEXT,
                pickup_details TEXT,
                availability TEXT DEFAULT 'Daily',
                faq_json TEXT,
                is_featured INTEGER DEFAULT 0,
                is_combo INTEGER DEFAULT 0,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                seo_title TEXT,
                seo_desc TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS tour_bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_ref TEXT,
                tour_id INTEGER,
                category_name TEXT,
                tour_title TEXT,
                customer_name TEXT NOT NULL,
                customer_email TEXT NOT NULL,
                country_code TEXT DEFAULT '+971',
                customer_phone TEXT NOT NULL,
                booking_date TEXT NOT NULL,
                preferred_time TEXT,
                adults_count INTEGER DEFAULT 1,
                children_count INTEGER DEFAULT 0,
                children_ages TEXT,
                guests_count INTEGER DEFAULT 1,
                pickup_location TEXT,
                nationality TEXT,
                special_requests TEXT,
                consent_given INTEGER DEFAULT 1,
                source_page TEXT,
                utm_source TEXT,
                utm_medium TEXT,
                utm_campaign TEXT,
                utm_term TEXT,
                utm_content TEXT,
                total_price_aed REAL DEFAULT 0,
                status TEXT DEFAULT 'New',
                email_sent INTEGER DEFAULT 0,
                ip TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS leads (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT,
                service_id INTEGER,
                service_type TEXT,
                message TEXT,
                source_page TEXT,
                utm_source TEXT,
                utm_medium TEXT,
                utm_campaign TEXT,
                utm_term TEXT,
                utm_content TEXT,
                ip TEXT,
                user_agent TEXT,
                status TEXT DEFAULT 'New',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    } else {
        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(120) UNIQUE,
                setting_value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120),
                email VARCHAR(190) UNIQUE,
                password_hash VARCHAR(255),
                role VARCHAR(40) DEFAULT 'editor',
                is_active TINYINT(1) DEFAULT 1,
                last_login DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip VARCHAR(64),
                attempted_at DATETIME
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS activity_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                action VARCHAR(190),
                entity_type VARCHAR(80),
                entity_id INT DEFAULT 0,
                details TEXT,
                ip VARCHAR(64),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS tour_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                section_title VARCHAR(190),
                slug VARCHAR(150) UNIQUE NOT NULL,
                description TEXT,
                cta_text TEXT,
                seo_keywords TEXT,
                icon VARCHAR(60) DEFAULT 'compass',
                image VARCHAR(255),
                sort_order INT DEFAULT 0,
                seo_title VARCHAR(255),
                seo_desc TEXT,
                is_active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS tours (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                title VARCHAR(190) NOT NULL,
                slug VARCHAR(190) UNIQUE NOT NULL,
                badge VARCHAR(80),
                short_desc TEXT,
                long_desc LONGTEXT,
                duration VARCHAR(120),
                pickup_line VARCHAR(190),
                has_pickup TINYINT(1) DEFAULT 1,
                price_aed DECIMAL(10,2) NOT NULL DEFAULT 0,
                child_price_aed DECIMAL(10,2) DEFAULT 0,
                original_price_aed DECIMAL(10,2) NULL,
                age_policy VARCHAR(255),
                rating DECIMAL(3,1) DEFAULT 4.9,
                reviews_count INT DEFAULT 120,
                featured_image VARCHAR(255),
                gallery_json LONGTEXT,
                itinerary_json LONGTEXT,
                inclusions_json LONGTEXT,
                exclusions_json LONGTEXT,
                details_json LONGTEXT,
                pickup_details TEXT,
                availability VARCHAR(120) DEFAULT 'Daily',
                faq_json LONGTEXT,
                is_featured TINYINT(1) DEFAULT 0,
                is_combo TINYINT(1) DEFAULT 0,
                sort_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                seo_title VARCHAR(255),
                seo_desc TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS tour_bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                booking_ref VARCHAR(64),
                tour_id INT NULL,
                category_name VARCHAR(150),
                tour_title VARCHAR(255),
                customer_name VARCHAR(190) NOT NULL,
                customer_email VARCHAR(190) NOT NULL,
                country_code VARCHAR(16) DEFAULT '+971',
                customer_phone VARCHAR(64) NOT NULL,
                booking_date VARCHAR(40) NOT NULL,
                preferred_time VARCHAR(120),
                adults_count INT DEFAULT 1,
                children_count INT DEFAULT 0,
                children_ages VARCHAR(190),
                guests_count INT DEFAULT 1,
                pickup_location VARCHAR(255),
                nationality VARCHAR(120),
                special_requests TEXT,
                consent_given TINYINT(1) DEFAULT 1,
                source_page VARCHAR(255),
                utm_source VARCHAR(120),
                utm_medium VARCHAR(120),
                utm_campaign VARCHAR(120),
                utm_term VARCHAR(120),
                utm_content VARCHAR(120),
                total_price_aed DECIMAL(10,2) DEFAULT 0,
                status VARCHAR(40) DEFAULT 'New',
                email_sent TINYINT(1) DEFAULT 0,
                ip VARCHAR(64),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS leads (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(190) NOT NULL,
                phone VARCHAR(64) NOT NULL,
                email VARCHAR(190),
                service_id INT NULL,
                service_type VARCHAR(120),
                message TEXT,
                source_page VARCHAR(255),
                utm_source VARCHAR(120),
                utm_medium VARCHAR(120),
                utm_campaign VARCHAR(120),
                utm_term VARCHAR(120),
                utm_content VARCHAR(120),
                ip VARCHAR(64),
                user_agent VARCHAR(255),
                status VARCHAR(40) DEFAULT 'New',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    // Safely ensure any newly added columns exist on older tables
    $alterCols = [
        'tour_categories' => [
            'section_title' => $isSqlite ? 'TEXT' : 'VARCHAR(190)',
            'cta_text'      => 'TEXT',
            'seo_keywords'  => 'TEXT',
        ],
        'tours' => [
            'pickup_line'     => $isSqlite ? 'TEXT' : 'VARCHAR(190)',
            'has_pickup'      => $isSqlite ? 'INTEGER DEFAULT 1' : 'TINYINT(1) DEFAULT 1',
            'child_price_aed' => $isSqlite ? 'REAL DEFAULT 0' : 'DECIMAL(10,2) DEFAULT 0',
            'age_policy'      => $isSqlite ? 'TEXT' : 'VARCHAR(255)',
            'details_json'    => $isSqlite ? 'TEXT' : 'LONGTEXT',
        ],
        'tour_bookings' => [
            'booking_ref'    => $isSqlite ? 'TEXT' : 'VARCHAR(64)',
            'category_name'  => $isSqlite ? 'TEXT' : 'VARCHAR(150)',
            'country_code'   => $isSqlite ? "TEXT DEFAULT '+971'" : "VARCHAR(16) DEFAULT '+971'",
            'preferred_time' => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'adults_count'   => $isSqlite ? 'INTEGER DEFAULT 1' : 'INT DEFAULT 1',
            'children_count' => $isSqlite ? 'INTEGER DEFAULT 0' : 'INT DEFAULT 0',
            'children_ages'  => $isSqlite ? 'TEXT' : 'VARCHAR(190)',
            'nationality'    => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'consent_given'  => $isSqlite ? 'INTEGER DEFAULT 1' : 'TINYINT(1) DEFAULT 1',
            'source_page'    => $isSqlite ? 'TEXT' : 'VARCHAR(255)',
            'utm_source'     => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'utm_medium'     => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'utm_campaign'   => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'utm_term'       => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'utm_content'    => $isSqlite ? 'TEXT' : 'VARCHAR(120)',
            'email_sent'     => $isSqlite ? 'INTEGER DEFAULT 0' : 'TINYINT(1) DEFAULT 0',
        ],
    ];

    foreach ($alterCols as $tbl => $cols) {
        $existing = [];
        try {
            if ($isSqlite) {
                $rows = $db->query("PRAGMA table_info($tbl)")->fetchAll();
                foreach ($rows as $r) $existing[strtolower($r['name'])] = true;
            } else {
                $rows = $db->query("SHOW COLUMNS FROM `$tbl`")->fetchAll();
                foreach ($rows as $r) $existing[strtolower($r['Field'])] = true;
            }
            foreach ($cols as $col => $def) {
                if (empty($existing[strtolower($col)])) {
                    $db->exec("ALTER TABLE $tbl ADD COLUMN $col $def");
                }
            }
        } catch (Exception $e) {}
    }

    // Ensure default superadmin exists
    try {
        $cnt = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($cnt === 0) {
            $adminPass = password_hash('Admin@2024!', PASSWORD_DEFAULT);
            $db->prepare("INSERT INTO users (name, email, password_hash, role, is_active) VALUES ('Super Admin', 'admin@anchorpointbiz.org', ?, 'superadmin', 1)")
               ->execute([$adminPass]);
        }
    } catch (Exception $e) {}

    // Auto-seed the 2026 UAE Tourism redesign (5 categories, 35 activities, +971 52 537 6838)
    // once on BOTH SQLite (localhost) and MySQL (cPanel)
    try {
        $migCheck = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'pdf_redesign_v2026_migrated' LIMIT 1");
        $migCheck->execute();
        $migRow = $migCheck->fetch();
        $isMigrated = ($migRow && (string)$migRow['setting_value'] === '1');
        if (!$isMigrated) {
            require_once __DIR__ . '/seed_tourism_data.php';
            seedTourismRedesignData($db, $isSqlite);
            $db->exec("UPDATE tours SET is_active = 0 WHERE slug IN ('overnight-desert-safari','quad-bike-desert-safari','dhow-cruise-dinner-dubai-creek','family-dinner-cruise','corporate-and-group-cruise')");
        }
    } catch (Exception $e) {}

    // Enforce 2026 latest office details & overnight desert safari data
    try {
        $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")
           ->execute(['Office #08, Sheikh Rashid Rd, near Financial Centre Metro, Al Jafiliya, Dubai, UAE', 'office_address']);
        $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?")
           ->execute(['Daily: 9:00 AM – 6:00 PM (GST)', 'office_hours']);

        $newIncls = json_encode([
            '4x4 hotel pick-up (~03:00 PM) & next-morning drop-off (~08:30 AM)',
            'Full evening desert safari: red dune bashing, sunset photo stop & camel ride',
            'BBQ buffet dinner with grilled meats, salads, desserts & unlimited soft drinks',
            'Live evening entertainment: Tanoura dance, belly dance show & fire performance',
            'Overnight Bedouin tent stay with comfortable mattress, fresh linens, pillows & blankets',
            'Freshly prepared Arabian sunrise breakfast with eggs, warm bread, cheese, tea & Arabic Gahwa coffee',
            'Morning sunrise viewing across tranquil open desert dunes'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $db->prepare("UPDATE tours SET short_desc = ?, inclusions_json = ? WHERE slug = 'overnight-desert-safari'")
           ->execute(['Evening safari, overnight desert camp stay under the stars & sunrise breakfast', $newIncls]);

        // Auto-seed Business Setup Services & SEO Blog Articles
        require_once __DIR__ . '/seed_business_and_blog.php';
        seedBusinessAndBlog($db);

        // Complete category-wise Business Setup structure (5 categories, 31 services)
        if (is_file(__DIR__ . '/seed_business_setup_v2.php')) {
            require_once __DIR__ . '/seed_business_setup_v2.php';
            seedBusinessSetupV2($db);
        }

        // Extra attraction tickets & combo passes (27 more, grouped by type)
        if (is_file(__DIR__ . '/seed_attractions.php')) {
            require_once __DIR__ . '/seed_attractions.php';
            seedAttractionsV2($db);
        }
    } catch (Exception $e) {}
}
