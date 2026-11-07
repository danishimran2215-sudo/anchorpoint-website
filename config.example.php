<?php
/**
 * Local / Production Secret Overrides Template
 * Copy this file to `config.local.php` (ignored by Git) on your server.
 */

define('APP_ENV', 'production'); // 'development' | 'production'

// MySQL Database Credentials (cPanel / Production)
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_cpanel_dbname');
define('DB_USER', 'your_cpanel_dbuser');
define('DB_PASS', 'your_strong_dbpassword');

// Optional SMTP Overrides
// define('SMTP_HOST', 'mail.anchorpointbiz.org');
// define('SMTP_PORT', 587);
// define('SMTP_USER', 'info@anchorpointbiz.org');
// define('SMTP_PASS', 'your_smtp_password');
