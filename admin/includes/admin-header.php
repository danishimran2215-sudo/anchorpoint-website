<?php
/**
 * Anchor Point — Admin Header Partial
 */
$pageTitle = $pageTitle ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — Anchor Point Admin</title>
<link rel="icon" type="image/svg+xml" href="/assets/images/logo-icon.svg">
<link rel="stylesheet" href="/assets/css/admin.css">
<?= csrfMeta() ?>
</head>
<body>

<?php require_once __DIR__ . '/admin-sidebar.php'; ?>

<div class="admin-main">
  <!-- Topbar -->
  <header class="admin-topbar">
    <div style="display:flex;align-items:center;gap:1rem;">
      <button id="sidebar-toggle" class="theme-toggle" style="display:none;" aria-label="Toggle Navigation">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
      </button>
      <h1 style="font-size:1.25rem;font-weight:700;"><?= e($pageTitle) ?></h1>
    </div>

    <div class="admin-topbar-actions">
      <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Theme">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        Theme
      </button>

      <a href="/admin/logout" class="btn-logout" aria-label="Log Out">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        Sign Out
      </a>
    </div>
  </header>

  <!-- Page Content Container -->
  <div class="admin-content">
