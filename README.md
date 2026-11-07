# Anchor Point — Dubai Tours & Business Setup Website

Official web platform for **Anchor Point Business Setup & Travel** (`anchorpointbiz.org`), covering UAE tourism experiences (Desert Safaris, City Tours, Marina & Creek Dinner Cruises, Watersports, and Attraction Tickets) alongside Dubai Mainland & Free Zone Business Setup, Visa, and Corporate PRO services.

## Tech Stack

- **Backend:** PHP 8.1+ (PDO)
- **Database:** MySQL 8 / MariaDB (Production) with automatic SQLite fallback (Local Development)
- **Frontend:** Semantic HTML5, Vanilla CSS3, Vanilla JavaScript (ES6+), SVG
- **Server:** Apache 2.4+ (`mod_rewrite`, `mod_headers`, `mod_expires`, `mod_deflate`)

## Project Structure

```text
├── admin/               # Admin dashboard (tours, bookings, services, blog, settings, users)
├── api/                 # JSON & media endpoints (bookings, leads, CSRF, image uploads)
├── assets/              # Production stylesheets (CSS), scripts (JS), and brand SVG/hero assets
├── blog/                # SEO knowledge base & travel guides
├── includes/            # Core database layer, auth, mailers, helpers, partials & data seeders
├── logs/                # Runtime PHP error log directory (gitignored)
├── services/            # Business Setup & Corporate PRO catalog and service detail views
├── tours/               # Tour category landing pages and activity detail views
├── uploads/             # Optimized WebP/JPEG activity, attraction, and service media
├── .htaccess            # Apache URL routing, HTTPS redirect, CSP/security headers & caching
├── config.example.php   # Template for local/production secret overrides (`config.local.php`)
├── config.php           # Application configuration & bootstrap
├── database.sql         # Full MySQL InnoDB schema & seed dump for cPanel / phpMyAdmin
├── database.sqlite      # Pre-seeded portable SQLite database for zero-config local preview
├── index.php            # Main homepage
└── router.php           # Local built-in PHP server router
```

## Running Locally

Start PHP's built-in development server from the project root (uses SQLite automatically out of the box):

```bash
php -S localhost:8000 router.php
```

Open `http://localhost:8000` in your browser.

## Deploying to Production (cPanel / Apache + MySQL)

1. Upload or clone the repository files into `public_html/`.
2. Create a MySQL database and user in cPanel, then import `database.sql` via **phpMyAdmin**.
3. Copy `config.example.php` to `config.local.php` (or update `config.php`) with your MySQL `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS`.
4. Ensure `uploads/` and `logs/` are writable (`chmod 755`).
5. Sign in at `/admin/login` and immediately update the default administrator email and password under **Admin → Users**.
