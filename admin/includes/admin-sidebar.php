<?php
/**
 * Anchor Point Travel & Tourism — Streamlined Admin Sidebar Partial
 */
$adminPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard', PHP_URL_PATH) ?: '/admin/dashboard';
if (!function_exists('isNavActive')) {
    function isNavActive(string $segment): bool {
        global $adminPath;
        return str_contains($adminPath, $segment);
    }
}
?>
<aside class="admin-sidebar" aria-label="Admin Navigation">
  <div class="admin-brand">
    <img src="/assets/images/logo-icon.svg?v=5" alt="Anchor Point" width="28" height="28">
    <span>Anchor Tourism</span>
  </div>

  <nav class="admin-nav">
    <a href="/admin/dashboard" class="admin-nav-item<?= isNavActive('/admin/dashboard') ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
      Dashboard
    </a>

    <a href="/admin/tours" class="admin-nav-item<?= (isNavActive('/admin/tours') && !isNavActive('/admin/tours/bookings')) ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
      Activities &amp; Prices
    </a>

    <a href="/admin/services" class="admin-nav-item<?= isNavActive('/admin/services') ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
      Business Setup Services
    </a>

    <a href="/admin/blog" class="admin-nav-item<?= isNavActive('/admin/blog') ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
      Blog &amp; SEO Articles
    </a>

    <a href="/admin/tours/bookings" class="admin-nav-item<?= isNavActive('/admin/tours/bookings') ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
      Customer Enquiries
    </a>

    <a href="/admin/settings" class="admin-nav-item<?= isNavActive('/admin/settings') ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
      Site &amp; Email Settings
    </a>

    <a href="/admin/users" class="admin-nav-item<?= isNavActive('/admin/users') ? ' active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
      Admin Users
    </a>

    <div style="margin:1.5rem 1.5rem 0.5rem;font-size:0.75rem;text-transform:uppercase;color:var(--admin-muted);font-weight:700;">Live Website</div>
    <a href="/" target="_blank" class="admin-nav-item">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      View Live Site ↗
    </a>
  </nav>

  <div style="padding:1rem 1.5rem;border-top:1px solid var(--admin-border);font-size:0.8rem;color:var(--admin-muted);">
    Logged in as:<br>
    <strong style="color:var(--admin-text);"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></strong>
  </div>
</aside>
